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

use GuzzleHttp\Client;
use JBZoo\HttpClient\Options;

/**
 * Class Guzzle5
 * @package JBZoo\HttpClient
 */
class Guzzle5 extends Guzzle
{
    /**
     * @inheritdoc
     */
    public function request($url, $args, $method, Options $options)
    {
        $client = new Client();

        $headers               = $options->getHeaders();
        $headers['User-Agent'] = $options->getUserAgent('Guzzle5');

        $httpRequest = $client->createRequest($method, $url, array(
            'body'            => 'GET' !== $method ? $args : null,
            'headers'         => $headers,
            'exceptions'      => $options->isExceptions(),
            'timeout'         => $options->getTimeout(),
            'verify'          => $options->isVerify(),
            'auth'            => $options->getAuth(),
            'allow_redirects' => $this->_getAllowRedirects($options)
        ));

        $httpResult = $client->send($httpRequest);

        return array(
            $httpResult->getStatusCode(),
            $httpResult->getHeaders(),
            $httpResult->getBody()->getContents()
        );
    }
}
