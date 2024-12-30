<?php

namespace AP\PerformanceTest\Helpers;

class UUIDv4
{
    public static function create(): string
    {
        if (function_exists('com_create_guid')) {
            return \com_create_guid();
        } else {
            $set_charid = strtoupper(md5(uniqid((string)rand(), true)));
            $set_hyphen = chr(45);
            return
                substr($set_charid, 0, 8) . $set_hyphen
                . substr($set_charid, 8, 4) . $set_hyphen
                . substr($set_charid, 12, 4) . $set_hyphen
                . substr($set_charid, 16, 4) . $set_hyphen
                . substr($set_charid, 20, 12);
        }
    }

    public static function pack(string $uuid)
    {
        return pack("H*", str_replace('-', '', $uuid));
    }

    public static function unpack($bin): string
    {
        return join("-", unpack("H8time_low/H4time_mid/H4time_hi/H4clock_seq_hi/H12clock_seq_low", $bin));
    }
}
