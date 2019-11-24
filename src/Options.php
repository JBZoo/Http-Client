<?php
/**
 * JBZoo Http-Client
 *
 * This file is part of the JBZoo CCK package.
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
 * Class Options
 * @package JBZoo\HttpClient
 */
class Options extends JSON
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
    protected $_default = [
        'auth'            => false,
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
     * Response constructor.
     * @param array|string $data
     */
    public function __construct($data = [])
    {
        $data = array_merge($this->_default, $data);
        parent::__construct($data);
    }

    /**
     * @return array
     */
    public function getAuth()
    {
        return $this->get('auth', false);
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->get('headers', []);
    }

    /**
     * @return string
     */
    public function getDriver()
    {
        return $this->get('driver', self::DEFAULT_DRIVER, 'ucfirst');
    }

    /**
     * @return int
     */
    public function getTimeout()
    {
        return $this->get('timeout', self::DEFAULT_TIMEOUT, 'int');
    }

    /**
     * @return bool
     */
    public function isVerify()
    {
        return $this->get('verify', self::DEFAULT_VERIFY, 'bool');
    }

    /**
     * @return bool
     */
    public function isExceptions()
    {
        return $this->get('exceptions', self::DEFAULT_EXCEPTIONS, 'bool');
    }

    /**
     * @return bool
     */
    public function isAllowRedirects()
    {
        return $this->get('allow_redirects', self::DEFAULT_ALLOW_REDIRECTS, 'bool');
    }

    /**
     * @return int
     */
    public function getMaxRedirects()
    {
        return $this->get('max_redirects', self::DEFAULT_MAX_REDIRECTS, 'int');
    }

    /**
     * @return string
     */
    public function getUserAgent($suffix)
    {
        return $this->get('user_agent', self::DEFAULT_USER_AGENT) . " ({$suffix})";
    }
}
