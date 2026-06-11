<?php

use Tester\Support\Test;

$entidades = [
    'pm' => 'PM',
    'cm' => 'CM',
    'fpsm' => 'FPSM',
];

$test_name = 'Dotação transferida';

$result = [];

foreach ($entidades as $entidade => $qualifier) {
    $lsql = [
        'Saldo final da conta contábil 5.2.2.1.3.06' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '5.2.2.1.3.06.%' and entidade like '$entidade';",
    ];
    $rsql = [
        'Crédito reaberto' => "select sum(valor_reabertura) from decreto where remessa = {$this->remessa} and entidade like '$entidade' and data_reabertura is not null;",
    ];

    $result[] = Test::ab($this, $test_name, $qualifier, $lsql, $rsql);
}




return $result;