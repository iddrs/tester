<?php

use Tester\Support\Test;

$entidades = [
    'pm' => 'PM',
    'cm' => 'CM',
    'fpsm' => 'FPSM',
];

$test_name = 'Saldo dos recursos de exercícios anteriores';

$result = [];

foreach ($entidades as $entidade => $qualifier) {
    $lsql = [
        'Saldo final da conta contábil 8.2.1.1.1.02' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '8.2.1.1.1.02.%' and entidade like '$entidade';",
    ];
    $rsql = [
        'Saldo inicial da conta contábil 1.* (F)' => "select sum(saldo_anterior) from balver where remessa = {$this->remessa} and conta_contabil like '1.%' and entidade like '$entidade' and indicador_superavit like 'F';",
        '(-) Saldo final da conta contábil 5.3.1' => "select sum(saldo_atual)*-1 from balver where remessa = {$this->remessa} and conta_contabil like '5.3.1.%' and entidade like '$entidade';",
        '(-) Saldo final da conta contábil 5.3.2' => "select sum(saldo_atual)*-1 from balver where remessa = {$this->remessa} and conta_contabil like '5.3.2.%' and entidade like '$entidade';",
        '(-) Saldo inicial da conta contábil 2.1.8.8.* (F)' => "select sum(saldo_anterior)*-1 from balver where remessa = {$this->remessa} and conta_contabil like '2.1.8.8.%' and entidade like '$entidade' and indicador_superavit like 'F';",
        '(-) Saldo final da conta contábil 5.2.2.1.3.01' => "select sum(saldo_atual)*-1 from balver where remessa = {$this->remessa} and conta_contabil like '5.2.2.1.3.01.%' and entidade like '$entidade';",
        '(+) Saldo final da conta contábil 6.3.1.9' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '6.3.1.9.%' and entidade like '$entidade';",
        '(+) Saldo final da conta contábil 6.3.2.9' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '6.3.2.9.%' and entidade like '$entidade';",
    ];

    $result[] = Test::ab($this, $test_name, $qualifier, $lsql, $rsql);
}




return $result;