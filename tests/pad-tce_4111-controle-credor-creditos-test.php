<?php

use Tester\Support\Test;

$entidades = [
    'pm' => 'PM',
    'cm' => 'CM',
    'fpsm' => 'FPSM',
];

$test_name = 'PAD / tce_4111: Controles credores / créditos';

$result = [];

foreach ($entidades as $entidade => $qualifier) {
    $lsql = [
        'Relatórios (valor manual)' => "select sum(valor) from manual_values_{$entidade} where item like 'Controles credores - créditos';",
    ];
    $rsql = [
        'Valor do PAD' => "select sum(valor) from diario where remessa = {$this->remessa} and entidade like '$entidade' and conta_contabil like '8.%' and tipo_lancamento like 'C' and data between '{$this->dataInicial}' and '$this->dataFinal';",
    ];

    $result[] = Test::ab($this, $test_name, $qualifier, $lsql, $rsql);
}




return $result;