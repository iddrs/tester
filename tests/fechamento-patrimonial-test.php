<?php

use Tester\Support\Test;

$entidades = [
    'pm' => 'PM',
    'cm' => 'CM',
    'fpsm' => 'FPSM',
];

$test_name = 'Fechamento das contas patrimoniais';

$result = [];

foreach ($entidades as $entidade => $qualifier) {
    $lsql = [
        'Débitos do ativo' => "select sum(movimento_debito) from balver where remessa = {$this->remessa} and conta_contabil like '1.%' and entidade like '$entidade';",
        'Débitos do passivo' => "select sum(movimento_debito) from balver where remessa = {$this->remessa} and conta_contabil like '2.%' and entidade like '$entidade';",
        'Débitos das VPDs' => "select sum(movimento_debito) from balver where remessa = {$this->remessa} and conta_contabil like '3.%' and entidade like '$entidade';",
        'Débitos das VPAs' => "select sum(movimento_debito) from balver where remessa = {$this->remessa} and conta_contabil like '4.%' and entidade like '$entidade';",
    ];
    $rsql = [
        'Créditos do ativo' => "select sum(movimento_credito) from balver where remessa = {$this->remessa} and conta_contabil like '1.%' and entidade like '$entidade';",
        'Créditos do passivo' => "select sum(movimento_credito) from balver where remessa = {$this->remessa} and conta_contabil like '2.%' and entidade like '$entidade';",
        'Créditos das VPDs' => "select sum(movimento_credito) from balver where remessa = {$this->remessa} and conta_contabil like '3.%' and entidade like '$entidade';",
        'Créditos das VPAs' => "select sum(movimento_credito) from balver where remessa = {$this->remessa} and conta_contabil like '4.%' and entidade like '$entidade';",
    ];

    $result[] = Test::ab($this, $test_name, $qualifier, $lsql, $rsql);
}




return $result;