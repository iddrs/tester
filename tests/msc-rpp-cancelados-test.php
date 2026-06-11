<?php

use Tester\Support\Test;

$test_name = 'MSC: restos a pagar processados cancelados';
$qualifier = 'consolidado';

$lsql = [
    'Relatórios (valor manual)' => "select sum(valor) from manual_values_total where item like 'RPP cancelados';",
];
$rsql = [
    'Valor da MSC' => "select sum(saldo_atual) from msc where remessa = {$this->remessa} and conta_contabil like '6.3.2.9.%';",
];

return [Test::ab($this, $test_name, $qualifier, $lsql, $rsql)];