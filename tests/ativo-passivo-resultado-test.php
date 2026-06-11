<?php

use Tester\Support\Test;

$entidades = [
    'pm' => 'PM',
    'cm' => 'CM',
    'fpsm' => 'FPSM',
];

$test_name = 'Ativo = Passivo + Resultado';

$result = [];

foreach ($entidades as $entidade => $qualifier) {
    $lsql = [
        'Saldo final da conta contábil 1.*' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '1.%' and entidade like '$entidade';",
    ];
    $rsql = [
        'Saldo final da conta contábil 2.*' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '2.%' and entidade like '$entidade';",
        '(-) Saldo final da conta contábil 3.*' => "select sum(saldo_atual)*-1 from balver where remessa = {$this->remessa} and conta_contabil like '3.%' and entidade like '$entidade';",
        'Saldo final da conta contábil 4.*' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '4.%' and entidade like '$entidade';",
    ];

    $result[] = Test::ab($this, $test_name, $qualifier, $lsql, $rsql);
}




return $result;