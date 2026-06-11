<?php

use Tester\Support\Test;

$entidades = [
    'pm' => 'PM',
//    'cm' => 'CM',
//    'fpsm' => 'FPSM',
];

$test_name = 'Ativo de rendimentos do legislativo';

$result = [];

foreach ($entidades as $entidade => $qualifier) {
    $lsql = [
        'Saldo final da conta contábil 1.1.9.2.1.01 (F)' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '1.1.9.2.1.01.%' and balver.indicador_superavit like 'F' and entidade like '$entidade';",
    ];
    $rsql = [
        'Saldo final deve ser zero' => "select 0.0",
    ];

    $result[] = Test::ab($this, $test_name, $qualifier, $lsql, $rsql);
}




return $result;