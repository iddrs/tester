<?php

use Tester\Support\Test;

$test_name = 'MSC: restos a pagar processados inscritos no último ano';
$qualifier = 'consolidado';

$lsql = [
    'Relatórios (valor manual)' => "select sum(valor) from manual_values_total where item like 'RPP inscritos ano anterior';",
];
$rsql = [
    'Valor da MSC' => "select sum(saldo_atual) from msc where remessa = {$this->remessa} and conta_contabil like '5.3.2.1.%';",
];

return [Test::ab($this, $test_name, $qualifier, $lsql, $rsql)];