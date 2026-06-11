<?php

use Tester\Support\Test;

$test_name = 'Desincorporação do duodécimo repassado: VPA x VPD';
$qualifier = 'PM e CM';

$lsql = [
    'Saldo atual da conta contábil 4.6.4.1.2.03 na PM' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '4.6.4.1.2.03.%' and entidade like 'pm'",
];
$rsql = [
    'Saldo atual da conta contábil 3.6.5.1.2.03 na CM' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '3.6.5.1.2.03.%' and entidade like 'cm'",
];

return [Test::ab($this, $test_name, $qualifier, $lsql, $rsql)];