<?php

/**
 * JBZoo Toolbox - Http-Client.
 *
 * This file is part of the JBZoo Toolbox project.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @see        https://github.com/JBZoo/Http-Client
 */

declare(strict_types=1);

namespace JBZoo\HttpClient\Driver;

use GuzzleHttp\Client;
use GuzzleHttp\Promise\Utils;
use JBZoo\HttpClient\Options;
use JBZoo\HttpClient\Request;
use JBZoo\HttpClient\Response;

final class Guzzle extends AbstractDriver
{
    public function request(Request $request): Response
    {
        $client = new Client();

        $start = \microtime(true);

        $httpResult = $client->request(
            $request->getMethod(),
            $request->getUri(),
            self::getDriverOptions(
                $request->getOptions(),
                $request->getHeaders(),
                $request->getMethod(),
                $request->getArgs(),
            ),
        );

        return (new Response())
            ->setCode($httpResult->getStatusCode())
            ->setHeaders($httpResult->getHeaders())
            ->setBody($httpResult->getBody()->getContents())
            ->setRequest($request)
            ->setTime(\microtime(true) - $start);
    }

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
                    $request->getArgs(),
                ),
            );
        }

        $httpResults = Utils::unwrap($promises);

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

    private static function getDriverOptions(
        Options $options,
        array $headers,
        string $method,
        null|array|string $args,
    ): array {
        $headers['User-Agent'] = $options->getUserAgent('Guzzle');

        $body = $formParams = null;

        if ($method !== Request::GET) {
            if (\is_array($args)) {
                $formParams = $args;
            } else {
                $body = $args;
            }
        }

        return [
            'form_params'     => $formParams,
            'http_errors'     => $options->allowException(),
            'body'            => $body,
            'headers'         => $headers,
            'connect_timeout' => $options->getTimeout(),
            'timeout'         => $options->getTimeout(),
            'verify'          => $options->isVerify(),
            'exceptions'      => $options->allowException(),
            'auth'            => $options->getAuth(),
            'allow_redirects' => self::getAllowRedirects($options),
        ];
    }

    private static function getAllowRedirects(Options $options): ?array
    {
        $allowRedirects = null;

        if ($options->isAllowRedirects()) {
            $allowRedirects = [
                'max' => $options->getMaxRedirects(),
            ];
        }

        return $allowRedirects;
    }
}
