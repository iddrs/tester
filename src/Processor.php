<?php

namespace Tester;

use Saturio\DuckDB\DuckDB;
use Saturio\DuckDB\Result\ResultSet;
use Tester\Support\Output;
use Twig\Environment;
use Twig\Extra\Intl\IntlExtension;
use Twig\Loader\FilesystemLoader;

class Processor
{
    private readonly Environment $twig;

    public readonly string $dataInicial;
    public readonly string $dataMesInicial;
    public readonly string $dataFinal;
    public readonly int $exercicio;
    public function __construct(
        public readonly int $remessa,
        public readonly DuckDB $db,
        public readonly array $testFiles,
        public readonly string $resultDir
    )
    {
        $di = date_create_from_format('Ymd', "{$remessa}01");
        $this->dataMesInicial = $di->format('Y-m-d');
        $this->dataInicial = date_create_from_format('Y-m-d', "{$di->format('Y')}-01-01")->format('Y-m-d');
        $this->dataFinal = date_create_from_format('Y-m-d', "{$di->format('Y-m')}-{$di->format('t')}")->format('Y-m-d');
        $this->exercicio = intval($di->format('Y'));

        $this->twig = new Environment(
            new FilesystemLoader(TEMPLATE_DIR)
        );
        $this->twig->addExtension(new IntlExtension());
    }

    public function render(string $templateName, array $context = []): string
    {
        $templateFile = "$templateName.html.twig";
        return $this->twig->render($templateFile, $context);
    }

    private function clearOldResults(string $path): void
    {
        $old = glob("{$path}/*-result.html");
        foreach ($old as $file) {
            unlink($file);
        }
    }

    private function executeSql(string $dir)
    {
        Output::println("Executando scripts de $dir");
        $sqls = glob("$dir/*.sql");
        foreach ($sqls as $sql) {
            Output::println($sql);
            $sql = str_replace('{{manual_values_file}}', MANUAL_VALUES_DIR."/{$this->remessa}.xlsx", file_get_contents($sql));
            $this->db->query($sql);
        }
    }

    public function run(): void
    {
        $fails = 0;
        $pass = 0;
        $this->executeSql(PRE_TEST_DIR);

        $output_dir = realpath($this->resultDir);
        $this->clearOldResults($output_dir);
        foreach ($this->testFiles as $tfile) {
            $basename = basename($tfile, '.php');
            Output::println($basename);
            $result = require $tfile;
            foreach ($result as $r) {
                if ($r->success) {
                    $success = 'pass';
                    $pass++;
                }else{
                    $success = 'fail';
                    $fails++;
                }
                $qualifier = $r->qualifier;
                $output_name = "{$output_dir}/{$success}-{$basename}-{$qualifier}-result.html";
                file_put_contents($output_name, $r->html);
            }
        }

        $this->executeSql(POST_TEST_DIR);

        Output::println();
        Output::println();
        Output::println('=============================================================================');
        $total = $pass + $fails;
        Output::println(sprintf("Testes que passaram\t\t%d\t/\t%.2f%%", $pass, $pass / $total * 100));
        Output::println(sprintf("Testes que falharam\t\t%d\t/\t%.2f%%", $fails, $fails / $total * 100));
        Output::println(sprintf("Total de testes\t\t\t%d", $total));
    }
}