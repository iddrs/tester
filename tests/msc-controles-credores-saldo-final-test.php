<?php

use Tester\Support\Test;

$test_name = 'MSC: Controles credores / saldo final';
$qualifier = 'consolidado';

$lsql = [
    'Relatórios (valor manual)' => "select sum(valor) from manual_values_total where item like 'Controles credores - saldo final';",
];
$rsql = [
    'Valor da MSC' => "select sum(saldo_atual) from msc where remessa = {$this->remessa} and conta_contabil like '8.%';",
];

return [Test::ab($this, $test_name, $qualifier, $lsql, $rsql)];