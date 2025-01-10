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

    public static function makeNewConnection(): mysqli
    {
        $connection = mysqli_init();
        $connection->real_connect(
            hostname: self::getEnvStringOptional('DB_HOST'),
            username: self::getEnvStringOptional('DB_USER'),
            password: self::getEnvStringOptional('DB_PASS'),
            database: self::getEnvStringOptional('DB_BASE'),
        );
        return $connection;
    }

    /**
     * @param int $count
     * @return array<mysqli>
     */
    public static function makeManyConnections(int $count): array
    {
        $connections = [];
        for ($i = 0; $i < $count; $i++) {
            $connections[$i] = Db::makeNewConnection();
        }
        return $connections;
    }
}