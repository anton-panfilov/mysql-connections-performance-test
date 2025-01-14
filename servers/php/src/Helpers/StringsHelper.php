<?php

namespace AP\PerformanceTest\Helpers;

use Random\RandomException;
use RuntimeException;

class StringsHelper
{
    const string symbolsAlphaNumeric = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

    /**
     * @throws RandomException
     * @throws RuntimeException
     */
    public static function randomString(int $len, string $symbols = self::symbolsAlphaNumeric): string
    {
        $symbols_len = strlen($symbols);
        if (!$symbols_len) {
            throw new RuntimeException("pre-condition: empty symbols");
        }

        $h = '';
        for ($i = 0; $i < $len; $i++) {
            $h .= $symbols[random_int(0, $symbols_len - 1)];
        }
        return $h;
    }
}
