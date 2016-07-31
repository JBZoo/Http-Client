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

use JBZoo\Data\Data;
use JBZoo\Data\JSON;
use JBZoo\Utils\Filter;

/**
 * Class Response
 * @package JBZoo\HttpClient
 */
class Response extends Data
{
    /**
     * Response constructor.
     * @param array|string $data
     */
    public function __construct($data = array())
    {
        $data['code']    = 0;
        $data['headers'] = array();
        $data['body']    = '';

        parent::__construct($data);
    }

    /**
     * @param $code
     */
    public function setCode($code)
    {
        $this['code'] = Filter::int($code);
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->get('code', 0, 'int');
    }

    /**
     * @param $body
     */
    public function setBody($body)
    {
        $this['body'] = (string)$body;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->get('body', null);
    }

    /**
     * @return JSON
     */
    public function getJSON()
    {
        return new JSON($this->getBody());
    }

    /**
     * @param array $headers
     * @return array
     */
    public function setHeaders(array $headers)
    {
        $result = array();

        foreach ($headers as $key => $value) {

            $key = strtolower($key);
            if (is_array($value)) {
                $value = implode(';', $value);
            }

            $result[$key] = $value;
        }

        $this['headers'] = $result;
    }

    /**
     * @param string $key
     * @return string
     */
    public function getHeader($key)
    {
        return $this->find('headers.' . strtolower($key));
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->get('headers', array());
    }
}
