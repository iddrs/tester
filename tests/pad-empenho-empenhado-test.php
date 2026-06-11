<?php

use Tester\Support\Test;

$entidades = [
    'pm' => 'PM',
    'cm' => 'CM',
    'fpsm' => 'FPSM',
];

$test_name = 'PAD / EMPENHO: empenhado';

$result = [];

foreach ($entidades as $entidade => $qualifier) {
    $lsql = [
        'Relatórios (valor manual)' => "select sum(valor) from manual_values_{$entidade} where item like 'Empenhado';",
    ];
    $rsql = [
        'Valor do PAD' => "select sum(valor) from empenho where remessa = {$this->remessa} and entidade like '$entidade' and data between '{$this->dataInicial}' and '{$this->dataFinal}' and ano_empenho = {$this->exercicio};",
    ];

    $result[] = Test::ab($this, $test_name, $qualifier, $lsql, $rsql);
}




return $result;