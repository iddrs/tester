<?php

use Tester\Support\Test;

$entidades = [
    'pm' => 'PM',
    'cm' => 'CM',
    'fpsm' => 'FPSM',
];

$test_name = 'Crédito empenhado, liquidado e pago';

$result = [];

foreach ($entidades as $entidade => $qualifier) {
    $lsql = [
        'Saldo final da conta contábil 6.2.2.1.3.04' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '6.2.2.1.3.04.%' and entidade like '$entidade';",
    ];
    $rsql = [
        'Pagamento do exercício' => "select sum(valor_pago) from baldesp where remessa = {$this->remessa} and entidade like '$entidade';",
    ];

    $result[] = Test::ab($this, $test_name, $qualifier, $lsql, $rsql);
}




return $result;