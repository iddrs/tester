<?php

use Tester\Support\Test;

$entidades = [
    'pm' => 'PM',
    'cm' => 'CM',
    'fpsm' => 'FPSM',
];

$test_name = 'Precatórios de curto prazo: passivo x controle';

$result = [];

foreach ($entidades as $entidade => $qualifier) {
    $lsql = [
        'Saldo final da conta contábil 2.1.1.1.1.05' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '2.1.1.1.1.05.%' and entidade like '$entidade';",
        'Saldo final da conta contábil 2.1.3.1.1.06' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '2.1.3.1.1.06.%' and entidade like '$entidade';",
    ];
    $rsql = [
        'Saldo final da conta contábil 8.9.9.0.0.01' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '8.9.9.0.0.01.%' and entidade like '$entidade';",
    ];

    $result[] = Test::ab($this, $test_name, $qualifier, $lsql, $rsql);
}




return $result;