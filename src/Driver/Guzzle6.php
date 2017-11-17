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

use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use GuzzleHttp\Psr7\Response;
use JBZoo\HttpClient\Options;
use JBZoo\Utils\Url;

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

        $httpResult = $client->request($method, $url, $this->_getClientOptions($options, $method, $args));

        return array(
            $httpResult->getStatusCode(),
            $httpResult->getHeaders(),
            $httpResult->getBody()->getContents()
        );
    }

    /**
     * @inheritdoc
     */
    public function multiRequest(array $urls)
    {
        $client = new Client();

        $promises = array();
        foreach ($urls as $urlName => $urlData) {

            if (is_string($urlData)) {
                $urlData = array($urlData, array());
            }

            $urlOptions = new Options($urlData[1]);

            $method = $urlOptions->get('method', 'GET', 'up');
            $args   = $urlOptions->get('args');
            $url    = 'GET' === $method ? Url::addArg((array)$args, $urlData[0]) : $urlData[0];

            $promises[$urlName] = $client->requestAsync(
                $method,
                $url,
                $this->_getClientOptions($urlOptions, $method, $args)
            );
        }

        $httpResults = Promise\unwrap($promises);

        /** @var string $resName */
        /** @var Response $httpResult */
        $result = array();
        foreach ($httpResults as $resName => $httpResult) {
            $result[$resName] = array(
                $httpResult->getStatusCode(),
                $httpResult->getHeaders(),
                $httpResult->getBody()->getContents()
            );
        }

        return $result;
    }

    /**
     * @param Options      $options
     * @param string       $method
     * @param string|array $args
     * @return array
     */
    protected function _getClientOptions(Options $options, $method, $args)
    {
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

        return array(
            'form_params'     => $formParams,
            'body'            => $body,
            'headers'         => $headers,
            'connect_timeout' => $options->getTimeout(),
            'timeout'         => $options->getTimeout(),
            'verify'          => $options->isVerify(),
            'exceptions'      => $options->isExceptions(),
            'auth'            => $options->getAuth(),
            'allow_redirects' => $this->_getAllowRedirects($options)
        );
    }
}
