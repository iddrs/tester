#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
TESTER — Interface Gráfica (tkinter)
====================================

Interface gráfica para o "TESTER -- Testador de dados." (test.php).

Executa o comando:

    php test.php <remessa> <db_file> <test_path> <result_dir>

mostrando a saída do console em tempo real dentro da janela.

A última configuração executada é salva em um arquivo JSON (tester_gui_config.json)
ao lado deste script e restaurada na próxima abertura.
"""

from __future__ import annotations

import json
import os
import queue
import shutil
import subprocess
import sys
import threading
import time
import tkinter as tk
from tkinter import filedialog, messagebox, scrolledtext, ttk

# --------------------------------------------------------------------------- #
# Configurações de caminho (a GUI vive ao lado do test.php)
# --------------------------------------------------------------------------- #
PROJECT_DIR = os.path.dirname(os.path.abspath(__file__))
TEST_PHP = os.path.join(PROJECT_DIR, "test.php")
CONFIG_FILE = os.path.join(PROJECT_DIR, "tester_gui_config.json")

# Fonte mono-espaçada para o console (com fallback por plataforma)
if sys.platform.startswith("win"):
    CONSOLE_FONT = ("Consolas", 10)
elif sys.platform == "darwin":
    CONSOLE_FONT = ("Menlo", 11)
else:
    CONSOLE_FONT = ("DejaVu Sans Mono", 10)


def load_config() -> dict:
    """Carrega a última configuração salva. Retorna {} se não existir/corrompida."""
    try:
        with open(CONFIG_FILE, "r", encoding="utf-8") as fh:
            data = json.load(fh)
        if not isinstance(data, dict):
            return {}
        return data
    except (OSError, ValueError):
        return {}


def save_config(data: dict) -> None:
    """Salva a configuração em JSON, ignorando erros de escrita."""
    try:
        with open(CONFIG_FILE, "w", encoding="utf-8") as fh:
            json.dump(data, fh, ensure_ascii=False, indent=2)
    except OSError:
        pass


class TesterGUI:
    """Janela principal da interface."""

    def __init__(self, root: tk.Tk) -> None:
        self.root = root
        root.title("TESTER — Testador de dados")
        root.minsize(720, 520)

        self._config = load_config()

        # Variáveis dos campos -------------------------------------------------
        self.var_remessa = tk.StringVar(value=self._config.get("remessa", ""))
        self.var_db_file = tk.StringVar(value=self._config.get("db_file", ""))
        self.var_test_path = tk.StringVar(value=self._config.get("test_path", ""))
        self.var_result_dir = tk.StringVar(value=self._config.get("result_dir", ""))

        # Estado de execução ----------------------------------------------------
        self._proc: subprocess.Popen | None = None
        self._queue: queue.Queue = queue.Queue()
        self._start_mono: float | None = None

        self._build_ui()

        # Restaura geometria da janela, se salva
        geometry = self._config.get("geometry")
        if geometry:
            try:
                root.geometry(geometry)
            except tk.TclError:
                pass

        # Atualiza o preview do comando e a status bar
        self._refresh_preview()
        self._set_status("Pronto.")

        # Fecha a janela de forma segura (salva config e encerra processo)
        root.protocol("WM_DELETE_WINDOW", self._on_close)

    # ----------------------------------------------------------------------- #
    # Construção da interface
    # ----------------------------------------------------------------------- #
    def _build_ui(self) -> None:
        pad = {"padx": 8, "pady": 4}

        # ---- Painel de parâmetros ------------------------------------------
        params = ttk.LabelFrame(self.root, text=" Parâmetros de execução ")
        params.pack(fill="x", padx=8, pady=(8, 4))

        grid = ttk.Frame(params)
        grid.pack(fill="x", padx=6, pady=6)
        grid.columnconfigure(1, weight=1)

        # Remessa (AAAAMM)
        ttk.Label(grid, text="Remessa (AAAAMM):").grid(row=0, column=0, sticky="w", **pad)
        ttk.Entry(grid, textvariable=self.var_remessa, width=12).grid(
            row=0, column=1, sticky="w", **pad
        )

        # Arquivo DuckDB
        ttk.Label(grid, text="Arquivo DuckDB (db_file):").grid(row=1, column=0, sticky="w", **pad)
        ttk.Entry(grid, textvariable=self.var_db_file).grid(row=1, column=1, sticky="ew", **pad)
        ttk.Button(grid, text="Procurar...", command=self._browse_db).grid(
            row=1, column=2, **pad
        )

        # Caminho dos testes (arquivo ou diretório)
        ttk.Label(grid, text="Testes (test_path):").grid(row=2, column=0, sticky="w", **pad)
        ttk.Entry(grid, textvariable=self.var_test_path).grid(row=2, column=1, sticky="ew", **pad)
        ttk.Button(grid, text="Arquivo...", command=self._browse_test_file).grid(
            row=2, column=2, padx=(8, 2), pady=4
        )
        ttk.Button(grid, text="Diretório...", command=self._browse_test_dir).grid(
            row=2, column=3, padx=(2, 8), pady=4
        )

        # Diretório de resultados
        ttk.Label(grid, text="Resultados (result_dir):").grid(row=3, column=0, sticky="w", **pad)
        ttk.Entry(grid, textvariable=self.var_result_dir).grid(row=3, column=1, sticky="ew", **pad)
        ttk.Button(grid, text="Procurar...", command=self._browse_result_dir).grid(
            row=3, column=2, **pad
        )

        # Preview do comando
        self.var_cmd_preview = tk.StringVar()
        ttk.Label(grid, text="Comando:").grid(row=4, column=0, sticky="nw", **pad)
        preview = ttk.Label(
            grid,
            textvariable=self.var_cmd_preview,
            font=CONSOLE_FONT,
            foreground="#444444",
            wraplength=720,
            justify="left",
        )
        preview.grid(row=4, column=1, columnspan=3, sticky="ew", **pad)

        # ---- Botões de ação -------------------------------------------------
        actions = ttk.Frame(self.root)
        actions.pack(fill="x", padx=8, pady=4)

        self.btn_run = ttk.Button(actions, text="▶ Executar", command=self._run)
        self.btn_run.pack(side="left", padx=(0, 6))

        self.btn_stop = ttk.Button(
            actions, text="■ Parar", command=self._stop, state="disabled"
        )
        self.btn_stop.pack(side="left", padx=(0, 6))

        ttk.Button(actions, text="Ajuda", command=self._show_help).pack(
            side="left", padx=(0, 6)
        )
        ttk.Button(actions, text="Limpar", command=self._clear_all).pack(side="left")

        # ---- Saída do console ------------------------------------------------
        console_frame = ttk.LabelFrame(self.root, text=" Saída do console ")
        console_frame.pack(fill="both", expand=True, padx=8, pady=(4, 4))

        self._console = scrolledtext.ScrolledText(
            console_frame,
            wrap="word",
            font=CONSOLE_FONT,
            state="disabled",
            background="#1e1e1e",
            foreground="#dcdcdc",
            insertbackground="#dcdcdc",
        )
        self._console.pack(fill="both", expand=True, padx=4, pady=4)

        # ---- Barra de status --------------------------------------------------
        status = ttk.Frame(self.root)
        status.pack(fill="x", padx=8, pady=(0, 8))

        self._status_var = tk.StringVar()
        self._elapsed_var = tk.StringVar(value="")
        ttk.Label(status, textvariable=self._status_var).pack(side="left")
        ttk.Label(status, textvariable=self._elapsed_var).pack(side="right")

        # Recalcula o preview do comando a cada alteração nos campos
        for var in (self.var_remessa, self.var_db_file, self.var_test_path, self.var_result_dir):
            var.trace_add("write", lambda *_: self._refresh_preview())

    # ----------------------------------------------------------------------- #
    # Navegação de arquivos
    # ----------------------------------------------------------------------- #
    def _browse_db(self) -> None:
        path = filedialog.askopenfilename(
            parent=self.root,
            title="Selecionar arquivo DuckDB",
            filetypes=[("Arquivos DuckDB", "*.duckdb *.ddb *.db"), ("Todos os arquivos", "*.*")],
        )
        if path:
            self.var_db_file.set(path)

    def _browse_test_file(self) -> None:
        path = filedialog.askopenfilename(
            parent=self.root,
            title="Selecionar arquivo de teste",
            filetypes=[("Arquivos PHP", "*.php"), ("Todos os arquivos", "*.*")],
        )
        if path:
            self.var_test_path.set(path)

    def _browse_test_dir(self) -> None:
        path = filedialog.askdirectory(
            parent=self.root, title="Selecionar diretório com arquivos de teste"
        )
        if path:
            self.var_test_path.set(path)

    def _browse_result_dir(self) -> None:
        path = filedialog.askdirectory(
            parent=self.root, title="Selecionar diretório de resultados"
        )
        if path:
            self.var_result_dir.set(path)

    # ----------------------------------------------------------------------- #
    # Preview do comando
    # ----------------------------------------------------------------------- #
    def _refresh_preview(self) -> None:
        args = self._command_args()
        self.var_cmd_preview.set("php test.php " + " ".join(args))

    # ----------------------------------------------------------------------- #
    # Validação e execução
    # ----------------------------------------------------------------------- #
    def _command_args(self) -> list[str]:
        return [
            self.var_remessa.get().strip(),
            self.var_db_file.get().strip(),
            self.var_test_path.get().strip(),
            self.var_result_dir.get().strip(),
        ]

    def _validate(self, args: list[str]) -> str | None:
        """Retorna uma mensagem de erro ou None se tudo estiver OK."""
        import re

        if not re.fullmatch(r"\d{6}", args[0]):
            return "Remessa deve estar no formato AAAAMM (6 dígitos).\nExemplo: 202607"
        if not args[1]:
            return "Informe o arquivo DuckDB (db_file)."
        if not os.path.isfile(args[1]):
            return f"Arquivo DuckDB não encontrado:\n{args[1]}"
        if not args[2]:
            return "Informe o caminho dos testes (test_path)."
        if not os.path.exists(args[2]):
            return f"Caminho dos testes não encontrado:\n{args[2]}"
        if not args[3]:
            return "Informe o diretório de resultados (result_dir)."
        return None

    def _run(self) -> None:
        if self._proc is not None:
            return

        args = self._command_args()
        error = self._validate(args)
        if error:
            messagebox.showerror("Parâmetros inválidos", error, parent=self.root)
            return

        php_bin = shutil.which("php")
        if not php_bin:
            messagebox.showerror(
                "PHP não encontrado",
                "Não foi possível localizar o executável 'php' no PATH.",
                parent=self.root,
            )
            return

        # A configuração executada (e o estado da janela) ficam salvos
        self._persist_config()

        self._console_clear()
        self._set_status("Executando...")

        command = [php_bin, TEST_PHP, *args]
        try:
            proc = subprocess.Popen(
                command,
                cwd=PROJECT_DIR,
                stdout=subprocess.PIPE,
                stderr=subprocess.STDOUT,
            )
        except OSError as exc:
            messagebox.showerror("Erro ao executar", str(exc), parent=self.root)
            self._set_status("Erro ao iniciar.")
            return

        self._proc = proc
        self._start_mono = time.monotonic()
        self._elapsed_var.set("")
        self._set_running_state(True)

        threading.Thread(
            target=self._reader, args=(proc, self._queue), daemon=True
        ).start()

        self.root.after(50, self._poll)

    def _reader(self, proc: subprocess.Popen, q: queue.Queue) -> None:
        """Lê a saída do processo em segundo plano e a enfileira."""
        assert proc.stdout is not None
        for raw in proc.stdout:
            q.put(("out", raw))
        proc.wait()
        q.put(("done", proc.returncode))

    def _poll(self) -> None:
        """Consome a fila de saída e atualiza a interface (roda na thread principal)."""
        if self._proc is None:
            return
        try:
            while True:
                kind, payload = self._queue.get_nowait()
                if kind == "out":
                    self._console_append(payload.decode("utf-8", "replace"))
                else:
                    self._on_finished(payload)
        except queue.Empty:
            pass

        if self._proc is not None:
            self._update_elapsed()
            self.root.after(50, self._poll)

    def _on_finished(self, returncode: int) -> None:
        self._proc = None
        self._set_running_state(False)

        elapsed = self._elapsed()
        if returncode == 0:
            self._set_status(f"Concluído com sucesso (código 0). Tempo: {elapsed}.")
        else:
            self._set_status(f"Encerrado com código {returncode}. Tempo: {elapsed}.")
        self._console_append(
            f"\n[processo encerrado — código {returncode}, tempo {elapsed}]\n"
        )

    # ----------------------------------------------------------------------- #
    # Ações auxiliares
    # ----------------------------------------------------------------------- #
    def _show_help(self) -> None:
        """Executa `php test.php` sem argumentos e mostra a ajuda no console."""
        if self._proc is not None:
            messagebox.showinfo(
                "Teste em execução",
                "Aguarde a conclusão do teste atual antes de exibir a ajuda.",
                parent=self.root,
            )
            return

        php_bin = shutil.which("php")
        if not php_bin:
            messagebox.showerror(
                "PHP não encontrado",
                "Não foi possível localizar o executável 'php' no PATH.",
                parent=self.root,
            )
            return

        self._console_clear()
        self._set_status("Mostrando ajuda.")
        try:
            result = subprocess.run(
                [php_bin, TEST_PHP],
                cwd=PROJECT_DIR,
                capture_output=True,
                timeout=30,
            )
        except (OSError, subprocess.TimeoutExpired) as exc:
            self._set_status("Erro ao exibir a ajuda.")
            self._console_append(str(exc) + "\n")
            return

        self._console_append(result.stdout.decode("utf-8", "replace"))
        if result.stderr:
            self._console_append(
                "\n[erros]\n" + result.stderr.decode("utf-8", "replace")
            )
        self._set_status("Pronto.")

    def _stop(self) -> None:
        if self._proc is None:
            return
        try:
            self._proc.terminate()
            self._set_status("Encerrando processo...")
            self.btn_stop.configure(state="disabled")
        except ProcessLookupError:
            pass

    def _clear_all(self) -> None:
        self._console_clear()
        if self._proc is None:
            self.var_remessa.set("")
            self.var_db_file.set("")
            self.var_test_path.set("")
            self.var_result_dir.set("")
            self._set_status("Pronto.")

    # ----------------------------------------------------------------------- #
    # Console
    # ----------------------------------------------------------------------- #
    def _console_append(self, text: str) -> None:
        self._console.configure(state="normal")
        self._console.insert(tk.END, text)
        self._console.configure(state="disabled")
        self._console.see(tk.END)

    def _console_clear(self) -> None:
        self._console.configure(state="normal")
        self._console.delete("1.0", tk.END)
        self._console.configure(state="disabled")

    # ----------------------------------------------------------------------- #
    # Aparência / status
    # ----------------------------------------------------------------------- #
    def _set_running_state(self, running: bool) -> None:
        state = "disabled" if running else "normal"
        self.btn_run.configure(state=state)
        stop_state = "normal" if running else "disabled"
        self.btn_stop.configure(state=stop_state)

    def _set_status(self, text: str) -> None:
        self._status_var.set(text)

    def _elapsed(self) -> str:
        if self._start_mono is None:
            return "0s"
        seconds = int(time.monotonic() - self._start_mono)
        if seconds < 60:
            return f"{seconds}s"
        minutes, seconds = divmod(seconds, 60)
        return f"{minutes}m {seconds}s"

    def _update_elapsed(self) -> None:
        self._elapsed_var.set(f"Tempo: {self._elapsed()}")

    # ----------------------------------------------------------------------- #
    # Persistência da configuração
    # ----------------------------------------------------------------------- #
    def _persist_config(self) -> None:
        self._config.update(
            {
                "remessa": self.var_remessa.get().strip(),
                "db_file": self.var_db_file.get().strip(),
                "test_path": self.var_test_path.get().strip(),
                "result_dir": self.var_result_dir.get().strip(),
                "geometry": self.root.geometry(),
            }
        )
        save_config(self._config)

    def _on_close(self) -> None:
        self._persist_config()
        if self._proc is not None:
            if messagebox.askyesno(
                "Teste em execução",
                "O teste ainda está em execução. Deseja encerrá-lo e sair?",
                parent=self.root,
            ):
                try:
                    self._proc.terminate()
                except ProcessLookupError:
                    pass
            else:
                return
        self.root.destroy()


def main() -> None:
    root = tk.Tk()
    TesterGUI(root)
    root.mainloop()


if __name__ == "__main__":
    main()