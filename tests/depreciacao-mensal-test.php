<?php

use Tester\Support\Test;

$entidades = [
    'pm' => 'PM',
    'cm' => 'CM',
    'fpsm' => 'FPSM',
];

$test_name = 'Valor depreciado mensal';

$result = [];

foreach ($entidades as $entidade => $qualifier) {
    $lsql = [
        'Créditos da conta contábil 1.2.3.8' => "select sum(valor) from diario where remessa = {$this->remessa} and conta_contabil like '1.2.3.8.%' and entidade like '$entidade' and data between '{$this->dataMesInicial}' and '{$this->dataFinal}';",
    ];
    $rsql = [
        'Depreciação - débito (valor manual)' => "select sum(valor) from manual_values_{$entidade} where item like 'Depreciação - débito';",
    ];

    $result[] = Test::ab($this, $test_name, $qualifier, $lsql, $rsql);
}




return $result;