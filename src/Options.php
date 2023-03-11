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

use JBZoo\Data\Data;

use function JBZoo\Utils\bool;
use function JBZoo\Utils\int;

final class Options
{
    public const DEFAULT_DRIVER          = 'Guzzle';
    public const DEFAULT_TIMEOUT         = 10;
    public const DEFAULT_VERIFY          = true;
    public const DEFAULT_EXCEPTIONS      = false;
    public const DEFAULT_ALLOW_REDIRECTS = true;
    public const DEFAULT_MAX_REDIRECTS   = 10;
    public const DEFAULT_USER_AGENT      = 'JBZoo/Http-Client';

    private Data $options;

    public function __construct(array $options = [])
    {
        $this->options = new Data(\array_merge([
            'auth'            => [],
            'headers'         => [],
            'driver'          => self::DEFAULT_DRIVER,
            'timeout'         => self::DEFAULT_TIMEOUT,
            'verify'          => self::DEFAULT_VERIFY,
            'exceptions'      => self::DEFAULT_EXCEPTIONS,
            'allow_redirects' => self::DEFAULT_ALLOW_REDIRECTS,
            'max_redirects'   => self::DEFAULT_MAX_REDIRECTS,
            'user_agent'      => self::DEFAULT_USER_AGENT,
        ], $options));
    }

    public function getAuth(): ?array
    {
        return (array)$this->options->get('auth', []) ?: null;
    }

    public function getHeaders(): array
    {
        return (array)$this->options->get('headers', []);
    }

    public function getDriver(): string
    {
        return (string)$this->options->get('driver', self::DEFAULT_DRIVER, 'ucfirst');
    }

    public function getTimeout(): int
    {
        return int($this->options->get('timeout', self::DEFAULT_TIMEOUT, 'int'));
    }

    public function isVerify(): bool
    {
        return bool($this->options->get('verify', self::DEFAULT_VERIFY, 'bool'));
    }

    public function allowException(): bool
    {
        return bool($this->options->get('exceptions', self::DEFAULT_EXCEPTIONS, 'bool'));
    }

    public function isAllowRedirects(): bool
    {
        return bool($this->options->get('allow_redirects', self::DEFAULT_ALLOW_REDIRECTS, 'bool'));
    }

    public function getMaxRedirects(): int
    {
        return int($this->options->get('max_redirects', self::DEFAULT_MAX_REDIRECTS, 'int'));
    }

    public function getUserAgent(?string $suffix = null): string
    {
        $userAgent = (string)$this->options->get('user_agent', self::DEFAULT_USER_AGENT);
        if ($suffix && $userAgent === self::DEFAULT_USER_AGENT) {
            return "{$userAgent} ({$suffix})";
        }

        return $userAgent;
    }

    public function toArray(): array
    {
        return $this->options->getArrayCopy();
    }
}
