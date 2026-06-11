<?php

use Tester\Support\Test;

$entidades = [
    'pm' => 'PM',
    'cm' => 'CM',
    'fpsm' => 'FPSM',
];

$test_name = 'PASEP a pagar';

$result = [];

foreach ($entidades as $entidade => $qualifier) {
    $lsql = [
        'Saldo final da conta contábil 2.1.4.1.3.11 (F)' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '2.1.4.1.3.11.%' and entidade like '$entidade' and indicador_superavit like 'F';",
    ];
    $rsql = [
        'Liquidado na despesa 3.3.90.47.12' => "select sum(liquidado) from moviemp where remessa = {$this->remessa} and entidade like '$entidade' and rubrica like '3.3.90.47.12.%';",
        '(-) Pago na despesa 3.3.90.47.12' => "select sum(pago)*-1 from moviemp where remessa = {$this->remessa} and entidade like '$entidade' and rubrica like '3.3.90.47.12.%';",
    ];

    $result[] = Test::ab($this, $test_name, $qualifier, $lsql, $rsql);
}




return $result;