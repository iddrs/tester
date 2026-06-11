<?php

use Tester\Support\Test;

$entidades = [
    'pm' => 'PM',
    'cm' => 'CM',
    'fpsm' => 'FPSM',
];

$test_name = 'Contribuição patronal ao RPPS a pagar';

$result = [];

foreach ($entidades as $entidade => $qualifier) {
    $lsql = [
        'Saldo final da conta contábil 2.1.1.4.2.01 (F)' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '2.1.1.4.2.01.%' and entidade like '$entidade' and indicador_superavit like 'F';",
    ];
    $rsql = [
        'Liquidado na despesa 3.1.91.13' => "select sum(valor) from liquidacao where remessa = {$this->remessa} and entidade like '$entidade' and data between '{$this->dataInicial}' and '{$this->dataFinal}' and rubrica like '3.1.91.13.%';",
        '(-) Pago na despesa 3.1.91.13' => "select sum(valor)*-1 from pagamento where remessa = {$this->remessa} and entidade like '$entidade' and data between '{$this->dataInicial}' and '{$this->dataFinal}' and rubrica like '3.1.91.13.%';",
    ];

    $result[] = Test::ab($this, $test_name, $qualifier, $lsql, $rsql);
}




return $result;