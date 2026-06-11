<?php

use Tester\Support\Test;

$test_name = 'Ativo x Passivo intra-OFSS';
$qualifier = 'consolidado';

$lsql = [
    'Saldo atual do ativo intra-OFSS' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '1._._._.2.%'",
];
$rsql = [
    'Saldo atual do passivo intra-OFSS' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '2._._._.2.%'",
];

return [Test::ab($this, $test_name, $qualifier, $lsql, $rsql)];