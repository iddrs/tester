<?php

use Saturio\DuckDB\DB\Configuration;
use Saturio\DuckDB\DuckDB;
use Tester\Processor;
use Tester\Support\Output;

$start_time = new DateTimeImmutable();

// Carrega as bibliotecas necessárias
require 'vendor/autoload.php';

// Configura o ambiente
define('TEMPLATE_DIR', realpath('./templates/'));
define('PRE_TEST_DIR', realpath('./sql/pre-test'));
define('POST_TEST_DIR', realpath('./sql/post-test'));
define('MANUAL_VALUES_DIR', realpath('./manual_values/'));

// Imprime cabeçalho
Output::println('==============================================================');
Output::println('TESTER -- Testador de dados.');
Output::println('Desenvolvido por Everton da Rosa <everton3x@gmail.com>');
Output::println('==============================================================');
Output::println();

// Verifica se recebeu argumentos obrigatórios da linha de comando
$argRemessa = $argv[1] ?? null;
$argDbFile = $argv[2] ?? null;
$argTestPath = $argv[3] ?? null;
$argResultDir = $argv[4] ?? null;

// Se faltarem argumentos, mostra a ajuda e sai
if(is_null($argRemessa) || is_null($argDbFile) || is_null($argTestPath) || is_null($argResultDir)) {
    Output::println('Como utilizar');
    Output::println();
    Output::println('test remessa db_file test_path result_dir');
    Output::println();
    Output::println('Parâmetros');
    Output::println("\tremessa\t\tRemessa a ser testada no formato AAAAMM.");
    Output::println("\tdb_file\t\tArquivo DuckDB com os dados para testar.");
    Output::println("\ttest_path\tDiretório com os arquivos de teste ou arquivo de teste único.");
    Output::println("\tresult_dir\tDiretório onde serão salvos os resultados dos testes.");
    exit();
}

// Testa se $argDbFile existe
if(!file_exists($argDbFile)) {
    Output::println("$argDbFile não foi encontrado.");
    exit();
}

// Testa se $argTextPath existe
if(!file_exists($argTestPath)) {
    Output::println("$argTestPath não foi encontrado.");
    exit();
}

// Cria o diretório de teste se ele não existir
if(!file_exists($argResultDir)) {
    Output::println("Criando o diretório $argResultDir");
    mkdir($argResultDir, 0777, true);
}

// Testa se $argResultDir é um diretório
if(!is_dir($argResultDir)) {
    Output::println("$argResultDir não é um diretório.");
    exit();
}

// Busca os arquivos para testar
$testFiles = [];
if(is_file($argTestPath)) {
    $testFiles[] = $argTestPath;
}else{
    $sourceDirPattern = "$argTestPath/*-test.php";
//    $dirIterator = new RegexIterator(
//        new RecursiveIteratorIterator(
//            new RecursiveDirectoryIterator($sourceDirPattern, RecursiveDirectoryIterator::SKIP_DOTS)), '/^.+\.txt$/i', RecursiveRegexIterator::GET_MATCH);
//    foreach ($dirIterator as $item) {
//        $testFiles[] = realpath($item[0]);
//    }
    $testFiles = glob($sourceDirPattern);
}
//print_r($testFiles);

// Conectando com o arquivo DuckDB
$duckConfig = new Configuration();
$duckConfig->set('access_mode', 'READ_WRITE');
$duck = DuckDB::create($argDbFile, config: $duckConfig);


$processor = new Processor($argRemessa, $duck, $testFiles, $argResultDir);
$processor->run();

$end_time = new DateTimeImmutable();

$duration = $start_time->diff($end_time);

Output::println("Tempo decorrido de processamento: {$duration->format('%H horas, %I minutos e %S segundos')}");