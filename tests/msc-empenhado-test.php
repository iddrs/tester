<?php

use Tester\Support\Test;

$test_name = 'MSC: empenhado';
$qualifier = 'consolidado';

$lsql = [
    'Relatórios (valor manual)' => "select sum(valor) from manual_values_total where item like 'Empenhado';",
];
$rsql = [
    'Valor da MSC' => "select sum(saldo_atual) from msc where remessa = {$this->remessa} and conta_contabil like '5.2.2.9.2.%';",
];

return [Test::ab($this, $test_name, $qualifier, $lsql, $rsql)];