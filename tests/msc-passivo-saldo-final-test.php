<?php

use Tester\Support\Test;

$test_name = 'MSC: passivo / saldo final';
$qualifier = 'consolidado';

$lsql = [
    'Relatórios (valor manual)' => "select sum(valor) from manual_values_total where item like 'Passivo - saldo final';",
];
$rsql = [
    'Valor da MSC' => "select sum(saldo_atual) from msc where remessa = {$this->remessa} and conta_contabil like '2.%';",
];

return [Test::ab($this, $test_name, $qualifier, $lsql, $rsql)];