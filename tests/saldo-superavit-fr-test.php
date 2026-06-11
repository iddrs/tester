<?php

use Tester\ResultTest;

$entidades = [
    'pm' => 'PM',
    'cm' => 'CM',
    'fpsm' => 'FPSM',
];

$test_name = 'Saldo de superávit financeiro disponível para abertura de créditos adicionais';

$result = [];
$success = true;

foreach ($entidades as $entidade => $qualifier) {
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
            ifnull((
                select sum(saldo_anterior)
                from balver
                where remessa = {$this->remessa}
                and entidade like '$entidade'
                and conta_contabil like '1.%'
                and indicador_superavit like 'F'
                and balver.fonte_recurso = fr.fonte_recurso
            ), 0) as ativo_financeiro,
            ifnull((
                select sum(saldo_atual) from msc
                where remessa = {$this->remessa}
                and entidade like '$entidade'
                and conta_contabil like '5.3.%'
                and msc.fonte_recurso = fr.fonte_recurso
            ), 0) as rp_a_pagar,
            ifnull((
            select sum(saldo_atual) from msc
            where remessa = {$this->remessa}
              and entidade like '$entidade'
            and conta_contabil like '6.3._.9.%'
            and msc.fonte_recurso = fr.fonte_recurso
            ), 0) as rp_cancelados,
            ifnull((
                select sum(valor_credito) from decreto
                where remessa = {$this->remessa}
                and entidade like '$entidade'
                and origem_recurso = 1
                and fonte_recurso_credito = fr.fonte_recurso
            ), 0) as creditos_abertos,
            ifnull((
                       select sum(saldo_atual) from balver
                       where remessa = {$this->remessa}
                         and entidade like '$entidade'
                         and conta_contabil like '8.2.1.1.1.02.%'
                         and balver.fonte_recurso = fr.fonte_recurso
                   ), 0) as saldo_contabil
        from fr
            )
        select
            fonte_recurso,
            nome,
            ativo_financeiro,
            rp_a_pagar,
            (ativo_financeiro - rp_a_pagar) as superavit_inicial,
            rp_cancelados,
            creditos_abertos,
            (ativo_financeiro - rp_a_pagar + rp_cancelados - creditos_abertos) as superavit_disponivel,
            saldo_contabil,
            (ativo_financeiro - rp_a_pagar + rp_cancelados - creditos_abertos - saldo_contabil) as diferenca
        from t1
        order by fonte_recurso asc
    SQL;

    $resultset = $this->db->query($sql);
    $errors = 0;
    $rows = [];
    foreach ($resultset->rows(true) as $row) {
        $rows[] = $row;
        if(round($row['diferenca'], 0) !== 0.0){
            $success = false;
            $errors++;
        }
    }


    $html = $this->render('saldo-superavit-fr-test', [
        'testName' => $test_name,
        'qualifier' => $qualifier,
        'rows' => $rows,
        'errors' => $errors,
    ]);
    $result[] = new ResultTest($success, $qualifier, $html);
}




return $result;