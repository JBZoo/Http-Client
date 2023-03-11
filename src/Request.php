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

use JBZoo\Utils\Url;

final class Request
{
    public const GET    = 'GET';
    public const HEAD   = 'HEAD';
    public const PUT    = 'PUT';
    public const POST   = 'POST';
    public const PATCH  = 'PATCH';
    public const DELETE = 'DELETE';

    public const DEFAULT_METHOD = self::GET;

    /** @var string */
    private $url = '';

    /** @var null|array|string */
    private $args;

    /** @var string */
    private $method = self::GET;

    /** @var array */
    private $headers = [];

    /** @var Options */
    private $options;

    /**
     * @param null|array|string $args
     * @param array|Options     $options
     */
    public function __construct(
        string $url = '',
        $args = [],
        string $method = self::DEFAULT_METHOD,
        array $headers = [],
        $options = [],
    ) {
        $this->setUrl($url);
        $this->setArgs($args);
        $this->setMethod($method);
        $this->setHeaders($headers);

        $this->options = new Options();
        if (\is_array($options)) {
            $this->setOptions($options);
        } else {
            $this->setOptions($options->toArray());
        }
    }

    /**
     * @return $this
     */
    public function setUrl(string $url): self
    {
        $this->url = \trim($url);

        return $this;
    }

    /**
     * @param  null|array|string $args
     * @return $this
     */
    public function setArgs($args): self
    {
        $this->args = $args;

        return $this;
    }

    /**
     * @return $this
     */
    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * @return $this
     */
    public function setMethod(string $method): self
    {
        $method = \strtoupper(\trim($method)) ?: self::DEFAULT_METHOD;

        $validMethods = [
            self::GET,
            self::HEAD,
            self::PUT,
            self::POST,
            self::PATCH,
            self::DELETE,
        ];
        if (!\in_array($method, $validMethods, true)) {
            throw new Exception("Undefined HTTP method: {$method}");
        }

        $this->method = $method;

        return $this;
    }

    /**
     * @return $this
     */
    public function setOptions(array $options): self
    {
        $this->options = new Options(\array_merge($this->options->toArray(), $options));

        return $this;
    }

    public function getUri(): string
    {
        if ($this->method === self::GET) {
            return Url::addArg((array)$this->args, $this->url);
        }

        return $this->url;
    }

    /**
     * @return null|array|string
     */
    public function getArgs()
    {
        return $this->method === self::GET ? null : $this->args;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getHeaders(): array
    {
        return \array_merge($this->options->getHeaders(), $this->headers);
    }

    public function getOptions(): Options
    {
        return new Options($this->options->toArray());
    }

    public function toArray(): array
    {
        return [
            'uri'     => $this->getUri(),
            'method'  => $this->getMethod(),
            'args'    => $this->getArgs(),
            'headers' => $this->getHeaders(),
            'options' => $this->getOptions()->toArray(),
        ];
    }
}
