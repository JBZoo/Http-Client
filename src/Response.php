<?php

/**
 * JBZoo Toolbox - Http-Client
 *
 * This file is part of the JBZoo Toolbox project.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Http-Client
 * @license    MIT
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/Http-Client
 */

declare(strict_types=1);

namespace JBZoo\HttpClient;

use JBZoo\Data\JSON;
use JBZoo\Utils\Xml;

/**
 * Class Response
 * @package JBZoo\HttpClient
 *
 * @property int        $code
 * @property array      $headers
 * @property string     $body
 * @property float|null $time
 */
class Response
{
    /**
     * @var int
     */
    protected $internalCode = 0;

    /**
     * @var string[]
     */
    protected $internalHeaders = [];

    /**
     * @var string|null
     */
    protected $internalBody;

    /**
     * @var JSON|null
     */
    protected $parsedJsonData;

    /**
     * @var float|null
     */
    protected $time;

    /**
     * @var Request|null
     */
    protected $originalRequest;

    /**
     * @param int $code
     * @return $this
     */
    public function setCode(int $code): self
    {
        $this->internalCode = $code;
        return $this;
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->internalCode;
    }

    /**
     * @param array $headers
     * @return $this
     */
    public function setHeaders(array $headers): self
    {
        $result = [];

        foreach ($headers as $key => $value) {
            if (is_array($value)) {
                $value = implode(';', $value);
            }

            $result[$key] = $value;
        }

        $this->internalHeaders = $result;
        return $this;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->internalHeaders;
    }

    /**
     * @param string $body
     * @return $this
     */
    public function setBody(string $body): self
    {
        $this->internalBody = $body;
        $this->parsedJsonData = null;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getBody(): ?string
    {
        return $this->internalBody;
    }

    /**
     * @return JSON|null
     */
    public function getJSON(): ?JSON
    {
        if (null === $this->parsedJsonData && $this->internalBody) {
            $this->parsedJsonData = new JSON($this->internalBody);
        }

        return $this->parsedJsonData;
    }

    /**
     * @return JSON
     */
    public function getXml(): JSON
    {
        try {
            $xmlAsArray = Xml::dom2Array(Xml::createFromString($this->internalBody));
        } catch (\Exception $exception) {
            throw new Exception(
                "Can't parse xml document from HTTP response. " .
                "Details: {$exception->getMessage()}"
            );
        }

        return new JSON($xmlAsArray);
    }

    /**
     * @param string $name
     * @return array|string|float|int|string[]|null
     */
    public function __get($name)
    {
        if ('code' === $name) {
            return $this->getCode();
        }

        if ('headers' === $name) {
            return $this->getHeaders();
        }

        if ('body' === $name) {
            return $this->getBody();
        }

        if ('time' === $name) {
            return $this->getTime();
        }

        throw new Exception("Property '{$name}' not defined");
    }

    /**
     * @param string $headerKey
     * @param bool   $ignoreCase
     * @return string|null
     */
    public function getHeader(string $headerKey, bool $ignoreCase = true): ?string
    {
        if ($ignoreCase) {
            $headers = [];
            $headerKey = strtolower($headerKey);
            foreach ($this->getHeaders() as $key => $value) {
                $key = strtolower($key);
                $headers[$key] = $value;
            }
        } else {
            $headers = $this->getHeaders();
        }

        return $headers[$headerKey] ?? null;
    }

    /**
     * @param Request $request
     * @return $this
     */
    public function setRequest(Request $request): self
    {
        $this->originalRequest = $request;
        return $this;
    }

    /**
     * @return Request|null
     */
    public function getRequest(): ?Request
    {
        return $this->originalRequest;
    }

    /**
     * @return float|null
     */
    public function getTime(): ?float
    {
        return $this->time;
    }

    /**
     * @param float $time
     * @return $this
     */
    public function setTime(float $time): self
    {
        $this->time = $time;
        return $this;
    }

    /**
     * @param bool $parseJson
     * @return array
     */
    public function toArray(bool $parseJson = false): array
    {
        $request = $this->getRequest();
        $requestArray = $request ? $request->toArray() : null;

        $body = $this->getBody();
        if ($parseJson && $jsonBody = $this->getJSON()) {
            $body = $jsonBody;
        }

        return [
            'request'  => $requestArray,
            'response' => [
                'code'    => $this->getCode(),
                'body'    => $body,
                'headers' => $this->getHeaders(),
                'time'    => $this->getTime(),
            ]
        ];
    }
}
