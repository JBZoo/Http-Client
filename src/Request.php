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

    private string            $url     = '';
    private null|array|string $args    = null;
    private string            $method  = self::GET;
    private array             $headers = [];
    private Options           $options;

    public function __construct(
        string $url = '',
        null|array|string $args = [],
        string $method = self::DEFAULT_METHOD,
        array $headers = [],
        array|Options $options = [],
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

    public function setUrl(string $url): self
    {
        $this->url = \trim($url);

        return $this;
    }

    public function setArgs(null|array|string $args): self
    {
        $this->args = $args;

        return $this;
    }

    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;

        return $this;
    }

    public function setMethod(string $method): self
    {
        $method = \strtoupper(\trim($method));
        $method = $method === '' ? self::DEFAULT_METHOD : $method;

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

    public function getArgs(): null|array|string
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
