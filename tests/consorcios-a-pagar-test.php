<?php

use Tester\Support\Test;

$entidades = [
    'pm' => 'PM',
    'cm' => 'CM',
    'fpsm' => 'FPSM',
];

$test_name = 'Consórcios a pagar';

$result = [];

foreach ($entidades as $entidade => $qualifier) {
    $lsql = [
        'Saldo final da conta contábil 2.1.8.9.1.14 (F)' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '2.1.8.9.1.14.%' and entidade like '$entidade' and indicador_superavit like 'F';",
    ];
    $rsql = [
        'Liquidado na despesa _._.71.70' => "select sum(liquidado) from moviemp where remessa = {$this->remessa} and entidade like '$entidade' and rubrica like '_._.71.70.%';",
        '(-) Pago na despesa _._.71.70' => "select sum(pago)*-1 from moviemp where remessa = {$this->remessa} and entidade like '$entidade' and rubrica like '_._.71.70.%';",
    ];

    $result[] = Test::ab($this, $test_name, $qualifier, $lsql, $rsql);
}




return $result;