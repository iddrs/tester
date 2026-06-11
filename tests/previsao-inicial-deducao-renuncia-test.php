<?php

use Tester\Support\Test;

$entidades = [
    'pm' => 'PM',
//    'cm' => 'CM',
    'fpsm' => 'FPSM',
];

$test_name = 'Previsão inicial da dedução por renúncia';

$result = [];

foreach ($entidades as $entidade => $qualifier) {
    $lsql = [
        'Saldo final da conta contábil 5.2.1.1.2.02' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '5.2.1.1.2.02.%' and entidade like '$entidade';",
    ];
    $rsql = [
        'Previsão inicial com dedução 101' => "select sum(receita_orcada) from balrec where remessa = {$this->remessa} and entidade like '$entidade' and deducao = 101;",
        'Previsão inicial com dedução 103' => "select sum(receita_orcada) from balrec where remessa = {$this->remessa} and entidade like '$entidade' and deducao = 103;",
    ];

    $result[] = Test::ab($this, $test_name, $qualifier, $lsql, $rsql);
}




return $result;