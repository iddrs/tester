<?php

use Tester\Support\Test;

$test_name = 'Valor total do duodécimo a repassar: VPA x VPD';
$qualifier = 'PM e CM';

$lsql = [
    'Saldo atual da conta contábil 4.9.9.9.2.01 na CM' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '4.9.9.9.2.01.%' and entidade like 'cm'",
];
$rsql = [
    'Saldo atual da conta contábil 3.9.9.9.2.01 na PM' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '3.9.9.9.2.01.%' and entidade like 'pm'",
];

return [Test::ab($this, $test_name, $qualifier, $lsql, $rsql)];