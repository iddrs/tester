<?php

use Tester\Support\Test;

$entidades = [
    'pm' => 'PM',
//    'cm' => 'CM',
    'fpsm' => 'FPSM',
];

$test_name = 'Previsão inicial de outras deduções';

$result = [];

foreach ($entidades as $entidade => $qualifier) {
    $lsql = [
        'Saldo final da conta contábil 5.2.1.1.2.99' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '5.2.1.1.2.99.%' and entidade like '$entidade';",
    ];
    $rsql = [
        'Previsão inicial com dedução 102' => "select sum(receita_orcada) from balrec where remessa = {$this->remessa} and entidade like '$entidade' and deducao = 102;",
        'Previsão inicial com dedução 106' => "select sum(receita_orcada) from balrec where remessa = {$this->remessa} and entidade like '$entidade' and deducao = 106;",
        'Previsão inicial com dedução 107' => "select sum(receita_orcada) from balrec where remessa = {$this->remessa} and entidade like '$entidade' and deducao = 107;",
        'Previsão inicial com dedução 108' => "select sum(receita_orcada) from balrec where remessa = {$this->remessa} and entidade like '$entidade' and deducao = 108;",
        'Previsão inicial com dedução 109' => "select sum(receita_orcada) from balrec where remessa = {$this->remessa} and entidade like '$entidade' and deducao = 109;",
    ];

    $result[] = Test::ab($this, $test_name, $qualifier, $lsql, $rsql);
}




return $result;