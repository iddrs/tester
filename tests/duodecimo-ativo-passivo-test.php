<?php

use Tester\Support\Test;

$test_name = 'Duodécimo: ativo x passivo';
$qualifier = 'PM e CM';

$lsql = [
    'Saldo atual da conta contábil 1.1.3.8.2.99.01 na CM' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '1.1.3.8.2.99.01.%' and entidade like 'cm'",
];
$rsql = [
    'Saldo atual da conta contábil 2.1.8.9.2.98.01 na PM' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '2.1.8.9.2.98.01.%' and entidade like 'pm'",
];

return [Test::ab($this, $test_name, $qualifier, $lsql, $rsql)];