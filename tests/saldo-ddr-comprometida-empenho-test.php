<?php

use Tester\Support\Test;

$entidades = [
    'pm' => 'PM',
    'cm' => 'CM',
    'fpsm' => 'FPSM',
];

$test_name = 'DDR comprometida por empenho';

$result = [];

foreach ($entidades as $entidade => $qualifier) {
    $lsql = [
        'Saldo final da conta contábil 8.2.1.1.2' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '8.2.1.1.2.%' and entidade like '$entidade';",
    ];
    $rsql = [
        'Saldo final da conta contábil 6.2.2.1.3.01' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '6.2.2.1.3.01.%' and entidade like '$entidade';",
        'Saldo final da conta contábil 6.3.1.1' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '6.3.1.1.%' and entidade like '$entidade';",
    ];

    $result[] = Test::ab($this, $test_name, $qualifier, $lsql, $rsql);
}




return $result;