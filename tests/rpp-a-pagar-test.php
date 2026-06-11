<?php

use Tester\Support\Test;

$entidades = [
    'pm' => 'PM',
    'cm' => 'CM',
    'fpsm' => 'FPSM',
];

$test_name = 'Restos a pagar processados a pagar';

$result = [];

foreach ($entidades as $entidade => $qualifier) {
    $lsql = [
        'Saldo final da conta contábil 6.3.2.1' => "select sum(saldo_atual) from balver where remessa = {$this->remessa} and conta_contabil like '6.3.2.1.%' and entidade like '$entidade';",
    ];
    $rsql = [
        'Restos a pagar processados inscritos em exercícios anteriores (valor manual)' => "select sum(valor) from manual_values_{$entidade} where item like 'RPP inscritos anteriores';",
        'Restos a pagar processados inscritos no último exercício (valor manual)' => "select sum(valor) from manual_values_{$entidade} where item like 'RPP inscritos ano anterior';",
        '(-) Restos a pagar processados cancelados (valor manual)' => "select sum(valor)*-1 from manual_values_{$entidade} where item like 'RPP cancelados';",
        '(-) Restos a pagar processados pagos (valor manual)' => "select sum(valor)*-1 from manual_values_{$entidade} where item like 'RPP pagos';",
    ];

    $result[] = Test::ab($this, $test_name, $qualifier, $lsql, $rsql);
}




return $result;