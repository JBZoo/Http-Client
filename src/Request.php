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

use JBZoo\Utils\Url;

/**
 * Class Request
 * @package JBZoo\HttpClient
 */
class Request
{
    public const GET    = 'GET';
    public const HEAD   = 'HEAD';
    public const PUT    = 'PUT';
    public const POST   = 'POST';
    public const PATCH  = 'PATCH';
    public const DELETE = 'DELETE';

    public const DEFAULT_METHOD = self::GET;

    /**
     * @var string
     */
    protected $url = '';

    /**
     * @var array|string|null
     */
    protected $args;

    /**
     * @var string
     */
    protected $method = self::GET;

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * @var Options
     */
    protected $options;

    /**
     * Request constructor.
     *
     * @param string            $url
     * @param array|string|null $args
     * @param string            $method
     * @param array             $headers
     * @param Options|array     $options
     */
    public function __construct(
        string $url = '',
        $args = [],
        string $method = self::DEFAULT_METHOD,
        array $headers = [],
        $options = []
    ) {
        $this->setUrl($url);
        $this->setArgs($args);
        $this->setMethod($method);
        $this->setHeaders($headers);

        $this->options = new Options();
        if (is_array($options)) {
            $this->setOptions($options);
        } else {
            $this->setOptions($options->toArray());
        }
    }

    /**
     * @param string $url
     * @return $this
     */
    public function setUrl(string $url): self
    {
        $this->url = trim($url);
        return $this;
    }

    /**
     * @param array|string|null $args
     * @return $this
     */
    public function setArgs($args): self
    {
        $this->args = $args;
        return $this;
    }

    /**
     * @param array $headers
     * @return $this
     */
    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * @param string $method
     * @return $this
     */
    public function setMethod(string $method): self
    {
        $method = strtoupper(trim($method)) ?: self::DEFAULT_METHOD;

        $validMethods = [
            self::GET,
            self::HEAD,
            self::PUT,
            self::POST,
            self::PATCH,
            self::DELETE
        ];
        if (!in_array($method, $validMethods, true)) {
            throw new Exception("Undefined HTTP method: {$method}");
        }

        $this->method = $method;
        return $this;
    }

    /**
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options): self
    {
        $this->options = new Options(array_merge($this->options->toArray(), $options));
        return $this;
    }

    /**
     * @return string
     */
    public function getUri(): string
    {
        if (self::GET === $this->method) {
            return Url::addArg((array)$this->args, $this->url);
        }

        return $this->url;
    }

    /**
     * @return array|string|null
     */
    public function getArgs()
    {
        return self::GET === $this->method ? null : $this->args;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return array_merge($this->options->getHeaders(), $this->headers);
    }

    /**
     * @return Options
     */
    public function getOptions(): Options
    {
        return new Options($this->options->toArray());
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'uri'     => $this->getUri(),
            'method'  => $this->getMethod(),
            'args'    => $this->getArgs(),
            'headers' => $this->getHeaders(),
            'options' => $this->getOptions()->toArray()
        ];
    }
}
