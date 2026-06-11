<?php

use Tester\Support\Test;

$entidades = [
    'pm' => 'PM',
    'cm' => 'CM',
    'fpsm' => 'FPSM',
];

$test_name = 'PAD / DECRETO: anulação de dotações';

$result = [];

foreach ($entidades as $entidade => $qualifier) {
    $lsql = [
        'Relatórios (Anulação de dotação) (valor manual)' => "select sum(valor) from manual_values_{$entidade} where item like 'Anulação de dotação';",
        'Relatórios (Superávit com redução) (valor manual)' => "select sum(valor) from manual_values_{$entidade} where item like 'Superávit com redução';",
    ];
    $rsql = [
        'Valor do PAD (redução na mesma entidade)' => "select sum(valor_credito) from decreto where remessa = {$this->remessa} and entidade like '$entidade' and origem_recurso = 5;",
        'Valor do PAD (redução em outra entidade)' => "select sum(valor_credito) from decreto where remessa = {$this->remessa} and entidade like '$entidade' and origem_recurso = 6;",
    ];

    $result[] = Test::ab($this, $test_name, $qualifier, $lsql, $rsql);
}




return $result;