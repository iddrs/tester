<?php

use Tester\Support\Test;

$entidades = [
    'pm' => 'PM',
    'cm' => 'CM',
    'fpsm' => 'FPSM',
];

$test_name = 'Fechamento das contas orçamentárias';

$result = [];

foreach ($entidades as $entidade => $qualifier) {
    $lsql = [
        'Débitos do CAPO' => "select sum(movimento_debito) from balver where remessa = {$this->remessa} and conta_contabil like '5.%' and entidade like '$entidade';",
        'Débitos do CEPO' => "select sum(movimento_debito) from balver where remessa = {$this->remessa} and conta_contabil like '6.%' and entidade like '$entidade';",
    ];
    $rsql = [
        'Créditos do CAPO' => "select sum(movimento_credito) from balver where remessa = {$this->remessa} and conta_contabil like '5.%' and entidade like '$entidade';",
        'Créditos do CEPO' => "select sum(movimento_credito) from balver where remessa = {$this->remessa} and conta_contabil like '6.%' and entidade like '$entidade';",
    ];

    $result[] = Test::ab($this, $test_name, $qualifier, $lsql, $rsql);
}




return $result;