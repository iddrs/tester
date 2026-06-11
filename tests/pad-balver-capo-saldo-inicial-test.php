<?php

use Tester\Support\Test;

$entidades = [
    'pm' => 'PM',
    'cm' => 'CM',
    'fpsm' => 'FPSM',
];

$test_name = 'PAD / BAL_VER: CAPO / saldo inicial';

$result = [];

foreach ($entidades as $entidade => $qualifier) {
    $lsql = [
        'Relatórios (valor manual)' => "select sum(valor) from manual_values_{$entidade} where item like 'CAPO - saldo inicial';",
    ];
    $rsql = [
        'Valor do PAD' => "select sum(saldo_anterior) from balver where remessa = {$this->remessa} and entidade like '$entidade' and conta_contabil like '5.%';",
    ];

    $result[] = Test::ab($this, $test_name, $qualifier, $lsql, $rsql);
}




return $result;