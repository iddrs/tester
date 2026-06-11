<?php

use Tester\Support\Test;

$test_name = 'MSC: redução de dotações';
$qualifier = 'consolidado';

$lsql = [
    'Relatórios (valor manual)' => "select sum(valor) from manual_values_total where item like 'Redução de dotações';",
];
$rsql = [
    'Valor da MSC' => "select sum(saldo_atual)*-1 from msc where remessa = {$this->remessa} and conta_contabil like '5.2.2.1.9.04.%';",
];

return [Test::ab($this, $test_name, $qualifier, $lsql, $rsql)];