<?php

use Tester\Support\Test;

$test_name = 'MSC: liquidado';
$qualifier = 'consolidado';

$lsql = [
    'Relatórios (valor manual)' => "select sum(valor) from manual_values_total where item like 'Liquidado';",
];
$rsql = [
    'Valor da MSC (6.2.2.1.3.03)' => "select sum(saldo_atual) from msc where remessa = {$this->remessa} and conta_contabil like '6.2.2.1.3.03.%';",
    'Valor da MSC (6.2.2.1.3.04)' => "select sum(saldo_atual) from msc where remessa = {$this->remessa} and conta_contabil like '6.2.2.1.3.04.%';",
];

return [Test::ab($this, $test_name, $qualifier, $lsql, $rsql)];