<?php

use AP\PerformanceTest\Db\Db;
use AP\PerformanceTest\Response\Layout\Json;
use AP\PerformanceTest\Response\Response\PerformanceTestSelect;

require __DIR__ . '/../vendor/autoload.php';

microtime(true);


$first_id   = 1;
$iterations = 20000;
$threads    = 10;
$duration   = 0;

$connections = Db::makeManyConnections($threads);

for ($i = 0; $i < 9; $i++) {
    $connections[$i]->query(
        "select * from _sandbox where id=" . ($i + 10),
        MYSQLI_STORE_RESULT | MYSQLI_ASYNC
    );
}

usleep(10000);

$links = $errors = $rejected = $connections;

var_export(mysqli::poll($links, $errors, $rejected, 0, 1));
var_export($links);
var_export($errors);
var_export($rejected);
//var_export($connections);

if (count($links)) {
    echo "\n----------------------------------\n";
    foreach ($links as $conn) {
        if ($conn instanceof mysqli) {
            try {
                $res = $conn->reap_async_query();
                echo "\n\n";
                echo ($conn->thread_id);
                echo "\n\n";
                var_export($res->fetch_all());
                //$res->close();
            } catch (mysqli_sql_exception $e) {
                echo "ERROR: {$e->getMessage()}\n";
            }
        } else {
            echo "ERROR";
        }
    }
    echo "\n----------------------------------\n";
    var_export(mysqli::poll($links, $errors, $rejected, 0, 1));
    var_export($links);
    var_export($errors);
    var_export($rejected);

    echo "\n----------------------------------\n";
}

$start = microtime(true);
$wait  = [];
for ($i = $first_id; $i < $iterations + $first_id; $i++) {

    //echo "$i\n";
    //    for ($j = 0; $j) {
    //
    //    }
    //    $connections[$i]->query(
    //        "select * from _sandbox where id=$i",
    //        MYSQLI_STORE_RESULT | MYSQLI_ASYNC
    //    );
    //
    //    $wait[] = $connections[$i];
}

//
//
//foreach ($wait as $conn) {
//    if ($conn instanceof mysqli) {
//        try {
//            $res = $conn->reap_async_query();
//            //var_export($res->fetch_all());
//        } catch (mysqli_sql_exception $e) {
//            echo "ERROR: {$e->getMessage()}\n";
//        }
//    } else {
//        echo "ERROR";
//    }
//}

//$connections["error"] = $wait["error"] = Db::makeNewConnection();
//$wait["error"]->query(
//    "select * from _sandbox_error where id=$i",
//    MYSQLI_STORE_RESULT | MYSQLI_ASYNC
//);

//$links = $errors = $rejected = $wait;
//var_dump(mysqli_poll($links, $errors, $rejected, 0, 1));
//var_dump($links);
//var_dump($errors);
//var_dump($rejected);
//
//foreach ($links as $conn){
//    if($conn instanceof mysqli) {
//        $res = $conn->reap_async_query();
//        if ($res instanceof mysqli_result) {
//            $all = $res->fetch_all();
//            var_export($all);
//        } else {
//            echo "ERROR";
//        }
//    }
//}
//$i = 0;
//while (count($wait)) {
//    next($wait);
//    $key = key($wait);
//    if (is_null(key($wait))) {
//        reset($wait);
//        $key = key($wait);
//    }
//    var_dump($key);
//    $conn = current($wait);
//    if ($conn instanceof mysqli) {
//        $res = $conn->reap_async_query();
//        if ($res instanceof mysqli_result) {
//            $all = $res->fetch_all();
//            unset($wait[$key]);
//        }
//    }
//
//    $i++;
//    if ($i > 10) {
//        break;
//    }
//}
$duration = (microtime(true) - $start);

//usleep(100000);
//
//for ($i = $first_id; $i < $iterations + $first_id; $i++) {
//
//    var_dump($connections[$i]->reap_async_query()->fetch_all());
//}

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