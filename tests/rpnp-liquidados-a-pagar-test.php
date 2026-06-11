<?php

use Tester\Support\Test;

$entidades = [
    'pm' => 'PM',
    'cm' => 'CM',
    'fpsm' => 'FPSM',
];

$test_name = 'Restos a pagar não processados liquidados a pagar';

$result = [];

foreach ($entidades as $entidade => $qualifier) {
    $lsql = [
        'Saldo final da conta contábil 6.3.1.3' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '6.3.1.3.%' and entidade like '$entidade';",
    ];
    $rsql = [
        'Restos a pagar não processados liquidados (valor manual)' => "select sum(valor) from manual_values_{$entidade} where item like 'RPNP liquidados';",
        '(-) Restos a pagar não processados pagos (valor manual)' => "select sum(valor)*-1 from manual_values_{$entidade} where item like 'RPNP pagos';",
    ];

    $result[] = Test::ab($this, $test_name, $qualifier, $lsql, $rsql);
}




return $result;