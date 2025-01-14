<?php

use AP\PerformanceTest\Db\Db;
use AP\PerformanceTest\Db\DbPersistant;
use AP\PerformanceTest\Response\Layout\Json;
use AP\PerformanceTest\Response\Response\PerformanceTestSelect;
use AP\PerformanceTest\Helpers\DataSize;

require __DIR__ . '/../../../../../vendor/autoload.php';

microtime(true);



$first_id   = 1;
$iterations = DataSize::getDataSize();
$columns    = "*";

$start = microtime(true);
$db = Db::getInstance()->connection;
//$db = DbPersistant::getInstance()->connection; 
for ($i = $first_id; $i < $iterations + $first_id; $i++) {
    $db->execute_query("select * from _sandbox where id=$i");
}
$duration = (microtime(true) - $start);

Json::staticRender(
    response: new PerformanceTestSelect(
        driver: "mysqli",
        method: "execute_query",
        threads: 1,
        batch_size: 1,
        data_size: $iterations,
        columns: 9,
        duration: $duration
    )
);