<?php declare(strict_types=1);

namespace AP\PerformanceTest\Response\Response;

use AP\PerformanceTest\Response\Interface\ToArray;

readonly class PerformanceTestMigration implements ToArray
{
    public function __construct(
        public float $duration,
    )
    {
    }

    public function getArray(): array
    {
        return [
            "language" => "php",
            "test"     => "migration",
            "duration" => $this->duration
        ];
    }
}