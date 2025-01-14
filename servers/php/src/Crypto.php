<?php declare(strict_types=1);

namespace AP\PerformanceTest;


use AP\Cryptographer\CipherAlgo;
use AP\Cryptographer\Cryptographer;
use AP\Structure\Singleton\Singleton;
use RuntimeException;

class Crypto
{
    use Singleton;

    const CipherAlgo ALGO = CipherAlgo::aes_256_gcm;

    public readonly Cryptographer $cryptographer;

    private static function getEnvStringOptional(string $name): ?string
    {
        return isset($_SERVER[$name]) && is_string($_SERVER[$name]) ? $_SERVER[$name] : null;
    }

    protected function __construct()
    {
        $passphrase_base64 = getenv('PASSPHRASE_BASE64');

        if (!is_string($passphrase_base64) || !strlen($passphrase_base64)) {
            throw new RuntimeException("pre-condition: install ENV value PASSPHRASE_BASE64");
        }

        $passphrase = base64_decode($passphrase_base64);

        $this->cryptographer = new Cryptographer(
            cipher_algo: self::ALGO,
            passphrase: $passphrase,
            tag_length: 16
        );
    }
}