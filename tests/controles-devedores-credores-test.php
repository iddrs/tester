<?php

use Tester\Support\Test;

$entidades = [
    'pm' => 'PM',
    'cm' => 'CM',
    'fpsm' => 'FPSM',
];

$test_name = 'Controles Devedores = Controles Credores';

$result = [];

foreach ($entidades as $entidade => $qualifier) {
    $lsql = [
        'Saldo final da conta contábil 7.*' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '7.%' and entidade like '$entidade';",
    ];
    $rsql = [
        'Saldo final da conta contábil 8.*' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '8.%' and entidade like '$entidade';",
    ];

    $result[] = Test::ab($this, $test_name, $qualifier, $lsql, $rsql);
}




return $result;