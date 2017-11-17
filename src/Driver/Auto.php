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

namespace JBZoo\HttpClient\Driver;

use JBZoo\HttpClient\Options;
use JBZoo\Utils\Sys;

/**
 * Class Auto
 *
 * @package JBZoo\HttpClient
 */
class Auto extends Driver
{
    /**
     * @inheritdoc
     */
    public function request($url, $args, $method, Options $options)
    {
        return $this->_getClient()->request($url, $args, $method, $options);
    }

    /**
     * @inheritdoc
     */
    public function multiRequest(array $urls)
    {
        return $this->_getClient()->multiRequest($urls);
    }

    /**
     * @return Driver
     */
    protected function _getClient()
    {
        if (class_exists('\GuzzleHttp\Client')
            && (version_compare(Sys::getVersion(), '5.3', '>') || Sys::isHHVM())
        ) {
            if (method_exists('\GuzzleHttp\Client', 'request')) {
                $client = new Guzzle6();

            } elseif (method_exists('\GuzzleHttp\Client', 'createRequest')) {
                $client = new Guzzle5();
            }
        }

        if (!isset($client)) { // Fallback driver
            $client = new Rmccue();
        }

        return $client;
    }
}
