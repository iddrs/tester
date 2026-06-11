<?php

use Tester\ResultTest;

$entidades = [
    'pm' => 'PM',
    'cm' => 'CM',
    'fpsm' => 'FPSM',
];

$test_name = 'Saldo das DDR comprometida por retenções e consignações por fonte de recursos';

$result = [];

foreach ($entidades as $entidade => $qualifier) {
    $success = true;
    $sql = <<<SQL
    with fr as (
    select distinct
        fonte_recurso, nome
    from fonte_recurso
    where exercicio = {$this->exercicio}
    and fonte_recurso between 860 and 869
    order by fonte_recurso asc
    ),
    t1 as (
    select
    fr.*,
    ifnull((
        select sum(saldo_atual)
        from balver
        where remessa = {$this->remessa}
        and entidade like '$entidade'
        and conta_contabil like '1.1.3.%'
        and indicador_superavit like 'F'
        and balver.fonte_recurso = fr.fonte_recurso
    ), 0) as ativo_extra,
    ifnull((
        select sum(saldo_atual)
        from balver
        where remessa = {$this->remessa}
        and entidade like '$entidade'
        and conta_contabil like '2.1.8.8.%'
        and indicador_superavit like 'F'
        and balver.fonte_recurso = fr.fonte_recurso
    ), 0) as passivo_extra,
    ifnull((
           select sum(saldo_atual)
           from balver
           where remessa = {$this->remessa}
             and entidade like '$entidade'
             and conta_contabil like '8.2.1.1.3.02.%'
             and balver.fonte_recurso = fr.fonte_recurso
       ), 0) as saldo_contabil
            from fr
    ),
    t2 as (
        select t1.*,
               (passivo_extra - ativo_extra) as comprometida,
               (saldo_contabil + ativo_extra - passivo_extra) as diferenca
        from t1
    )
    select * from t2
    order by fonte_recurso asc
    SQL;

    $resultset = $this->db->query($sql);
    $errors = 0;
    $rows = [];
    foreach ($resultset->rows(true) as $row) {
        $rows[] = $row;
        if(round($row['diferenca'], 0) !== 0.0){
            $errors++;
        }
    }

    if($errors > 0){
        $success = false;
    }

    $html = $this->render('saldo-ddr-comprometida-extra-fr-test', [
        'testName' => $test_name,
        'qualifier' => $qualifier,
        'rows' => $rows,
        'errors' => $errors,
    ]);
    $result[] = new ResultTest($success, $qualifier, $html);
}




return $result;