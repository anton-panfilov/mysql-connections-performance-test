<?php

use AP\PerformanceTest\Db\Db;
use AP\PerformanceTest\Response\Layout\Json;
use AP\PerformanceTest\Response\Response\PerformanceTestMigration;

require __DIR__ . '/../vendor/autoload.php';

microtime(true);

$db = Db::getInstance()->connection;

$start = microtime(true);
$db->query(
"CREATE TABLE IF NOT EXISTS `_sandbox` (
      `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      `guid` binary(16) NOT NULL,
      `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `enum` tinyint(4) NOT NULL DEFAULT '1',
      `int` int(11) NOT NULL,
      `string` varchar(200) NOT NULL,
      `bool` tinyint(1) NOT NULL DEFAULT '0',
      `json` json NOT NULL,
      `encrypted_json` longblob NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8"
);
$duration = (microtime(true) - $start);

Json::staticRender(
    response: new PerformanceTestMigration(
        duration: $duration
    )
);