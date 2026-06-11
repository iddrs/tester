<?php

use Tester\Support\Test;

$test_name = 'Redução/suplementação entre entidades';
$qualifier = 'consolidado';

$lsql = [
    'Suplementação por redução em outra entidade' => "select sum(valor_credito) from decreto where remessa = {$this->remessa} and origem_recurso = 6",
];
$rsql = [
    'Redução para suplementação em outra entidade' => "select sum(valor_reducao) from decreto where remessa = {$this->remessa} and origem_recurso = 6",
];

return [Test::ab($this, $test_name, $qualifier, $lsql, $rsql)];