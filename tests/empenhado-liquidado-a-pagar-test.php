<?php

use Tester\Support\Test;

$entidades = [
    'pm' => 'PM',
    'cm' => 'CM',
    'fpsm' => 'FPSM',
];

$test_name = 'Empenhado, liquidado a pagar';

$result = [];

foreach ($entidades as $entidade => $qualifier) {
    $lsql = [
        'Saldo final da conta contábil 6.2.2.9.2.01.03' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '6.2.2.9.2.01.03.%' and entidade like '$entidade';",
    ];
    $rsql = [
        'Liquidado a pagar do exercício' => "select sum(liquidado_a_pagar) from baldesp where remessa = {$this->remessa} and entidade like '$entidade';",
    ];

    $result[] = Test::ab($this, $test_name, $qualifier, $lsql, $rsql);
}




return $result;