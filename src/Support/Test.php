<?php

namespace Tester\Support;

use Tester\Processor;
use Tester\ResultTest;

class Test
{
    static public function ab(Processor $processor, string $testName, string $qualifier, array $lsql, array $rsql): ResultTest
    {
        $success = false;

        $litems = [];
        $ritems = [];
        $ltotal = 0.0;
        $rtotal = 0.0;

        foreach ($lsql as $label => $sql) {
            $lvalue = 0.0;
            foreach ($processor->db->query($sql)->rows() as $row){
                $lvalue += $row[0];
            }
            $litems[$label] = $lvalue;
            $ltotal += $lvalue;
        }
        $ltotal = round($ltotal, 2);

        foreach ($rsql as $label => $sql) {
            $rvalue = 0.0;
            foreach ($processor->db->query($sql)->rows() as $row){
                $rvalue += floatval($row[0]);
            }
            $ritems[$label] = $rvalue;
            $rtotal += $rvalue;
        }
        $rtotal = round($rtotal, 2);

        $diff = round($rtotal - $ltotal, 2);

        if($diff === 0.0){
            $success = true;
        }

        $html = $processor->render('ab-test', [
            'testName' => $testName,
            'qualifier' => $qualifier,
            'litems' => $litems,
            'ltotal' => $ltotal,
            'ritems' => $ritems,
            'rtotal' => $rtotal,
            'diff' => $diff,
        ]);
        return new ResultTest($success, $qualifier, $html);
    }
}