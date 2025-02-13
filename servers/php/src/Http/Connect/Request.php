<?php

namespace AP\PerformanceTest\Http\Connect;

use AP\PerformanceTest\Http\Connect\Exception\NoFinishedException;
use CurlHandle;
use SimpleXMLElement;

class Request
{
    protected bool $started  = false;
    protected bool $finished = false;

    /**
     * @var CurlHandle|null
     */
    protected ?CurlHandle $ch = null;

    protected ?string $asyncName     = null;
    protected ?string $response_head = "";
    protected ?string $response_body = null;
    protected ?string $request_body  = null;
    protected ?float  $waitTime      = null;
    protected ?int    $asyncErrno    = null;

    protected float $timeout;

    protected array $headers = [];
    protected array $options = [
        CURLOPT_POST           => false,
        CURLOPT_FAILONERROR    => false,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS      => 16,
    ];

    public function __construct(string $url)
    {
        $this->setOption(CURLOPT_URL, $url);
        $this->setTimeout(30);
    }

    public function addHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function authorizationBearer(string $token): self
    {
        $this->addHeader("Authorization", "Bearer " . $token);
        return $this;
    }

    public function authorizationBasic(string $clientId, string $clientSecret): static
    {
        return $this->addHeader('Authorization', 'Basic ' . base64_encode("$clientId:$clientSecret"));
    }

    public function headersLog(bool $turnOn = true): self
    {
        return $this
            ->setOption(CURLOPT_HEADER, $turnOn)
            ->setOption(CURLINFO_HEADER_OUT, $turnOn);
    }

    public function setOption($option, $value): self
    {
        if (is_null($value)) {
            if (isset($this->options[$option])) {
                unset($this->options[$option]);
            }
        } else {
            $this->options[$option] = $value;
        }
        return $this;
    }

    public function removeOption($option): self
    {
        return $this->setOption($option, null);
    }

    public function setBodyString(string $body): self
    {
        $this->request_body = $body;
        return $this;
    }

    public function setBodyJSON($json, string $contentType = 'application/json'): self
    {
        return $this
            ->setBodyString(is_array($json) ? json_encode($json) : $json)
            ->addHeader('Content-Type', $contentType);
    }

    public function setBodyPost($data, string $contentType = 'application/x-www-form-urlencoded'): self
    {
        return $this
            ->setBodyString(is_array($data) ? http_build_query($data) : $data)
            ->addHeader('Content-Type', $contentType);
    }

    public function setBodyXML($xml, string $contentTypeHeader = 'text/xml; charset=utf-8'): static
    {
        if ($xml instanceof SimpleXMLElement) {
            $xml = $xml->asXML();
        }
        $this->request_body = (string)$xml;
        return $this
            ->addHeader('Content-Type', $contentTypeHeader);
    }

    public function setTimeout(float $timeout): self
    {
        $this->timeout = $timeout;
        $intTimeout    = (int)$timeout;
        if ((float)$intTimeout == $timeout) {
            $this->setOption(CURLOPT_TIMEOUT, $intTimeout);
            $this->removeOption(CURLOPT_TIMEOUT_MS);
        } else {
            $this->setOption(CURLOPT_TIMEOUT_MS, ceil($timeout * 1000));
            $this->removeOption(CURLOPT_TIMEOUT);
        }
        return $this;
    }

    public function makeCurlOptions(bool $useCURLTimeout = true): array
    {
        $headers = [];
        if (count($this->headers)) {
            foreach ($this->headers as $k => $v) {
                $headers[] = "$k:$v";
            }
        }

        $options                     = $this->options;
        $options[CURLOPT_HTTPHEADER] = $headers;

        if (is_string($this->request_body)) {
            $options[CURLOPT_POSTFIELDS] = $this->request_body;
        }

        if (!$useCURLTimeout) {
            $options[CURLOPT_TIMEOUT]    = 0;
            $options[CURLOPT_TIMEOUT_MS] = 0;
        }

        return $options;
    }

    /**
     * @param bool $useCURLTimeout
     * @return CurlHandle|false|null
     */
    public function getCurlHandler(bool $useCURLTimeout = true): CurlHandle|bool|null
    {
        if (is_null($this->ch)) {
            $this->ch = curl_init();
            curl_setopt_array($this->ch, $this->makeCurlOptions($useCURLTimeout));
        }
        return $this->ch;
    }

    /**
     * @throws Exception\CurlErrorException
     * @throws Exception\DuplicateFinishException
     * @throws Exception\DuplicateStartException
     * @throws Exception\NoStartException
     */
    public function run(): self
    {
        $start    = microtime(1);
        $this->ch = $this->getCurlHandler();

        $this->triggerStarted();

        $fullResponse = curl_exec($this->ch);

        if ($fullResponse === false) {
            throw new Exception\CurlErrorException(curl_error($this->ch), curl_errno($this->ch));
        }
        $this->setResponseFull($fullResponse);
        $this->triggerFinished(microtime(true) - $start);

        return $this;
    }

    public function getFullInfo()
    {
        return curl_getinfo($this->ch);
    }

    public function getErrorMessage(): ?string
    {
        return is_int($this->asyncErrno) ?
            curl_strerror($this->asyncErrno) :
            curl_error($this->ch);
    }

    public function getErrorNumber(): int
    {
        return is_int($this->asyncErrno) ?
            $this->asyncErrno :
            curl_errno($this->ch);
    }

    public function getRuntime()
    {
        return curl_getinfo($this->ch, CURLINFO_TOTAL_TIME);
    }

    public function getResponseHttpCode(): string
    {
        return curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
    }

    private function setResponseFull($fullResponse): void
    {
        if (isset($this->options[CURLOPT_HEADER]) &&
            (
                $this->options[CURLOPT_HEADER] === true ||
                (is_numeric($this->options[CURLOPT_HEADER]) && $this->options[CURLOPT_HEADER] != 0)
            )
        ) {
            $response_header_size = curl_getinfo($this->ch, CURLINFO_HEADER_SIZE);
            $this->response_head  = trim(substr($fullResponse, 0, $response_header_size));
            $this->response_body  = substr($fullResponse, $response_header_size);
        } else {
            $this->response_body = $fullResponse;
        }
    }

    /**
     * @throws Exception\CurlHandleTypeException
     */
    public function loadResponse(): self
    {
        if (is_null($this->response_body)) {
            $phpVersion = substr(phpversion(), 0, 1);
            if ($phpVersion == 8 && !($this->ch instanceof CurlHandle)) {
                throw new Exception\CurlHandleTypeException("object is not ready");
            }
            $this->setResponseFull(curl_multi_getcontent($this->ch));
        }
        return $this;
    }

    /**
     * @throws Exception\CurlHandleTypeException
     */
    public function getResponseHead(): string
    {
        return $this->loadResponse()->response_head;
    }

    /**
     * @throws Exception\CurlHandleTypeException
     */
    public function getResponseBoby(): string
    {
        return $this->loadResponse()->response_body;
    }

    /**
     * @throws Exception\CurlHandleTypeException
     */
    public function getResponseBobyJSONDecode()
    {
        return json_decode(
            json: $this->getResponseBoby(),
            associative: true
        );
    }

    public function getRequestBody(): ?string
    {
        return $this->request_body;
    }

    public function getRequestHead(): string
    {
        return trim(curl_getinfo($this->ch, CURLINFO_HEADER_OUT));
    }

    public function getURL(): ?string
    {
        return $this->options[CURLOPT_URL] ?? null;
    }

    /**
     * @throws Exception\CurlHandleTypeException
     */
    public function getLogArray(): array
    {
        return [
            'url'           => $this->options[CURLOPT_URL] ?? null,
            'response_head' => $this->getResponseHead(),
            'response_body' => $this->getResponseBoby(),
            'request_head'  => $this->getRequestHead(),
            'request_body'  => $this->getRequestBody(),
            'runtime'       => $this->getRuntime(),
        ];
    }

    /**
     * @throws Exception\DuplicateStartException
     * @throws Exception\InvalidNameException
     * @throws Exception\DuplicateNameException
     */
    public function addToAsyncPool(?string $name = null): string
    {
        return AsyncPool::getInstance()->addRequest($this, $name);
    }

    /**
     * @throws Exception\CurlErrorException
     * @throws Exception\DuplicateFinishException
     * @throws Exception\DuplicateNameException
     * @throws Exception\DuplicateStartException
     * @throws Exception\InvalidNameException
     * @throws Exception\NameNotFoundException
     * @throws Exception\NoStartException
     */
    public function asyncWait(bool $curlErrorException = true, ?float $wait_seconds_limit = null): self
    {
        if (is_null($this->asyncName)) {
            if (is_null($this->waitTime)) {
                $this->addToAsyncPool();
            } else {
                return $this;
            }
        }

        return AsyncPool::getInstance()->wait(
            name: $this->asyncName,
            curlErrorException: $curlErrorException,
            wait_seconds_limit: $wait_seconds_limit
        );
    }

    public function isSyncMode(): bool
    {
        return is_null($this->asyncName);
    }

    /**
     * @throws Exception\DuplicateStartException
     */
    public function triggerStarted(?string $asyncName = null): self
    {
        if ($this->started) {
            throw new Exception\DuplicateStartException("dup triggerStarted");
        }

        $this->started   = true;
        $this->asyncName = $asyncName;

        return $this;
    }

    /**
     * @throws Exception\NoStartException
     * @throws Exception\DuplicateFinishException
     */
    public function triggerFinished(float $waitTimeSeconds, ?int $curlErrno = null): self
    {
        if (!$this->started) {
            throw new Exception\NoStartException("not triggerStarted");
        } elseif ($this->finished) {
            throw new Exception\DuplicateFinishException("dup triggerFinished: " . $this->getAsyncName() . " :: " . $this->getURL() . " :: " . $curlErrno . " :: " . $waitTimeSeconds);
        }

        $this->asyncErrno = $curlErrno;
        $this->finished   = true;
        $this->waitTime   = $waitTimeSeconds;

        return $this;
    }

    /**
     * @return $this
     * @throws Exception\CurlErrorException
     */
    public function getObjetOrException(): self
    {
        if (is_int($this->asyncErrno) && $this->asyncErrno != 0) {
            $error = curl_strerror($this->asyncErrno);
            throw new Exception\CurlErrorException(
                is_null($error) ? "unknown curl error: $this->asyncErrno" : $error,
                $this->asyncErrno
            );
        }
        return $this;
    }

    public function getAsyncName(): ?string
    {
        return $this->asyncName;
    }

    public function isFinished(): bool
    {
        return $this->finished;
    }

    /**
     * @throws NoFinishedException
     */
    public function getWaitTime(): float
    {
        if (is_null($this->waitTime)) {
            throw new Exception\NoFinishedException("request don't finished");
        }
        return $this->waitTime;
    }

    public function getTimeout(): float
    {
        return $this->timeout;
    }

    /**
     * @param                  $array
     * @param SimpleXMLElement $xml
     */
    public function array_to_xml($array, SimpleXMLElement &$xml): void
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                if (!is_numeric($key)) {
                    $subnode = $xml->addChild($key);
                    self::array_to_xml($value, $subnode);
                } else {
                    self::array_to_xml($value, $subnode);
                }
            } else {
                $xml->addChild($key, htmlspecialchars($value));
            }
        }
    }
}
