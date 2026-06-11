<?php

use Tester\Support\Test;

$test_name = 'MSC: receita líquida arrecadada';
$qualifier = 'consolidado';

$lsql = [
    'Relatórios (valor manual)' => "select sum(valor) from manual_values_total where item like 'Receita líquida arrecadada';",
];
$rsql = [
    'Valor da MSC (6.2.1.2)' => "select sum(saldo_atual) from msc where remessa = {$this->remessa} and conta_contabil like '6.2.1.2.%';",
    'Valor da MSC (6.2.1.3)' => "select sum(saldo_atual) from msc where remessa = {$this->remessa} and conta_contabil like '6.2.1.3.%';",
];

return [Test::ab($this, $test_name, $qualifier, $lsql, $rsql)];