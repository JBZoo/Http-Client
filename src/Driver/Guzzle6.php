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
 * Class Guzzle6
 * @package JBZoo\HttpClient
 */
class Guzzle6 extends Guzzle
{
    /**
     * @inheritdoc
     */
    public function request($url, $args, $method, Options $options)
    {
        $client = new Client();

        $headers               = $options->getHeaders();
        $headers['User-Agent'] = $options->getUserAgent('Guzzle6');

        $body = $formParams = null;
        if ('GET' !== $method) {
            if (is_array($args)) {
                $formParams = $args;
            } else {
                $body = $args;
            }
        }

        $httpResult = $client->request($method, $url, array(
            'form_params'     => $formParams,
            'body'            => $body,
            'headers'         => $headers,
            'connect_timeout' => $options->getTimeout(),
            'timeout'         => $options->getTimeout(),
            'verify'          => $options->isVerify(),
            'exceptions'      => $options->isExceptions(),
            'auth'            => $options->getAuth(),
            'allow_redirects' => $this->_getAllowRedirects($options)
        ));

        return array(
            $httpResult->getStatusCode(),
            $httpResult->getHeaders(),
            $httpResult->getBody()->getContents()
        );
    }
}
