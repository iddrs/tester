<?php

use Tester\ResultTest;

$entidades = [
    'pm' => 'PM',
    'cm' => 'CM',
    'fpsm' => 'FPSM',
];

$test_name = 'Saldos invertidos';

$result = [];
$success = true;

foreach ($entidades as $entidade => $qualifier) {
    $sql = <<<SQL
        with t0 as (
            select
                p.conta_contabil,
                p.especificacao,
                p.radical,
                a.natureza_saldo as natureza_esperada
            from pcasp p
            left join auxiliar_saldo_invertido a on p.conta_contabil like a.conta_contabil
            where p.exercicio = {$this->exercicio}
        ),
        t1 as (
            select
                conta_contabil,
                especificacao,
                radical,
                ifnull(natureza_esperada,
                       case
                           when substring(conta_contabil, 1, 1) in ('1', '3', '5', '7') then 'D'
                           when substring(conta_contabil, 1, 1) in ('2', '4', '6', '8') then 'c'
                       end
                ) as natureza_esperada
            from t0
        ),
        t2 as (
            select t1.*,
                ifnull((
                select sum(saldo_atual)
                from balver
                where remessa = {$this->remessa}
                and entidade like '$entidade'
                and balver.conta_contabil ^@ t1.radical
                ), 0) as saldo_atual_encontrado
            from t1
        ),
        t3 as (
            select t2.*,
                case
                    when substring(conta_contabil, 1, 1) in ('1', '3', '5', '7') and saldo_atual_encontrado > 0 then 'D'
                    when substring(conta_contabil, 1, 1) in ('1', '3', '5', '7') and saldo_atual_encontrado < 0 then 'C'
                    when substring(conta_contabil, 1, 1) in ('2', '4', '6', '8') and saldo_atual_encontrado > 0 then 'C'
                    when substring(conta_contabil, 1, 1) in ('2', '4', '6', '8') and saldo_atual_encontrado < 0 then 'D'
                end as natureza_encontrada
            from t2
        ),
        t4 as (
            select t3.*,
                case
                    when saldo_atual_encontrado = 0.0 then 'N'
                    when upper(natureza_esperada) = 'DC' then 'N'
                    when upper(natureza_esperada) = natureza_encontrada then 'N'
                    else 'S'
                end as diferenca
            from t3
        )
        select * from t4
        where diferenca like 'S'
        order by conta_contabil asc
    SQL;

    $resultset = $this->db->query($sql);
    $rows = [];
    foreach ($resultset->rows(true) as $row) {
        $rows[] = $row;
    }
    $errors = count($rows);
    if ($errors > 0) {
        $success = false;
    }

    $html = $this->render('saldo-invertido-test', [
        'testName' => $test_name,
        'qualifier' => $qualifier,
        'rows' => $rows,
        'errors' => $errors,
    ]);
    $result[] = new ResultTest($success, $qualifier, $html);
}




return $result;