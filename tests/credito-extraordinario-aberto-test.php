<?php

use Tester\Support\Test;

$entidades = [
    'pm' => 'PM',
    'cm' => 'CM',
    'fpsm' => 'FPSM',
];

$test_name = 'Crédito extraordinário aberto';

$result = [];

foreach ($entidades as $entidade => $qualifier) {
    $lsql = [
        'Saldo final da conta contábil 5.2.2.1.2.03.01' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '5.2.2.1.2.03.01.%' and entidade like '$entidade';",
    ];
    $rsql = [
        'Crédito extraordinário aberto' => "select sum(valor_credito) from decreto where remessa = {$this->remessa} and entidade like '$entidade' and tipo_credito = 3 and data_reabertura is null;",
    ];

    $result[] = Test::ab($this, $test_name, $qualifier, $lsql, $rsql);
}




return $result;