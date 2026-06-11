<?php

use Tester\Support\Test;

$entidades = [
    'pm' => 'PM',
    'cm' => 'CM',
    'fpsm' => 'FPSM',
];

$test_name = 'Restos a pagar processados - inscrição no exercício';

$result = [];

foreach ($entidades as $entidade => $qualifier) {
    $lsql = [
        'Saldo final da conta contábil 5.3.2.7' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '5.3.2.7.%' and entidade like '$entidade';",
    ];
    $rsql = [
        'Saldo deve ser zero' => "select 0;",
    ];

    $result[] = Test::ab($this, $test_name, $qualifier, $lsql, $rsql);
}




return $result;