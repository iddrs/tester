<?php

use Tester\ResultTest;

$entidades = [
    'pm' => 'PM',
    'cm' => 'CM',
    'fpsm' => 'FPSM',
];

$test_name = 'Saldo das DDR disponíveis';

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
                select sum(saldo_atual)
                from balver
                where remessa = {$this->remessa}
                    and entidade like '{$entidade}'
                    and conta_contabil like '1.%'
                    and indicador_superavit like 'F'
                    and balver.fonte_recurso = fr.fonte_recurso
            ), 0) as ativo_financeiro,
            (
                ifnull((select sum(valor)
                from empenho
                where remessa = {$this->remessa}
                    and entidade like '$entidade'
                    and empenho.fonte_recurso = fr.fonte_recurso)
               , 0)
                - ifnull((select sum(valor)
                   from pagamento
                   where remessa = {$this->remessa}
                     and entidade like '$entidade'
                     and pagamento.fonte_recurso = fr.fonte_recurso)
               , 0)
            ) as empenhado_a_pagar
        from fr
    ),
    t2 as (
        select
            t1.*,
            (
                ativo_financeiro - empenhado_a_pagar
            ) as ddr_disponivel,
            ifnull((select sum(saldo_atual)
                    from balver
                    where remessa = {$this->remessa}
                      and entidade like '$entidade'
                      and balver.fonte_recurso = t1.fonte_recurso
                      and conta_contabil like '8.2.1.1.1.%'),0) as saldo_contabil
        from t1
    ),
    ddr as (
        select
            t2.*,
            (
                ativo_financeiro - empenhado_a_pagar
            ) as ddr_disponivel,
            (
                ativo_financeiro - empenhado_a_pagar - saldo_contabil
            ) as diferenca
        from t2
    )
    select * from ddr
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


    $html = $this->render('saldo-ddr-fr-test', [
        'testName' => $test_name,
        'qualifier' => $qualifier,
        'rows' => $rows,
        'errors' => $errors,
    ]);
    $result[] = new ResultTest($success, $qualifier, $html);
}




return $result;