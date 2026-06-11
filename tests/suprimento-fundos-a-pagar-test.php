<?php

use Tester\Support\Test;

$entidades = [
    'pm' => 'PM',
    'cm' => 'CM',
    'fpsm' => 'FPSM',
];

$test_name = 'Suprimento de fundos a pagar';

$result = [];

foreach ($entidades as $entidade => $qualifier) {
    $lsql = [
        'Saldo final da conta contábil 2.1.8.9.1.03 (F)' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '2.1.8.9.1.03.%' and entidade like '$entidade' and indicador_superavit like 'F';",
    ];
    $rsql = [
        'Liquidado na despesa 3.3.90.__.96' => "select sum(liquidado) from moviemp where remessa = {$this->remessa} and entidade like '$entidade' and rubrica like '3.3.90.__.96.%';",
        '(-) Pago na despesa 3.3.90.__.96' => "select sum(pago)*-1 from moviemp where remessa = {$this->remessa} and entidade like '$entidade' and rubrica like '3.3.90.__.96.%';",
    ];

    $result[] = Test::ab($this, $test_name, $qualifier, $lsql, $rsql);
}




return $result;