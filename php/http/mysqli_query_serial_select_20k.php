<?php

use AP\PerformanceTest\Db\Db;
use AP\PerformanceTest\Response\Layout\Json;
use AP\PerformanceTest\Response\Response\PerformanceTestSelect;

require __DIR__ . '/../vendor/autoload.php';

microtime(true);

$db = Db::getInstance()->connection;

$first_id   = 1;
$iterations = 20000;

$start = microtime(true);
for ($i = $first_id; $i < $iterations + $first_id; $i++) {
    $db->query("select * from _sandbox where id=$i");
}
$duration = (microtime(true) - $start);


Json::staticRender(
    response: new PerformanceTestSelect(
        driver: "mysqli",
        method: "query",
        threads: 1,
        batch_size: 1,
        data_size: $iterations,
        columns: 9,
        duration: $duration
    )
);