<?php

use Tester\Support\Test;

$entidades = [
    'pm' => 'PM',
    'cm' => 'CM',
    'fpsm' => 'FPSM',
];

$test_name = 'PAD / DECRETO: excesso de arrecadação';

$result = [];

foreach ($entidades as $entidade => $qualifier) {
    $lsql = [
        'Relatórios (valor manual)' => "select sum(valor) from manual_values_{$entidade} where item like 'Excesso de arrecadação';",
    ];
    $rsql = [
        'Valor do PAD' => "select sum(valor_credito) from decreto where remessa = {$this->remessa} and entidade like '$entidade' and origem_recurso = 2;",
    ];

    $result[] = Test::ab($this, $test_name, $qualifier, $lsql, $rsql);
}




return $result;