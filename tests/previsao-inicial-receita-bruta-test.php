<?php

use Tester\Support\Test;

$entidades = [
    'pm' => 'PM',
//    'cm' => 'CM',
    'fpsm' => 'FPSM',
];

$test_name = 'Previsão inicial da receita bruta';

$result = [];

foreach ($entidades as $entidade => $qualifier) {
    $lsql = [
        'Saldo final da conta contábil 5.2.1.1.1' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '5.2.1.1.1.%' and entidade like '$entidade';",
    ];
    $rsql = [
        'Previsão inicial da receita bruta' => "select sum(receita_orcada) from balrec where remessa = {$this->remessa} and entidade like '$entidade' and deducao = 0;",
    ];

    $result[] = Test::ab($this, $test_name, $qualifier, $lsql, $rsql);
}




return $result;