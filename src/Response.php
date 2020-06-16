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
 * @property int    $code
 * @property array  $headers
 * @property string $body
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
     * @var JSON|null
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
     * @return array|int|string|string[]|null
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
     * @param array $requestData
     * @return $this
     */
    public function setRequest(array $requestData): self
    {
        $this->originalRequest = new JSON($requestData);
        return $this;
    }

    /**
     * @return JSON|null
     */
    public function getRequest(): ?JSON
    {
        return $this->originalRequest;
    }
}
