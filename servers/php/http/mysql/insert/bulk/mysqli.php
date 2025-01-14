<?php

use AP\PerformanceTest\Crypto;
use AP\PerformanceTest\Db\Db;
use AP\PerformanceTest\Helpers\StringsHelper;
use AP\PerformanceTest\Helpers\UUIDv4;
use AP\PerformanceTest\Response\Layout\Json;
use AP\PerformanceTest\Response\Response\PerformanceTestInsert;
use AP\PerformanceTest\Helpers\DataSize;

require __DIR__ . '/../../../../vendor/autoload.php';

microtime(true);

$cryptographer = Crypto::getInstance()->cryptographer;

$db = Db::getInstance()->connection;
$iterations = DataSize::getDataSize();
$data = [];
for ($i = 0; $i < $iterations; $i++) {
    $data[] = [
        "guid"           => UUIDv4::pack(UUIDv4::create()),
        "created"        => date("Y-m-d H:i:s"),
        "enum"           => 2,
        "int"            => rand(100000, 999999),
        "string"         => StringsHelper::randomString(10),
        "bool"           => true,
        "json"           => json_encode(["foo" => "boo"]),
        "encrypted_json" => $cryptographer->encrypt("some secure info")->serialize(),
    ];
}

$query = "INSERT INTO `_sandbox` (";
$add1 = false;
foreach ($data[0] as $name => $t) {
    if($add1){
        $query .= ",";
    }
    $add1 = true;
    $query .= "`$name`";
}
$query .= ") VALUES ";
$add1 = false;
foreach ($data as $el) {
    if($add1){
        $query .= ",";
    }
    $add1 = true;

    $query .= "(";
    $add2 = false;
    foreach ($el as $v) {
        if($add2){
            $query .= ",";
        }
        $add2 = true;

        if (is_bool($v)) {
            $query .= $v ? "1" : "0";
        } elseif (is_numeric($v)) {
            $query .= (string)$v;
        } elseif (is_string($v)) {
            $query .= '"' . mysqli_real_escape_string($db, $v) . '"';
        } else {
            throw new UnexpectedValueException();
        }
    }
    $query .= ")";
}

$start = microtime(true);
$db->query($query);
$duration = (microtime(true) - $start);

Json::staticRender(
    response: new PerformanceTestInsert(
        driver: "mysqli",
        method: "query",
        threads: "1",
        batch_size: $iterations,
        data_size: $iterations,
        duration: $duration
    )
);