<?php declare(strict_types=1);

namespace AP\PerformanceTest\Db;

use AP\Structure\Singleton\Singleton;
use mysqli;

class Db
{
    use Singleton;

    public readonly mysqli $connection;

    private static function getEnvStringOptional(string $name): ?string
    {
        return isset($_SERVER[$name]) && is_string($_SERVER[$name]) ? $_SERVER[$name] : null;
    }

    protected function __construct()
    {
        $this->connection = mysqli_init();
        $this->connection->real_connect(
            hostname: self::getEnvStringOptional('DB_HOST'),
            username: self::getEnvStringOptional('DB_USER'),
            password: self::getEnvStringOptional('DB_PASS'),
            database: self::getEnvStringOptional('DB_BASE'),
        );
    }
}