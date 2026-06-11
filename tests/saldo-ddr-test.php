<?php

use Tester\Support\Test;

$entidades = [
    'pm' => 'PM',
    'cm' => 'CM',
    'fpsm' => 'FPSM',
];

$test_name = 'Saldo das DDR';

$result = [];

foreach ($entidades as $entidade => $qualifier) {
    $lsql = [
        'Saldo final da conta contábil 8.2.1.1.1' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '8.2.1.1.1.%' and entidade like '$entidade';",
    ];
    $rsql = [
        'Saldo final da conta contábil 1.* (F)' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '1.%' and entidade like '$entidade' and indicador_superavit like 'F';",
        '(-) Saldo final da conta contábil 6.2.2.1.3.01' => "select sum(saldo_atual)*-1 from balver where remessa = {$this->remessa} and conta_contabil like '6.2.2.1.3.01.%' and entidade like '$entidade';",
        '(-) Saldo final da conta contábil 6.2.2.1.3.03' => "select sum(saldo_atual)*-1 from balver where remessa = {$this->remessa} and conta_contabil like '6.2.2.1.3.03.%' and entidade like '$entidade';",
        '(-) Saldo final da conta contábil 6.3.1.1' => "select sum(saldo_atual)*-1 from balver where remessa = {$this->remessa} and conta_contabil like '6.3.1.1.%' and entidade like '$entidade';",
        '(-) Saldo final da conta contábil 6.3.1.3' => "select sum(saldo_atual)*-1 from balver where remessa = {$this->remessa} and conta_contabil like '6.3.1.3.%' and entidade like '$entidade';",
        '(-) Saldo final da conta contábil 6.3.2.1' => "select sum(saldo_atual)*-1 from balver where remessa = {$this->remessa} and conta_contabil like '6.3.2.1.%' and entidade like '$entidade';",
        '(-) Saldo final da conta contábil 2.1.8.8.* (F)' => "select sum(saldo_atual)*-1 from balver where remessa = {$this->remessa} and conta_contabil like '2.1.8.8.%' and entidade like '$entidade' and indicador_superavit like 'F';",
    ];

    $result[] = Test::ab($this, $test_name, $qualifier, $lsql, $rsql);
}




return $result;