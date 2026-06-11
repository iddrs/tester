<?php

use Tester\Support\Test;

$entidades = [
    'pm' => 'PM',
    'cm' => 'CM',
    'fpsm' => 'FPSM',
];

$test_name = 'Extra-orçamentário: a recolher x valores depositados';

$result = [];

foreach ($entidades as $entidade => $qualifier) {
    $lsql = [
        'Saldo final da conta contábil 1.1.1.3.1 (F)' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '1.1.1.3.1.%' and balver.indicador_superavit like 'F' and entidade like '$entidade';",
        'Saldo final da conta contábil 1.1.3.2.3.06 (F)' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '1.1.3.2.3.06.%' and balver.indicador_superavit like 'F' and entidade like '$entidade';",
    ];
    $rsql = [
        'Saldo final da conta contábil 2.1.8.8 (F)' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '2.1.8.8.%' and balver.indicador_superavit like 'F' and entidade like '$entidade';",
    ];

    $result[] = Test::ab($this, $test_name, $qualifier, $lsql, $rsql);
}




return $result;