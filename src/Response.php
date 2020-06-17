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

namespace JBZoo\HttpClient;

use JBZoo\Data\JSON;

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
    private $internalCode = 0;

    /**
     * @var string[]
     */
    private $internalHeaders = [];

    /**
     * @var string|null
     */
    private $internalBody;

    /**
     * @var JSON|null
     */
    private $parsedJsonData;

    /**
     * @var float|null
     */
    private $time;

    /**
     * @var Request|null
     */
    private $originalRequest;

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
        return [
            'request'  => $this->getRequest()->toArray(),
            'response' => [
                'code'    => $this->getCode(),
                'body'    => $parseJson ? $this->getBody() : $this->getJSON()->getArrayCopy(),
                'headers' => $this->getHeaders(),
                'time'    => $this->getTime(),
            ]
        ];
    }
}
