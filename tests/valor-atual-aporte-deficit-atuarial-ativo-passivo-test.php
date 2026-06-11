<?php

use Tester\Support\Test;

$test_name = 'Valor atual dos aportes para cobertura do déficit atuarial: ativo x passivo';
$qualifier = 'PM e FPSM';

$lsql = [
    'Saldo atual da conta contábil 1.2.1.1.2.08.01 no FPSM' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '1.2.1.1.2.08.01.%' and entidade like 'fpsm'",
];
$rsql = [
    'Saldo atual da conta contábil 2.2.7.9.2.09 na PM' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '2.2.7.9.2.09.%' and entidade like 'pm'",
];

return [Test::ab($this, $test_name, $qualifier, $lsql, $rsql)];