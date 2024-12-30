<?php declare(strict_types=1);

namespace AP\PerformanceTest\Response\Layout;

use AP\PerformanceTest\Response\Interface\ToArray;
use AP\PerformanceTest\Response\Interface\ToString;

readonly class Json
{
    public function __construct(
        public ToArray|ToString $response,
    )
    {
    }

    public static function staticRender(ToArray|ToString $response): void
    {
        (new self($response))->render();
    }

    public function render(): void
    {
        header("content-type: application/json");
        if ($this->response instanceof ToString) {
            echo $this->response->getString();
        } elseif ($this->response instanceof ToArray) {
            echo json_encode($this->response->getArray());
        }
    }
}