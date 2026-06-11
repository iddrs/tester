<?php

use Tester\Support\Test;

$entidades = [
    'pm' => 'PM',
    'cm' => 'CM',
    'fpsm' => 'FPSM',
];

$test_name = 'CAPO = CEPO';

$result = [];

foreach ($entidades as $entidade => $qualifier) {
    $lsql = [
        'Saldo final da conta contábil 5.*' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '5.%' and entidade like '$entidade';",
    ];
    $rsql = [
        'Saldo final da conta contábil 6.*' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '6.%' and entidade like '$entidade';",
    ];

    $result[] = Test::ab($this, $test_name, $qualifier, $lsql, $rsql);
}




return $result;