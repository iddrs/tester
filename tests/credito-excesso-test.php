<?php

use Tester\Support\Test;

$entidades = [
    'pm' => 'PM',
    'cm' => 'CM',
    'fpsm' => 'FPSM',
];

$test_name = 'Crédito aberto por excesso de arrecadação';

$result = [];

foreach ($entidades as $entidade => $qualifier) {
    $lsql = [
        'Saldo final da conta contábil 5.2.2.1.3.02' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '5.2.2.1.3.02.%' and entidade like '$entidade';",
    ];
    $rsql = [
        'Crédito aberto por excesso de arrecadação' => "select sum(valor_credito) from decreto where remessa = {$this->remessa} and entidade like '$entidade' and origem_recurso = 2;",
    ];

    $result[] = Test::ab($this, $test_name, $qualifier, $lsql, $rsql);
}




return $result;