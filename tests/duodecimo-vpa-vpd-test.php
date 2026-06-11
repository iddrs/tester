<?php

use Tester\Support\Test;

$test_name = 'Duodécimo repassado: VPA x VPD';
$qualifier = 'PM e CM';

$lsql = [
    'Saldo atual da conta contábil 4.5.1.1.2.02 na CM' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '4.5.1.1.2.02.%' and entidade like 'cm'",
];
$rsql = [
    'Saldo atual da conta contábil 3.5.1.1.2.02 na PM' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '3.5.1.1.2.02.%' and entidade like 'pm'",
];

return [Test::ab($this, $test_name, $qualifier, $lsql, $rsql)];