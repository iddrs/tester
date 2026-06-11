<?php

use Tester\Support\Test;

$test_name = 'Contribuição dos ativos, aposentados e pensionistas a receber pelo RPPS';
$qualifier = 'consolidado';

$lsql = [
    'Saldo final da conta contábil 1.1.3.6.2.01.02 no FPSM' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '1.1.3.6.2.01.02.%' and entidade like 'fpsm'",
];
$rsql = [
    'Saldo final da conta contábil 2.1.8.8.2.01.01.01 (F)' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '2.1.8.8.2.01.01.01.%' and indicador_superavit like 'F'",
];

return [Test::ab($this, $test_name, $qualifier, $lsql, $rsql)];