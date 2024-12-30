<?php declare(strict_types=1);

namespace AP\PerformanceTest\Db;

use AP\Structure\Singleton\Singleton;
use Error;
use mysqli;

class ConnectionError extends Error
{
}