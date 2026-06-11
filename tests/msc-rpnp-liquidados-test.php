<?php

use Tester\Support\Test;

$test_name = 'MSC: restos a pagar não processados liquidados';
$qualifier = 'consolidado';

$lsql = [
    'Relatórios (valor manual)' => "select sum(valor) from manual_values_total where item like 'RPNP liquidados';",
];
$rsql = [
    'Valor da MSC (6.3.1.3)' => "select sum(saldo_atual) from msc where remessa = {$this->remessa} and conta_contabil like '6.3.1.3.%';",
    'Valor da MSC (6.3.1.4)' => "select sum(saldo_atual) from msc where remessa = {$this->remessa} and conta_contabil like '6.3.1.4.%';",
];

return [Test::ab($this, $test_name, $qualifier, $lsql, $rsql)];