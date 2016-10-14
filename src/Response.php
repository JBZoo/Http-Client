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
     * @var null|JSON
     */
    protected $_jsonData = null;

    /**
     * Response constructor.
     * @param array|string $data
     */
    public function __construct($data = array())
    {
        $data['code']    = 0;
        $data['headers'] = array();
        $data['body']    = '';
        $this->_jsonData = null;

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
        $this->_jsonData = null; // Force update getJSON() result

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
        if (null === $this->_jsonData) {
            $this->_jsonData = new JSON($this->get('body'));
            $this->_jsonData->setFlags(\ArrayObject::ARRAY_AS_PROPS); // For JBZoo/Data less 1.4.2
        }

        return $this->_jsonData;
    }
     
    /**
     * @return JSON from XML
     */  
    
    public function parseXml()
    {
        $bodyxml = simplexml_load_string($this->get('body', null));
        return new JSON($bodyxml);
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
