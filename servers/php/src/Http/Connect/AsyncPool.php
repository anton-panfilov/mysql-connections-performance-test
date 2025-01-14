<?php

namespace AP\PerformanceTest\Http\Connect;

use CurlMultiHandle;
use AP\PerformanceTest\Http\Connect\Exception\CurlErrorException;
use AP\PerformanceTest\Http\Connect\Exception\DuplicateFinishException;
use AP\PerformanceTest\Http\Connect\Exception\NameNotFoundException;
use AP\PerformanceTest\Http\Connect\Exception\NoStartException;

class AsyncPool
{
    private const DATA_TRANSFER_START_WAITING = 0;
    private const RUNTIME_STOCK_SECONDS       = 0.0001;
    private static ?AsyncPool $instance = null;

    public static function getInstance(): AsyncPool
    {
        if (is_null(self::$instance)) {
            self::$instance = new AsyncPool();
        }
        return self::$instance;
    }

    //////////////////////////////////////////////

    private CurlMultiHandle $multiHandle;

    private float $wait_total_time = 0;

    /**
     * @var Request[]
     */
    private array $requests = [];

    private array $curly = [];

    private array $curly_ids = [];

    public function __construct()
    {
        $this->multiHandle = curl_multi_init();
        curl_multi_setopt($this->multiHandle, CURLMOPT_MAX_TOTAL_CONNECTIONS, 12);
        curl_multi_setopt($this->multiHandle, CURLMOPT_MAX_HOST_CONNECTIONS, 12);
        curl_multi_setopt($this->multiHandle, CURLMOPT_MAXCONNECTS, 12);
        curl_multi_setopt($this->multiHandle, CURLMOPT_PIPELINING, CURLPIPE_NOTHING);
    }

    public function __destruct()
    {
        // if run curl_multi_close, services cannot end
        //curl_multi_close($this->multiHandle);
    }

    /**
     * @param Request $request
     * @param string|null $name
     * @param bool $safeStart
     * @return string
     * @throws Exception\DuplicateNameException
     * @throws Exception\DuplicateStartException
     * @throws Exception\InvalidNameException
     */
    public function addRequest(Request $request, ?string $name = null, bool $safeStart = true): string
    {
        $name                                      = $this->addRequestAddToRequestsArray($request, $name);
        $this->curly[$name]                        = $this->requests[$name]->getCurlHandler(false);
        $this->curly_ids[(int)$this->curly[$name]] = $name;

        $this->requests[$name]->triggerStarted($name);

        curl_multi_add_handle($this->multiHandle, $this->curly[$name]);
        curl_multi_exec($this->multiHandle, $running);

        if ($safeStart && self::DATA_TRANSFER_START_WAITING) {
            // an attempt to fix a bug that without curl_multi_exec in a loop, the process does not move
            // if the process of connecting to a remote server is slow
            usleep(self::DATA_TRANSFER_START_WAITING);
            curl_multi_exec($this->multiHandle, $running);
        }

        return $name;
    }

    public function exec(): int
    {
        curl_multi_exec($this->multiHandle, $running);
        return $running;
    }

    public function removeAllRequests(): void
    {
        $this->requests = [];
    }

    public function getAllRequests(): array
    {
        return $this->requests;
    }

    /**
     * @param array $names
     * @param bool $curlErrorException
     * @param bool $wait
     * @return Request[]
     * @throws Exception\CurlErrorException
     * @throws Exception\DuplicateFinishException
     * @throws Exception\NameNotFoundException
     * @throws Exception\NoStartException
     */
    public function checkOne(
        array  $names,
        bool   $curlErrorException = false,
        bool   $wait = true,
        ?float $wait_seconds_limit = null
    ): array
    {
        return $this->checkFew(
            names: $names,
            waitLimit: 1,
            curlErrorException: $curlErrorException,
            wait: $wait,
            wait_seconds_limit: $wait_seconds_limit
        );
    }

    /**
     * @param array $names
     * @param int $waitLimit
     * @param bool $curlErrorException
     * @param bool $wait
     * @param float|null $wait_seconds_limit
     * @return array
     * @throws CurlErrorException
     * @throws DuplicateFinishException
     * @throws NameNotFoundException
     * @throws NoStartException
     */
    public function checkFew(
        array  $names,
        int    $waitLimit,
        bool   $curlErrorException = false,
        bool   $wait = true,
        ?float $wait_seconds_limit = null
    ): array
    {
        $res = [];

        $names_key_equal_value = [];
        foreach ($names as $name) {
            $names_key_equal_value[$name] = $name;
        }
        $names = $names_key_equal_value;

        // check names
        foreach ($names as $name) {
            if (!isset($this->requests[$name])) {
                throw (new Exception\NameNotFoundException("name `$name` not found"));
            }
        }

        // check finished
        foreach ($names as $k => $name) {
            if ($this->requests[$name]->isFinished()) {
                unset($names[$k]);
                $res[$name] = $this->requests[$name];

                if ($curlErrorException) {
                    $res[$name] = $res[$name]->getObjetOrException();
                }

                if (count($res) == $waitLimit) {
                    return $res;
                }
            }
        }

        $start = microtime(true);
        do {
            curl_multi_exec($this->multiHandle, $running);
            do {
                $info = curl_multi_info_read($this->multiHandle, $nextInfo);
                if (isset($info['msg'], $info['result'], $info['handle'])
                    && $info['msg'] == CURLMSG_DONE
                ) {
                    $name = $this->curly_ids[(int)$info['handle']];
                    if (!$this->requests[$name]->isFinished()) {
                        $aRes = $this->requests[$name]->triggerFinished(
                            microtime(1) - $start,
                            (int)$info['result']
                        );

                        if (isset($names[$name])) {
                            $res[$name] = $aRes;
                            unset($names[$name]);
                        }

                        if (count($res) == $waitLimit) {
                            return $res;
                        }
                    }
//                    else {
//                        // todo: save timeout responses
//                    }
                }
            } while ($nextInfo > 0);

            foreach ($names as $k => $name) {
                $requestRuntime = $this->requests[$name]->getRuntime();
                if ($requestRuntime + self::RUNTIME_STOCK_SECONDS >= $this->requests[$name]->getTimeout()) {
                    $obj = $this->requests[$name]->triggerFinished(
                        microtime(1) - $start,
                        CURLE_OPERATION_TIMEDOUT
                    );

                    if ($curlErrorException) {
                        $obj = $obj->getObjetOrException();
                    }

                    unset($names[$k]);

                    $res[$name] = $obj;
                    if (count($res) == $waitLimit) {
                        return $res;
                    }
                }
            }

            if (!$wait) {
                return $res;
            }

            $next_wait_limit_seconds = null;
            if (is_float($wait_seconds_limit)) {
                $next_wait_limit_seconds = microtime(true) - $start;
                if ($next_wait_limit_seconds > $wait_seconds_limit) {
                    return $res;
                }
            }

            if ($running > 0) {
                $timeout = 1;
                // update if there is wait limit, and wait limit end before max loop delay
                if (is_float($next_wait_limit_seconds) && $next_wait_limit_seconds < $timeout) {
                    $timeout = $next_wait_limit_seconds;
                }

                // update if any request's timeout less than max loop limit
                foreach ($names as $name) {
                    $t = $this->requests[$name]->getTimeout() - $this->requests[$name]->getRuntime();
                    if ($t < $timeout) {
                        $timeout = $t;
                    }
                }

                $waitStart = microtime(true);
                curl_multi_select($this->multiHandle, $timeout);
                $waitTime              = microtime(true) - $waitStart;
                $this->wait_total_time += $waitTime;
            }
        } while ($running > 0);

        return $res;
    }

    /**
     * @param array $names
     * @return Request[]
     * @throws Exception\CurlErrorException
     * @throws Exception\DuplicateFinishException
     * @throws Exception\NameNotFoundException
     * @throws Exception\NoStartException
     */
    public function waitAny(array $names): array
    {
        return $this->checkFew(
            names: $names,
            waitLimit: 1
        );
    }

    /**
     * @return Request[]
     * @throws \Exception
     */
    public function waitAll(array $names): array
    {
        return $this->checkFew(
            names: $names,
            waitLimit: count($names)
        );
    }

    /**
     * @param string $name
     * @param bool $curlErrorException
     * @return Request
     * @throws Exception\CurlErrorException
     * @throws Exception\DuplicateFinishException
     * @throws Exception\NameNotFoundException
     * @throws Exception\NoStartException
     */
    public function wait(string $name, bool $curlErrorException = false, ?float $wait_seconds_limit = null): Request
    {
        return $this->checkFew(
            names: [$name],
            waitLimit: 1,
            curlErrorException: $curlErrorException,
            wait_seconds_limit: $wait_seconds_limit
        )[$name];
    }

    private int $requestIterator = 0;

    /**
     * @param Request $request
     * @param string|null $name
     * @return string
     * @throws Exception\DuplicateNameException
     * @throws Exception\InvalidNameException
     */
    private function addRequestAddToRequestsArray(Request $request, ?string $name = null): string
    {
        if (is_null($name)) {
            $name = "." . $this->requestIterator;
            $this->requestIterator++;
            $this->requests[$name] = $request;
            return $name;
        }

        if (str_starts_with($name, ".")) {
            throw (new Exception\InvalidNameException("name $name can not start as dot"));
        }

        if (isset($this->requests[$name])) {
            throw (new Exception\DuplicateNameException("duplicate name $name"));
        }
        $this->requests[$name] = $request;
        return $name;
    }

    public function getWaitTotalTime(): float
    {
        return $this->wait_total_time;
    }
}
