<?php

use Tester\Support\Test;

$entidades = [
    'pm' => 'PM',
    'cm' => 'CM',
    'fpsm' => 'FPSM',
];

$test_name = 'Saldo final do patrimônio';

$result = [];

foreach ($entidades as $entidade => $qualifier) {
    $lsql = [
        'Saldo final da conta contábil 1.2.3' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '1.2.3.%' and entidade like '$entidade';",
        '(-) Saldo final da conta contábil 1.2.3.1.1.99.08' => "select sum(saldo_atual)*-1 from balver where remessa = {$this->remessa} and conta_contabil like '1.2.3.1.1.99.08.%' and entidade like '$entidade';",
        '(-) Saldo final da conta contábil 1.2.3.1.1.99.99' => "select sum(saldo_atual)*-1 from balver where remessa = {$this->remessa} and conta_contabil like '1.2.3.1.1.99.99.%' and entidade like '$entidade';",
        '(-) Saldo final da conta contábil 1.2.3.2.1.05.01' => "select sum(saldo_atual)*-1 from balver where remessa = {$this->remessa} and conta_contabil like '1.2.3.2.1.05.01.%' and entidade like '$entidade';",
        '(-) Saldo final da conta contábil 1.2.3.2.1.05.03' => "select sum(saldo_atual)*-1 from balver where remessa = {$this->remessa} and conta_contabil like '1.2.3.2.1.05.03.%' and entidade like '$entidade';",
        '(-) Saldo final da conta contábil 1.2.3.2.1.05.04' => "select sum(saldo_atual)*-1 from balver where remessa = {$this->remessa} and conta_contabil like '1.2.3.2.1.05.04.%' and entidade like '$entidade';",
        '(-) Saldo final da conta contábil 1.2.3.2.1.05.06' => "select sum(saldo_atual)*-1 from balver where remessa = {$this->remessa} and conta_contabil like '1.2.3.2.1.05.06.%' and entidade like '$entidade';",
        '(-) Saldo final da conta contábil 1.2.3.2.1.05.99' => "select sum(saldo_atual)*-1 from balver where remessa = {$this->remessa} and conta_contabil like '1.2.3.2.1.05.99.%' and entidade like '$entidade';",
        '(-) Saldo final da conta contábil 1.2.3.2.1.06' => "select sum(saldo_atual)*-1 from balver where remessa = {$this->remessa} and conta_contabil like '1.2.3.2.1.06.%' and entidade like '$entidade';",
        '(-) Saldo final da conta contábil 1.2.3.2.1.07' => "select sum(saldo_atual)*-1 from balver where remessa = {$this->remessa} and conta_contabil like '1.2.3.2.1.07.%' and entidade like '$entidade';",
    ];
    $rsql = [
        'Patrimônio - saldo final (valor manual)' => "select sum(valor) from manual_values_{$entidade} where item like 'Patrimônio - saldo final';",
    ];

    $result[] = Test::ab($this, $test_name, $qualifier, $lsql, $rsql);
}




return $result;