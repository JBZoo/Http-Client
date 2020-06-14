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

namespace JBZoo\HttpClient\Driver;

use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use GuzzleHttp\Psr7\Response;
use JBZoo\HttpClient\Options;
use JBZoo\Utils\Url;
use Throwable;

/**
 * Class Guzzle
 * @package JBZoo\HttpClient
 */
class Guzzle extends Driver
{
    /**
     * @inheritdoc
     */
    public function request($url, $args, $method, Options $options)
    {
        $client = new Client();

        $httpResult = $client->request(
            $method,
            $url,
            $this->getClientOptions($options, $method, $args)
        );

        return [
            $httpResult->getStatusCode(),
            $httpResult->getHeaders(),
            $httpResult->getBody()->getContents()
        ];
    }

    /**
     * @inheritdoc
     * @throws Throwable
     */
    public function multiRequest(array $urls)
    {
        $client = new Client();

        $promises = [];
        foreach ($urls as $urlName => $urlData) {
            if (is_string($urlData)) {
                $urlData = [$urlData, []];
            }

            $urlOptions = new Options($urlData[1]);

            $method = $urlOptions->get('method', 'GET', 'up');
            $args = $urlOptions->get('args');
            $url = 'GET' === $method ? Url::addArg((array)$args, $urlData[0]) : $urlData[0];

            $promises[$urlName] = $client->requestAsync(
                $method,
                $url,
                $this->getClientOptions($urlOptions, $method, $args)
            );
        }

        /** @var Response[] $httpResults */
        $httpResults = Promise\unwrap($promises);

        $result = [];
        foreach ($httpResults as $resName => $httpResult) {
            $result[$resName] = [
                $httpResult->getStatusCode(),
                $httpResult->getHeaders(),
                $httpResult->getBody()->getContents()
            ];
        }

        return $result;
    }

    /**
     * @param Options           $options
     * @param string            $method
     * @param string|array|null $args
     * @return array
     */
    protected function getClientOptions(Options $options, $method, $args)
    {
        $headers = $options->getHeaders();
        $headers['User-Agent'] = $options->getUserAgent('Guzzle');

        $body = $formParams = null;

        if ('GET' !== $method) {
            if (is_array($args)) {
                $formParams = $args;
            } else {
                $body = $args;
            }
        }

        return [
            'form_params'     => $formParams,
            'body'            => $body,
            'headers'         => $headers,
            'connect_timeout' => $options->getTimeout(),
            'timeout'         => $options->getTimeout(),
            'verify'          => $options->isVerify(),
            'exceptions'      => $options->showException(),
            'auth'            => $options->getAuth(),
            'allow_redirects' => $this->getAllowRedirects($options)
        ];
    }

    /**
     * @param Options $options
     * @return array|bool
     */
    protected function getAllowRedirects(Options $options)
    {
        $allowRedirects = false;

        if ($options->isAllowRedirects()) {
            $allowRedirects = [
                'max' => $options->getMaxRedirects(),
            ];
        }

        return $allowRedirects;
    }
}
