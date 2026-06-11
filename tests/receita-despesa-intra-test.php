<?php

use Tester\Support\Test;

$test_name = 'Receita x Despesa intra-orçamentária';
$qualifier = 'consolidado';

$lsql = [
    'Receita Corrente intra-orçamentária arrecadada' => "select sum(receita_realizada) from balrec where remessa = {$this->remessa} and codigo_receita like '7.%'",
    'Receita de Capital intra-orçamentária arrecadada' => "select sum(receita_realizada) from balrec where remessa = {$this->remessa} and codigo_receita like '8.%'",
];
$rsql = [
    'Despesa intra-orçamentária paga' => "select sum(valor) from pagamento where remessa = {$this->remessa} and rubrica like '_._.91.%' and data between '{$this->dataInicial}' and '$this->dataFinal'",
];

return [Test::ab($this, $test_name, $qualifier, $lsql, $rsql)];