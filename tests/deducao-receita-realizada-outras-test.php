<?php

use Tester\Support\Test;

$entidades = [
    'pm' => 'PM',
//    'cm' => 'CM',
    'fpsm' => 'FPSM',
];

$test_name = 'Outras deduções de receita realizada';

$result = [];

foreach ($entidades as $entidade => $qualifier) {
    $lsql = [
        'Saldo final da conta contábil 6.2.1.3.9' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '6.2.1.3.9.%' and entidade like '$entidade';",
    ];
    $rsql = [
        'Dedução realizada no código 102' => "select sum(receita_realizada) from balrec where remessa = {$this->remessa} and entidade like '$entidade' and deducao = 102;",
        'Dedução realizada no código 106' => "select sum(receita_realizada) from balrec where remessa = {$this->remessa} and entidade like '$entidade' and deducao = 106;",
        'Dedução realizada no código 107' => "select sum(receita_realizada) from balrec where remessa = {$this->remessa} and entidade like '$entidade' and deducao = 107;",
        'Dedução realizada no código 108' => "select sum(receita_realizada) from balrec where remessa = {$this->remessa} and entidade like '$entidade' and deducao = 108;",
        'Dedução realizada no código 109' => "select sum(receita_realizada) from balrec where remessa = {$this->remessa} and entidade like '$entidade' and deducao = 109;",
    ];

    $result[] = Test::ab($this, $test_name, $qualifier, $lsql, $rsql);
}




return $result;