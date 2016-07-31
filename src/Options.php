<?php
/**
 * JBZoo Http-Client
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package   Http-Client
 * @license   MIT
 * @copyright Copyright (C) JBZoo.com,  All rights reserved.
 * @link      https://github.com/JBZoo/Http-Client
 */

namespace JBZoo\HttpClient;

use JBZoo\Data\JSON;

/**
 * Class Options
 * @package JBZoo\HttpClient
 */
class Options extends JSON
{
    const DEFAULT_METHOD          = 'GET';
    const DEFAULT_DRIVER          = 'Auto';
    const DEFAULT_TIMEOUT         = 10;
    const DEFAULT_VERIFY          = false;
    const DEFAULT_EXCEPTIONS      = false;
    const DEFAULT_ALLOW_REDIRECTS = true;
    const DEFAULT_MAX_REDIRECTS   = 10;
    const DEFAULT_USER_AGENT      = 'JBZoo/Http-Client v1.x-dev';

    /**
     * @var array
     */
    protected $_default = array(
        'auth'            => array('', ''),
        'headers'         => array(),
        'driver'          => self::DEFAULT_DRIVER,
        'timeout'         => self::DEFAULT_TIMEOUT,
        'verify'          => self::DEFAULT_VERIFY,
        'exceptions'      => self::DEFAULT_EXCEPTIONS,
        'allow_redirects' => self::DEFAULT_ALLOW_REDIRECTS,
        'max_redirects'   => self::DEFAULT_MAX_REDIRECTS,
        'user_agent'      => self::DEFAULT_USER_AGENT,
    );

    /**
     * Response constructor.
     * @param array|string $data
     */
    public function __construct($data = array())
    {
        foreach ($this->_default as $key => $value) {
            $this[$key] = $value;
        }

        parent::__construct($data);
    }

    /**
     * @return array
     */
    public function getAuth()
    {
        return $this->get('auth', array('', ''));
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->get('headers', array());
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
    public function getUserAgent()
    {
        return $this->get('user_agent', self::DEFAULT_USER_AGENT);
    }
}
