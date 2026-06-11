<?php

use Tester\ResultTest;

$entidades = [
    'pm' => 'PM',
    'cm' => 'CM',
    'fpsm' => 'FPSM',
];

$test_name = 'Saldo das DDR comprometida por liquidação por fonte de recursos';

$result = [];

foreach ($entidades as $entidade => $qualifier) {
    $success = true;
    $sql = <<<SQL
    with fr as (
    select distinct
        fonte_recurso, nome
    from fonte_recurso
    where exercicio = {$this->exercicio}
    and fonte_recurso not between 860 and 869
    order by fonte_recurso asc
    ),
    t1 as (
    select
    fr.*,
    (
        ifnull((
            select sum(valor)
            from liquidacao
            where remessa = {$this->remessa}
            and entidade like '$entidade'
            and data between '{$this->dataInicial}' and '{$this->dataFinal}'
            and ano_empenho = {$this->exercicio}
            and liquidacao.fonte_recurso = fr.fonte_recurso
        ), 0)
        - ifnull((
            select sum(valor)
            from pagamento
            where remessa = {$this->remessa}
            and entidade like '$entidade'
            and data between '{$this->dataInicial}' and '{$this->dataFinal}'
            and ano_empenho = {$this->exercicio}
            and pagamento.fonte_recurso = fr.fonte_recurso
        ), 0)
    ) as liquidado,
    ifnull((
        select sum(saldo_atual)
        from msc
        where remessa = {$this->remessa}
        and entidade like '$entidade'
        and conta_contabil like '6.3.1.3.%'
        and msc.fonte_recurso = fr.fonte_recurso
    ), 0) as rpnp_liquidado,
    ifnull((
        select sum(saldo_atual)
        from msc
        where remessa = {$this->remessa}
        and entidade like '$entidade'
        and conta_contabil like '6.3.2.1.%'
        and msc.fonte_recurso = fr.fonte_recurso
    ), 0) as rpp,
    ifnull((
           select sum(saldo_atual)
           from balver
           where remessa = {$this->remessa}
             and entidade like '$entidade'
             and conta_contabil like '8.2.1.1.3.01.%'
             and balver.fonte_recurso = fr.fonte_recurso
       ), 0) as saldo_contabil
            from fr
    ),
    t2 as (
        select t1.*,
               (liquidado + rpnp_liquidado + rpp) as comprometida,
               (saldo_contabil - liquidado - rpnp_liquidado - rpp) as diferenca
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

    $html = $this->render('saldo-ddr-comprometida-liquidacao-fr-test', [
        'testName' => $test_name,
        'qualifier' => $qualifier,
        'rows' => $rows,
        'errors' => $errors,
    ]);
    $result[] = new ResultTest($success, $qualifier, $html);
}




return $result;