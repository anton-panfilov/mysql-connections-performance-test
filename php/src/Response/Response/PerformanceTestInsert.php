<?php declare(strict_types=1);

namespace AP\PerformanceTest\Response\Response;

use AP\PerformanceTest\Response\Interface\ToArray;

readonly class PerformanceTestInsert implements ToArray
{
    public function __construct(
        public string $driver,
        public string $method,
        public int    $threads,
        public int    $batch_size,
        public int    $data_size,
        public float  $duration,
    )
    {
    }

    public function getArray(): array
    {
        return [
            "language"   => "php",
            "test"       => "insert",
            "driver"     => $this->driver,
            "method"     => $this->method,
            "threads"    => $this->threads,
            "batch_size" => $this->batch_size,
            "data_size"  => $this->data_size,
            "duration"   => $this->duration
        ];
    }
}