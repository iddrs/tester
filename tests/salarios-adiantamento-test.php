<?php

use Tester\Support\Test;

$entidades = [
    'pm' => 'PM',
    'cm' => 'CM',
    'fpsm' => 'FPSM',
];

$test_name = 'Salários e ordenados - adiantamento';

$result = [];

foreach ($entidades as $entidade => $qualifier) {
    $lsql = [
        'Saldo final da conta contábil 1.1.3.1.1.01.01 (F)' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '1.1.3.1.1.01.01.%' and balver.indicador_superavit like 'F' and entidade like '$entidade';",
    ];
    $rsql = [
        'Saldo final deve ser zero' => "select 0.0",
    ];

    $result[] = Test::ab($this, $test_name, $qualifier, $lsql, $rsql);
}




return $result;