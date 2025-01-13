<?php

use AP\PerformanceTest\Http\Connect\Request;

require __DIR__ . '/../vendor/autoload.php';

$request = new Request("https://leaptheory.com/api/test/a1");

echo "hello world: ". $request->run()->getRequestBody();