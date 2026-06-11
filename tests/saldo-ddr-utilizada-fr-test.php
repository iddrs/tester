<?php

use Tester\ResultTest;

$entidades = [
    'pm' => 'PM',
    'cm' => 'CM',
    'fpsm' => 'FPSM',
];

$test_name = 'Saldo das DDR utilizadas por fonte de recursos';

$result = [];

foreach ($entidades as $entidade => $qualifier) {
    $success = true;
    $sql = <<<SQL
    with fr as (
    select distinct
        fonte_recurso, nome
    from fonte_recurso
    where exercicio = {$this->exercicio}
    order by fonte_recurso asc
    ),
    t1 as (
    select
    fr.*,
    ifnull((
    select sum(valor)
    from pagamento
    where remessa = {$this->remessa}
    and entidade like '$entidade'
    and data between '{$this->dataInicial}' and '{$this->dataFinal}'
    and pagamento.fonte_recurso = fr.fonte_recurso
    ), 0) as pagamentos,
    (
    ifnull((select sum(movimento_credito)
    from balver
    where remessa = {$this->remessa}
    and entidade like '$entidade'
    and conta_contabil like '1.1.3.%'
    and indicador_superavit like 'F'
    and balver.fonte_recurso = fr.fonte_recurso)
        , 0)
    + ifnull((select sum(movimento_debito)
    from balver
    where remessa = {$this->remessa}
    and entidade like '$entidade'
    and conta_contabil like '2.1.8.8.%'
    and conta_contabil not like '2.1.8.8.1.04.99.%'
    and indicador_superavit like 'F'
    and balver.fonte_recurso = fr.fonte_recurso)
        , 0)
    ) as despesa_extra,
    case fr.fonte_recurso
        when 500 then ifnull((
                                 select sum(saldo_atual)
                                 from balver
                                 where remessa = {$this->remessa}
                                   and entidade like '$entidade'
                                   and conta_contabil like '3.5.1.1.2.%'
                             ), 0)
        else 0
    end as transferencias_concedidas,
    ifnull((
        select sum(receita_realizada)*-1
        from balrec
        where remessa = {$this->remessa}
        and entidade like '$entidade'
        and deducao > 0
        and balrec.fonte_recurso = fr.fonte_recurso
        and deducao = 105 -- apenas as deduções para o Fundeb é que são consideradas na DDR utilizada
    ), 0) as deducao_receita,
    ifnull((
               select sum(saldo_atual)
               from balver
               where remessa = {$this->remessa}
                 and entidade like '$entidade'
                 and conta_contabil like '8.2.1.1.4.%'
                 and balver.fonte_recurso = fr.fonte_recurso
           ), 0) as saldo_contabil
                from fr
                ),
                t2 as (
                    select
                        t1.*,
                        (pagamentos + despesa_extra + transferencias_concedidas + deducao_receita) as ddr_utilizada
                    from t1
                ),
                t3 as (
                    select t2.*,
                           (saldo_contabil - ddr_utilizada) as diferenca
                    from t2
                )
                select * from t3
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

    $html = $this->render('saldo-ddr-utilizada-fr-test', [
        'testName' => $test_name,
        'qualifier' => $qualifier,
        'rows' => $rows,
        'errors' => $errors,
    ]);
    $result[] = new ResultTest($success, $qualifier, $html);
}




return $result;