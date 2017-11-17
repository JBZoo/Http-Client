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

use GuzzleHttp\Message\Response;
use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use JBZoo\HttpClient\Options;
use JBZoo\Utils\Url;

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

        $httpRequest = $client->createRequest($method, $url, $this->_getClientOptions($options, $method, $args));

        $httpResult = $client->send($httpRequest);

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

        $requests = array();
        foreach ($urls as $urlName => $urlData) {

            if (is_string($urlData)) {
                $urlData = array($urlData, array());
            }

            $urlOptions = new Options($urlData[1]);

            $method = $urlOptions->get('method', 'GET', 'up');
            $args   = $urlOptions->get('args');
            $url    = 'GET' === $method ? Url::addArg((array)$args, $urlData[0]) : $urlData[0];

            $requests[$urlName] = $client->createRequest(
                $method,
                $url,
                $this->_getClientOptions($urlOptions, $method, $args)
            );
        }

        $httpResults = Pool::batch($client, $requests);

        /** @var string $resName */
        /** @var Response $httpResult */
        $result = array();

        $index = 0;
        $keys  = array_keys($urls);
        foreach ($keys as $resName) {
            $httpResult       = $httpResults->offsetGet($index++);
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
        $headers['User-Agent'] = $options->getUserAgent('Guzzle5');

        return array(
            'body'            => 'GET' !== $method ? $args : null,
            'headers'         => $headers,
            'exceptions'      => $options->isExceptions(),
            'timeout'         => $options->getTimeout(),
            'verify'          => $options->isVerify(),
            'auth'            => $options->getAuth(),
            'allow_redirects' => $this->_getAllowRedirects($options)
        );
    }
}
