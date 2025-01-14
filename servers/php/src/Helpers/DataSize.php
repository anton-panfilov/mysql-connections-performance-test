<?php

namespace AP\PerformanceTest\Helpers;

use Random\RandomException;
use RuntimeException;

class DataSize
{
    public static function getDataSize(): int
    {
        return isset($_GET['s'])
                        && is_numeric($_GET['s'])
                        && $_GET['s'] > 0
                        && $_GET['s'] <= 100000 ?
                 $_GET['s'] :
                 20000;
    }
}
