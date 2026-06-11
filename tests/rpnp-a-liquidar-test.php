<?php

use Tester\Support\Test;

$entidades = [
    'pm' => 'PM',
    'cm' => 'CM',
    'fpsm' => 'FPSM',
];

$test_name = 'Restos a pagar não processados a liquidar';

$result = [];

foreach ($entidades as $entidade => $qualifier) {
    $lsql = [
        'Saldo final da conta contábil 6.3.1.1' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '6.3.1.1.%' and entidade like '$entidade';",
    ];
    $rsql = [
        'Restos a pagar não processados inscritos em exercícios anteriores (valor manual)' => "select sum(valor) from manual_values_{$entidade} where item like 'RPNP inscritos anteriores';",
        'Restos a pagar não processados inscritos no último exercício (valor manual)' => "select sum(valor) from manual_values_{$entidade} where item like 'RPNP inscritos ano anterior';",
        '(-) Restos a pagar não processados liquidados (valor manual)' => "select sum(valor)*-1 from manual_values_{$entidade} where item like 'RPNP liquidados';",
        '(-) Restos a pagar não processados cancelados (valor manual)' => "select sum(valor)*-1 from manual_values_{$entidade} where item like 'RPNP cancelados';",
    ];

    $result[] = Test::ab($this, $test_name, $qualifier, $lsql, $rsql);
}




return $result;