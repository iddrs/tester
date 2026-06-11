<?php

use Tester\Support\Test;

$entidades = [
    'pm' => 'PM',
//    'cm' => 'CM',
    'fpsm' => 'FPSM',
];

$test_name = 'Reestimativa da receita';

$result = [];

foreach ($entidades as $entidade => $qualifier) {
    $lsql = [
        'Saldo final da conta contábil 5.2.1.2.1.01' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '5.2.1.2.1.01.%' and entidade like '$entidade';",
    ];
    $rsql = [
        'Previsão atualizada da receita' => "select sum(previsao_atualizada) from balrec where remessa = {$this->remessa} and entidade like '$entidade';",
        '(-) Previsão inicial da receita' => "select sum(receita_orcada)*-1 from balrec where remessa = {$this->remessa} and entidade like '$entidade';",
    ];

    $result[] = Test::ab($this, $test_name, $qualifier, $lsql, $rsql);
}




return $result;