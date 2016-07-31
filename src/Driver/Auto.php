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

namespace JBZoo\HttpClient\Driver;

use JBZoo\HttpClient\Exception;
use JBZoo\HttpClient\Options;
use JBZoo\Utils\Env;

/**
 * Class Auto
 * @package JBZoo\HttpClient
 */
class Auto extends Driver
{
    /**
     * @inheritdoc
     */
    public function request($url, $args, $method, Options $options)
    {
        if (class_exists('\GuzzleHttp\Client')
            && (version_compare(Env::getVersion(), '5.3', '>') || Env::isHHVM())
        ) {
            if (method_exists('\GuzzleHttp\Client', 'request')) {
                $client = new Guzzle6($options);

            } elseif (method_exists('\GuzzleHttp\Client', 'createRequest')) {
                $client = new Guzzle5($options);

            } else {
                throw new Exception('JBZoo/HttpClient: Supported Guzzle version driver not found!');
            }

        } else {
            $client = new Rmccue($options);
        }

        return $client->request($url, $args, $method, $options);
    }
}
