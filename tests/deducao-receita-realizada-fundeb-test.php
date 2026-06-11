<?php

use Tester\Support\Test;

$entidades = [
    'pm' => 'PM',
//    'cm' => 'CM',
    'fpsm' => 'FPSM',
];

$test_name = 'Dedução para o Fundeb realizada';

$result = [];

foreach ($entidades as $entidade => $qualifier) {
    $lsql = [
        'Saldo final da conta contábil 6.2.1.3.1.01' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '6.2.1.3.1.01.%' and entidade like '$entidade';",
    ];
    $rsql = [
        'Dedução realizada no código 105' => "select sum(receita_realizada) from balrec where remessa = {$this->remessa} and entidade like '$entidade' and deducao = 105;",
    ];

    $result[] = Test::ab($this, $test_name, $qualifier, $lsql, $rsql);
}




return $result;