<?php

use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server;

$host = '0.0.0.0';
$port = 80;

$http = new Server(
    host: $host,
    port: $port
);

$http->on('start', function (Server $server) use ($host, $port): void {
    echo "Swoole http server is started at http://{$host}:{$port}" . PHP_EOL;
});


$http->on('Request', function (Request $request, Response $response) {
    file_get_contents("https://leaptheory.com/api/test/a1");
    $response->header('Content-Type', 'text/html; charset=utf-8');
    $response->end('<h1>Hello Swoole</h1>');
});

$http->start();