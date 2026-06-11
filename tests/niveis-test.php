<?php

use Tester\ResultTest;

$entidades = [
    'pm' => 'PM',
    'cm' => 'CM',
    'fpsm' => 'FPSM',
];

$test_name = 'Equivalência entre níveis de contas contábeis';

$result = [];
$success = true;

foreach ($entidades as $entidade => $qualifier) {
    $sql = <<<SQL
    with t0 as (
        select distinct
            conta_contabil, pcasp.radical
        from pcasp
        where exercicio = {$this->exercicio}
        and (conta_contabil like '5.%' or conta_contabil like '7.%')
        and pcasp.radical not in (select radical from auxiliar_niveis)
        order by conta_contabil asc
    ),
    t1 as (
        select t0.*,
        ifnull((select sum(saldo_atual) from balver where remessa = {$this->remessa} and entidade like '$entidade' and balver.conta_contabil ^@ t0.radical), 0) as saldo1
        from t0
            order by conta_contabil asc
    ),
    t2 as (
        select t1.*,
            case substring(conta_contabil, 1, 1)
                when '5' then '6' || substring(radical, 2)
                when '7' then '8' || substring(radical, 2)
            end as radical2
        from t1
    ),
    t3 as (
    select t2.*,
        ifnull((select sum(saldo_atual) from balver where remessa = {$this->remessa} and entidade like '$entidade' and balver.conta_contabil ^@ t2.radical2), 0) as saldo2
    from t2
    order by conta_contabil asc
    ),
    t4 as (
        select t3.*,
        (saldo1 - saldo2) as diferenca
        from t3
    )
    select
        radical as conta1,
        saldo1,
        radical2 as conta2,
        saldo2,
        diferenca
    from t4
    where diferenca <> 0;
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


    $html = $this->render('niveis-test', [
        'testName' => $test_name,
        'qualifier' => $qualifier,
        'rows' => $rows,
        'errors' => $errors,
    ]);
    $result[] = new ResultTest($success, $qualifier, $html);
}




return $result;