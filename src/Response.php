<?php

/**
 * JBZoo Toolbox - Http-Client.
 *
 * This file is part of the JBZoo Toolbox project.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @see        https://github.com/JBZoo/Http-Client
 */

declare(strict_types=1);

namespace JBZoo\HttpClient;

use JBZoo\Data\JSON;
use JBZoo\Utils\Xml;

/**
 * @property int        $code
 * @property array      $headers
 * @property string     $body
 * @property null|float $time
 */
class Response
{
    protected int      $internalCode    = 0;
    protected array    $internalHeaders = [];
    protected ?string  $internalBody    = null;
    protected ?JSON    $parsedJsonData  = null;
    protected ?float   $time            = null;
    protected ?Request $originalRequest = null;

    /**
     * @return null|array|float|int|string|string[]
     */
    public function __get(string $name)
    {
        if ($name === 'code') {
            return $this->getCode();
        }

        if ($name === 'headers') {
            return $this->getHeaders();
        }

        if ($name === 'body') {
            return $this->getBody();
        }

        if ($name === 'time') {
            return $this->getTime();
        }

        throw new Exception("Property '{$name}' not defined");
    }

    public function setCode(int $code): self
    {
        $this->internalCode = $code;

        return $this;
    }

    public function getCode(): int
    {
        return $this->internalCode;
    }

    public function setHeaders(array $headers): self
    {
        $result = [];

        foreach ($headers as $key => $value) {
            if (\is_array($value)) {
                $value = \implode(';', $value);
            }

            $result[$key] = $value;
        }

        $this->internalHeaders = $result;

        return $this;
    }

    public function getHeaders(): array
    {
        return $this->internalHeaders;
    }

    public function setBody(string $body): self
    {
        $this->internalBody = $body;
        $this->parsedJsonData = null;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->internalBody;
    }

    public function getJSON(): ?JSON
    {
        if ($this->parsedJsonData === null && $this->internalBody) {
            $this->parsedJsonData = new JSON($this->internalBody);
        }

        return $this->parsedJsonData;
    }

    public function getXml(): JSON
    {
        try {
            $xmlAsArray = Xml::dom2Array(Xml::createFromString($this->internalBody));
        } catch (\Exception $exception) {
            throw new Exception(
                "Can't parse xml document from HTTP response. " .
                "Details: {$exception->getMessage()}",
            );
        }

        return new JSON($xmlAsArray);
    }

    public function getHeader(string $headerKey, bool $ignoreCase = true): ?string
    {
        if ($ignoreCase) {
            $headers = [];
            $headerKey = \strtolower($headerKey);

            foreach ($this->getHeaders() as $key => $value) {
                $key = \strtolower((string)$key);
                $headers[$key] = $value;
            }
        } else {
            $headers = $this->getHeaders();
        }

        return $headers[$headerKey] ?? null;
    }

    public function setRequest(Request $request): self
    {
        $this->originalRequest = $request;

        return $this;
    }

    public function getRequest(): ?Request
    {
        return $this->originalRequest;
    }

    public function getTime(): ?float
    {
        return $this->time;
    }

    public function setTime(float $time): self
    {
        $this->time = $time;

        return $this;
    }

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
            ],
        ];
    }
}
