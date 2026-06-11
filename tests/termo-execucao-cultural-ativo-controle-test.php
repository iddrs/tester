<?php

use Tester\Support\Test;

$entidades = [
    'pm' => 'PM',
    'cm' => 'CM',
    'fpsm' => 'FPSM',
];

$test_name = 'Termo de execução cultural: ativo x controle';

$result = [];

foreach ($entidades as $entidade => $qualifier) {
    $lsql = [
        'Saldo final da conta contábil 1.1.9.8.1.16' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '1.1.9.8.1.16.%' and entidade like '$entidade';",
    ];
    $rsql = [
        'Saldo final da conta contábil 8.1.2.2.1.99.02.01' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '8.1.2.2.1.99.02.01.%' and entidade like '$entidade';",
        'Saldo final da conta contábil 8.1.2.2.1.99.02.02' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '8.1.2.2.1.99.02.02.%' and entidade like '$entidade';",
    ];

    $result[] = Test::ab($this, $test_name, $qualifier, $lsql, $rsql);
}




return $result;