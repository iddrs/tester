<?php

use Tester\Support\Test;

$entidades = [
    'pm' => 'PM',
    'cm' => 'CM',
    'fpsm' => 'FPSM',
];

$test_name = 'Valores transferidos por contrato de rateio: Cofron';

$result = [];

foreach ($entidades as $entidade => $qualifier) {
    $lsql = [
        'Saldo final da conta contábil 8.5.3.1.0.05' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '8.5.3.1.0.05.%' and entidade like '$entidade';",
    ];
    $rsql = [
        'Pago no ano na despesa _._.71.70 no credor 451' => "select sum(valor) from pagamento where remessa = {$this->remessa} and entidade like '$entidade' and rubrica like '_._.71.70.%' and credor = 451 and data between '{$this->dataInicial}' and '{$this->dataFinal}';",
    ];

    $result[] = Test::ab($this, $test_name, $qualifier, $lsql, $rsql);
}




return $result;