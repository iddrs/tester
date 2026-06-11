<?php

use Tester\Support\Test;

$test_name = 'Parcelamento da dívida com o RPPS: ativo e passivo de longo prazo';
$qualifier = 'PM e FPSM';

$lsql = [
    'Saldo atual da conta contábil 1.2.1.1.2.06.04.01 no FPSM' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '1.2.1.1.2.06.04.01.%' and entidade like 'fpsm'",
];
$rsql = [
    'Saldo atual da conta contábil 2.2.1.4.2.01 na PM' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '2.2.1.4.2.01.%' and entidade like 'pm'",
];

return [Test::ab($this, $test_name, $qualifier, $lsql, $rsql)];