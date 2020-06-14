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
    protected $jsonData = null;

    /**
     * Response constructor.
     * @param array $data
     */
    public function __construct($data = [])
    {
        $data['code'] = 0;
        $data['headers'] = [];
        $data['body'] = '';
        $this->jsonData = null;

        parent::__construct($data);
    }

    /**
     * @param int $code
     */
    public function setCode($code): void
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
     * @param string $body
     */
    public function setBody($body): void
    {
        $this->jsonData = null; // Force update getJSON() result
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
        if (null === $this->jsonData) {
            $this->jsonData = new JSON($this->get('body'));
            $this->jsonData->setFlags(\ArrayObject::ARRAY_AS_PROPS); // For JBZoo/Data less 1.4.2
        }

        return $this->jsonData;
    }

    /**
     * @param array $headers
     * @return Response
     */
    public function setHeaders(array $headers): Response
    {
        $result = [];

        foreach ($headers as $key => $value) {
            $key = strtolower($key);
            if (is_array($value)) {
                $value = implode(';', $value);
            }

            $result[$key] = $value;
        }

        $this['headers'] = $result;

        return $this;
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
        return $this->get('headers', []);
    }
}
