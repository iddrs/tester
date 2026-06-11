<?php

use Tester\Support\Test;

$test_name = 'Parcelamento da dívida com o RPPS: ativo e passivo de curto prazo';
$qualifier = 'PM e FPSM';

$lsql = [
    'Saldo atual da conta contábil 1.1.2.1.2.71.01 no FPSM' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '1.1.2.1.2.71.01.%' and entidade like 'fpsm'",
];
$rsql = [
    'Saldo atual da conta contábil 2.1.1.4.2.02.01.01 na PM' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '2.1.1.4.2.02.01.01.%' and entidade like 'pm'",
];

return [Test::ab($this, $test_name, $qualifier, $lsql, $rsql)];