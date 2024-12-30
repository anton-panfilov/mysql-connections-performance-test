<?php declare(strict_types=1);

namespace AP\PerformanceTest\Response\Response;

use AP\PerformanceTest\Response\Interface\ToArray;

readonly class PerformanceTestSelect implements ToArray
{
    public function __construct(
        public string $driver,
        public string $method,
        public int    $threads,
        public int    $batch_size,
        public int    $data_size,
        public int    $columns,
        public float  $duration,
    )
    {
    }

    public function getArray(): array
    {
        return [
            "language"   => "php",
            "test"       => "select",
            "driver"     => $this->driver,
            "method"     => $this->method,
            "threads"    => $this->threads,
            "batch_size" => $this->batch_size,
            "data_size"  => $this->data_size,
            "columns"    => $this->columns,
            "duration"   => $this->duration
        ];
    }
}