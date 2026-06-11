<?php

namespace Tester;

class ResultTest
{
    public function __construct(
        public readonly bool $success,
        public readonly string $qualifier,
        public readonly string $html
    )
    {

    }
}