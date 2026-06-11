<?php

use Tester\Support\Test;

$entidades = [
    'pm' => 'PM',
    'cm' => 'CM',
    'fpsm' => 'FPSM',
];

$test_name = 'Fechamento das contas de controle';

$result = [];

foreach ($entidades as $entidade => $qualifier) {
    $lsql = [
        'Débitos dos controles devedores' => "select sum(movimento_debito) from balver where remessa = {$this->remessa} and conta_contabil like '7.%' and entidade like '$entidade';",
        'Débitos dos controles credores' => "select sum(movimento_debito) from balver where remessa = {$this->remessa} and conta_contabil like '8.%' and entidade like '$entidade';",
    ];
    $rsql = [
        'Créditos dos controles devedores' => "select sum(movimento_credito) from balver where remessa = {$this->remessa} and conta_contabil like '7.%' and entidade like '$entidade';",
        'Créditos dos controles credores' => "select sum(movimento_credito) from balver where remessa = {$this->remessa} and conta_contabil like '8.%' and entidade like '$entidade';",
    ];

    $result[] = Test::ab($this, $test_name, $qualifier, $lsql, $rsql);
}




return $result;