<?php

use Tester\Support\Test;

$entidades = [
    'pm' => 'PM',
//    'cm' => 'CM',
    'fpsm' => 'FPSM',
];

$test_name = 'PAD / RECEITA: receita líquida prevista';

$result = [];

foreach ($entidades as $entidade => $qualifier) {
    $lsql = [
        'Relatórios (valor manual)' => "select sum(valor) from manual_values_{$entidade} where item like 'Receita líquida prevista';",
    ];
    $rsql = [
        'Valor do PAD' => "select sum(meta_total) from receita where remessa = {$this->remessa} and entidade like '$entidade';",
    ];

    $result[] = Test::ab($this, $test_name, $qualifier, $lsql, $rsql);
}




return $result;