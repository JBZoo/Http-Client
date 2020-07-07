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
use JBZoo\HttpClient\Options;
use JBZoo\HttpClient\Request;
use JBZoo\HttpClient\Response;

use function GuzzleHttp\Promise\unwrap;

/**
 * Class Guzzle
 * @package JBZoo\HttpClient
 */
class Guzzle extends AbstractDriver
{
    /**
     * @inheritDoc
     */
    public function request(Request $request): Response
    {
        $client = new Client();

        $start = microtime(true);

        $httpResult = $client->request(
            $request->getMethod(),
            $request->getUri(),
            self::getDriverOptions(
                $request->getOptions(),
                $request->getHeaders(),
                $request->getMethod(),
                $request->getArgs()
            )
        );

        return (new Response())
            ->setCode($httpResult->getStatusCode())
            ->setHeaders($httpResult->getHeaders())
            ->setBody($httpResult->getBody()->getContents())
            ->setRequest($request)
            ->setTime(microtime(true) - $start);
    }

    /**
     * @inheritDoc
     */
    public function multiRequest(array $requestList): array
    {
        $client = new Client();

        $promises = [];
        foreach ($requestList as $name => $request) {
            $promises[$name] = $client->requestAsync(
                $request->getMethod(),
                $request->getUri(),
                self::getDriverOptions(
                    $request->getOptions(),
                    $request->getHeaders(),
                    $request->getMethod(),
                    $request->getArgs()
                )
            );
        }

        $httpResults = unwrap($promises);

        $result = [];
        foreach ($httpResults as $name => $httpResult) {
            $result[$name] = (new Response())
                ->setCode($httpResult->getStatusCode())
                ->setHeaders($httpResult->getHeaders())
                ->setBody($httpResult->getBody()->getContents())
                ->setRequest($requestList[$name]);
        }

        return $result;
    }

    /**
     * @param Options           $options
     * @param array             $headers
     * @param string            $method
     * @param string|array|null $args
     * @return array
     */
    protected static function getDriverOptions(Options $options, array $headers, string $method, $args): array
    {
        $headers['User-Agent'] = $options->getUserAgent('Guzzle');

        $body = $formParams = null;

        if (Request::GET !== $method) {
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
            'exceptions'      => $options->allowException(),
            'auth'            => $options->getAuth(),
            'allow_redirects' => self::getAllowRedirects($options)
        ];
    }

    /**
     * @param Options $options
     * @return array|bool
     */
    protected static function getAllowRedirects(Options $options)
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
