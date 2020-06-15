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

use JBZoo\Data\Data;

use function JBZoo\Utils\bool;
use function JBZoo\Utils\int;

/**
 * Class Options
 * @package JBZoo\HttpClient
 */
class Options
{
    public const DEFAULT_METHOD          = 'GET';
    public const DEFAULT_DRIVER          = 'Guzzle';
    public const DEFAULT_TIMEOUT         = 10;
    public const DEFAULT_VERIFY          = false;
    public const DEFAULT_EXCEPTIONS      = false;
    public const DEFAULT_ALLOW_REDIRECTS = true;
    public const DEFAULT_MAX_REDIRECTS   = 10;
    public const DEFAULT_USER_AGENT      = 'JBZoo/Http-Client';

    /**
     * @var array
     */
    protected $default = [
        'auth'            => [],
        'headers'         => [],
        'driver'          => self::DEFAULT_DRIVER,
        'timeout'         => self::DEFAULT_TIMEOUT,
        'verify'          => self::DEFAULT_VERIFY,
        'exceptions'      => self::DEFAULT_EXCEPTIONS,
        'allow_redirects' => self::DEFAULT_ALLOW_REDIRECTS,
        'max_redirects'   => self::DEFAULT_MAX_REDIRECTS,
        'user_agent'      => self::DEFAULT_USER_AGENT,
    ];

    /**
     * @var Data
     */
    protected $data;

    /**
     * Response constructor.
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->data = new Data(array_merge($this->default, $data));
    }

    /**
     * @return array|null
     */
    public function getAuth(): ?array
    {
        return (array)$this->data->get('auth', []) ?: null;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return (array)$this->data->get('headers', []);
    }

    /**
     * @return string
     */
    public function getDriver(): string
    {
        return (string)$this->data->get('driver', self::DEFAULT_DRIVER, 'ucfirst');
    }

    /**
     * @return int
     */
    public function getTimeout(): int
    {
        return int($this->data->get('timeout', self::DEFAULT_TIMEOUT, 'int'));
    }

    /**
     * @return bool
     */
    public function isVerify(): bool
    {
        return bool($this->data->get('verify', self::DEFAULT_VERIFY, 'bool'));
    }

    /**
     * @return bool
     */
    public function allowException(): bool
    {
        return bool($this->data->get('exceptions', self::DEFAULT_EXCEPTIONS, 'bool'));
    }

    /**
     * @return bool
     */
    public function isAllowRedirects(): bool
    {
        return bool($this->data->get('allow_redirects', self::DEFAULT_ALLOW_REDIRECTS, 'bool'));
    }

    /**
     * @return int
     */
    public function getMaxRedirects(): int
    {
        return int($this->data->get('max_redirects', self::DEFAULT_MAX_REDIRECTS, 'int'));
    }

    /**
     * @param string $suffix
     * @return string
     */
    public function getUserAgent(string $suffix): string
    {
        $packageName = (string)$this->data->get('user_agent', self::DEFAULT_USER_AGENT);
        return "{$packageName} ({$suffix})";
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->data->getArrayCopy();
    }
}
