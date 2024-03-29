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
        $this->options = new Data(
            \array_merge([
                'auth'            => [],
                'headers'         => [],
                'driver'          => self::DEFAULT_DRIVER,
                'timeout'         => self::DEFAULT_TIMEOUT,
                'verify'          => self::DEFAULT_VERIFY,
                'exceptions'      => self::DEFAULT_EXCEPTIONS,
                'allow_redirects' => self::DEFAULT_ALLOW_REDIRECTS,
                'max_redirects'   => self::DEFAULT_MAX_REDIRECTS,
                'user_agent'      => self::DEFAULT_USER_AGENT,
            ], $options),
        );
    }

    public function getAuth(): ?array
    {
        $auth = $this->options->getArray('auth');

        return \count($auth) > 0 ? $auth : null;
    }

    public function getHeaders(): array
    {
        return $this->options->getArray('headers');
    }

    public function getDriver(): string
    {
        return \ucfirst($this->options->getString('driver', self::DEFAULT_DRIVER));
    }

    public function getTimeout(): int
    {
        return $this->options->getInt('timeout', self::DEFAULT_TIMEOUT);
    }

    public function isVerify(): bool
    {
        return $this->options->getBool('verify', self::DEFAULT_VERIFY);
    }

    public function allowException(): bool
    {
        return $this->options->getBool('exceptions', self::DEFAULT_EXCEPTIONS);
    }

    public function isAllowRedirects(): bool
    {
        return $this->options->getBool('allow_redirects', self::DEFAULT_ALLOW_REDIRECTS);
    }

    public function getMaxRedirects(): int
    {
        return $this->options->getInt('max_redirects', self::DEFAULT_MAX_REDIRECTS);
    }

    public function getUserAgent(?string $suffix = null): string
    {
        $userAgent = $this->options->getString('user_agent', self::DEFAULT_USER_AGENT);

        if (
            $suffix !== null
            && $suffix !== ''
            && $userAgent === self::DEFAULT_USER_AGENT
        ) {
            return "{$userAgent} ({$suffix})";
        }

        return $userAgent;
    }

    public function toArray(): array
    {
        return $this->options->getArrayCopy();
    }
}
