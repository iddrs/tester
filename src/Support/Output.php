<?php

namespace Tester\Support;

use Twig\Environment;
use Twig\Extra\Intl\IntlExtension;
use Twig\Loader\FilesystemLoader;

class Output
{
    static public function println(string $msg = ''): void
    {
        echo $msg . PHP_EOL;
    }
}