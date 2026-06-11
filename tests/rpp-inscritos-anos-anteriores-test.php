<?php

use Tester\Support\Test;

$entidades = [
    'pm' => 'PM',
    'cm' => 'CM',
    'fpsm' => 'FPSM',
];

$test_name = 'Restos a pagar processados inscritos em anos anteriores';

$result = [];

foreach ($entidades as $entidade => $qualifier) {
    $lsql = [
        'Saldo final da conta contábil 5.3.2.2' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '5.3.2.2.%' and entidade like '$entidade';",
    ];
    $rsql = [
        'Restos a pagar processados inscritos em anos anteriores (valor manual)' => "select sum(valor) from manual_values_{$entidade} where item like 'RPP inscritos anteriores';",
    ];

    $result[] = Test::ab($this, $test_name, $qualifier, $lsql, $rsql);
}




return $result;