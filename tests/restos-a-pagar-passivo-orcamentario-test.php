<?php

use Tester\Support\Test;

$entidades = [
    'pm' => 'PM',
    'cm' => 'CM',
    'fpsm' => 'FPSM',
];

$test_name = 'Restos a pagar processado e não processado liquidado: passivo x controle orçamentário';

$result = [];

foreach ($entidades as $entidade => $qualifier) {
    $lsql = [
        'Saldo final da conta contábil 2.1.3.1.1.01.01.02 (F)' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '2.1.3.1.1.01.01.02.%' and balver.indicador_superavit like 'F' and entidade like '$entidade';",
    ];
    $rsql = [
        'Saldo final da conta contábil 6.3.1.3' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '6.3.1.3.%' and entidade like '$entidade';",
        'Saldo final da conta contábil 6.3.2.1' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '6.3.2.1.%' and entidade like '$entidade';",
    ];

    $result[] = Test::ab($this, $test_name, $qualifier, $lsql, $rsql);
}




return $result;