<?php

use Tester\Support\Test;

$entidades = [
    'pm' => 'PM',
    'cm' => 'CM',
    'fpsm' => 'FPSM',
];

$test_name = 'Suprimentos de fundos: ativo x controle';

$result = [];

foreach ($entidades as $entidade => $qualifier) {
    $lsql = [
        'Saldo final da conta contábil 1.1.3.1.1.02' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '1.1.3.1.1.02.%' and entidade like '$entidade';",
    ];
    $rsql = [
        'Saldo final da conta contábil 8.9.1.2.1.01' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '8.9.1.2.1.01.%' and entidade like '$entidade';",
        'Saldo final da conta contábil 8.9.1.2.1.05' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '8.9.1.2.1.05.%' and entidade like '$entidade';",
    ];

    $result[] = Test::ab($this, $test_name, $qualifier, $lsql, $rsql);
}




return $result;