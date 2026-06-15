<?php

use Tester\Support\Test;

$entidades = [
    'pm' => 'PM',
    'cm' => 'CM',
    'fpsm' => 'FPSM',
];

$test_name = 'Anulação de empenhos do exercício';

$result = [];

foreach ($entidades as $entidade => $qualifier) {
    $lsql = [
        'Saldo final da conta contábil 5.2.2.9.2.01.03' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '5.2.2.9.2.01.03.%' and entidade like '$entidade';",
    ];
    $rsql = [
        'Anulação de empenhos do exercício' => "select sum(valor) from empenho where remessa = {$this->remessa} and entidade like '$entidade' and ano_empenho = {$this->exercicio} and valor < 0;",
    ];

    $result[] = Test::ab($this, $test_name, $qualifier, $lsql, $rsql);
}




return $result;