<?php

use Tester\Support\Test;

$entidades = [
    'pm' => 'PM',
    'cm' => 'CM',
    'fpsm' => 'FPSM',
];

$test_name = 'PAD / BAL_VER: ativo / saldo final';

$result = [];

foreach ($entidades as $entidade => $qualifier) {
    $lsql = [
        'Relatórios (valor manual)' => "select sum(valor) from manual_values_{$entidade} where item like 'Ativo - saldo final';",
    ];
    $rsql = [
        'Valor do PAD' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and entidade like '$entidade' and conta_contabil like '1.%';",
    ];

    $result[] = Test::ab($this, $test_name, $qualifier, $lsql, $rsql);
}




return $result;