<?php

use Tester\Support\Test;

$test_name = 'VPD x VPA intra-OFSS';
$qualifier = 'consolidado';

$lsql = [
    'Saldo atual das VPDs intra-OFSS' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '3._._._.2.%'",
];
$rsql = [
    'Saldo atual das VPAs intra-OFSS' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '4._._._.2.%'",
];

return [Test::ab($this, $test_name, $qualifier, $lsql, $rsql)];