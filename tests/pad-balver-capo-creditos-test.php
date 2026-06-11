<?php

use Tester\Support\Test;

$entidades = [
    'pm' => 'PM',
    'cm' => 'CM',
    'fpsm' => 'FPSM',
];

$test_name = 'PAD / BAL_VER: CAPO / créditos';

$result = [];

foreach ($entidades as $entidade => $qualifier) {
    $lsql = [
        'Relatórios (valor manual)' => "select sum(valor) from manual_values_{$entidade} where item like 'CAPO - créditos';",
    ];
    $rsql = [
        'Valor do PAD' => "select sum(movimento_credito) from balver where remessa = {$this->remessa} and entidade like '$entidade' and conta_contabil like '5.%';",
    ];

    $result[] = Test::ab($this, $test_name, $qualifier, $lsql, $rsql);
}




return $result;