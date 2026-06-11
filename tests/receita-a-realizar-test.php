<?php

use Tester\Support\Test;

$entidades = [
    'pm' => 'PM',
//    'cm' => 'CM',
    'fpsm' => 'FPSM',
];

$test_name = 'Receita a realizar';

$result = [];

foreach ($entidades as $entidade => $qualifier) {
    $lsql = [
        'Saldo final da conta contábil 6.2.1.1' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '6.2.1.1.%' and entidade like '$entidade';",
    ];
    $rsql = [
        'Receita a realizar' => "select sum(dif_realizada_atualizada)*-1 from balrec where remessa = {$this->remessa} and entidade like '$entidade';",
    ];

    $result[] = Test::ab($this, $test_name, $qualifier, $lsql, $rsql);
}




return $result;