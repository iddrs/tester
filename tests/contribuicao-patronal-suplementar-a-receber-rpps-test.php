<?php

use Tester\Support\Test;

$test_name = 'Contribuição patronal suplementar a receber pelo RPPS';
$qualifier = 'consolidado';

$lsql = [
    'Saldo final da conta contábil 1.1.3.6.2.05 no FPSM' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '1.1.3.6.2.05.%' and entidade like 'fpsm'",
];
$rsql = [
    'Liquidado na despesa 3.1.91.13.21' => "select sum(valor) from liquidacao where remessa = {$this->remessa} and rubrica like '3.1.91.13.21.%' and data between '{$this->dataInicial}' and '$this->dataFinal'",
    '(-) Pago na despesa 3.1.91.13.21' => "select sum(valor)*-1 from pagamento where remessa = {$this->remessa} and rubrica like '3.1.91.13.21.%' and data between '{$this->dataInicial}' and '$this->dataFinal'",
];

return [Test::ab($this, $test_name, $qualifier, $lsql, $rsql)];