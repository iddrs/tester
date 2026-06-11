<?php

use Tester\ResultTest;

$test_name = 'Receitas com mais dedução do que arrecadação';
$qualifier = 'consolidado';
$success = true;

$sql = <<<SQL
select natureza_receita, especificacao, sum(receita_realizada) as arrecadacao_liquida 
from balrec 
where remessa = {$this->remessa}
group by natureza_receita, especificacao
having sum(receita_realizada) < 0
order by natureza_receita asc;
SQL;

$result = $this->db->query($sql);
foreach ($result->rows(true) as $row) {
    $rows[] = $row;
}
if(count($rows) > 0){
    $success = false;
}

$html = $this->render('deducao-maior-arrecadacao-test', [
    'testName' => $test_name,
    'qualifier' => $qualifier,
    'rows' => $rows,
]);

return [new ResultTest($success, $qualifier, $html)];