<?php

use Tester\Support\Test;

$entidades = [
    'pm' => 'PM',
    'cm' => 'CM',
    'fpsm' => 'FPSM',
];

$test_name = 'Saldo final do estoque';

$result = [];

foreach ($entidades as $entidade => $qualifier) {
    $lsql = [
        'Saldo final da conta contábil 1.1.5.6.1.99' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '1.1.5.6.1.99.%' and entidade like '$entidade';",
    ];
    $rsql = [
        'Estoque - saldo final (valor manual)' => "select sum(valor) from manual_values_{$entidade} where item like 'Estoque - saldo final';",
    ];

    $result[] = Test::ab($this, $test_name, $qualifier, $lsql, $rsql);
}




return $result;