<?php

use Tester\Support\Test;

$entidades = [
    'pm' => 'PM',
    'cm' => 'CM',
    'fpsm' => 'FPSM',
];

$test_name = 'Cancelamento de dotações';

$result = [];

foreach ($entidades as $entidade => $qualifier) {
    $lsql = [
        'Saldo final da conta contábil 5.2.2.1.9.04' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '5.2.2.1.9.04.%' and entidade like '$entidade';",
    ];
    $rsql = [
        'Cancelamento de dotações' => "select sum(valor_reducao)*-1 from decreto where remessa = {$this->remessa} and entidade like '$entidade';",
    ];

    $result[] = Test::ab($this, $test_name, $qualifier, $lsql, $rsql);
}




return $result;