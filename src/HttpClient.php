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

use JBZoo\HttpClient\Driver\AbstractDriver;
use JBZoo\Utils\Filter;
use JBZoo\Utils\Url;

/**
 * Class HttpClient
 * @package JBZoo\HttpClient
 */
class HttpClient
{
    /**
     * @var Options
     */
    protected $options;

    /**
     * HttpClient constructor.
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->options = new Options($options);
    }

    /**
     * @param string            $url
     * @param string|array|null $args
     * @param string            $method
     * @param array             $options
     * @return Response
     * @throws Exception
     */
    public function request(
        string $url,
        $args = null,
        string $method = Options::DEFAULT_METHOD,
        array $options = []
    ): Response {
        $method = Filter::up($method);
        $url = 'GET' === $method ? Url::addArg((array)$args, $url) : $url;

        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        $options = new Options(array_merge($this->options->toArray(), $options));

        $client = $this->getDriver($options);
        $response = new Response();

        try {
            [$code, $headers, $body] = $client->request($url, $args, $method, $options);

            $response
                ->setCode((int)$code)
                ->setHeaders((array)$headers)
                ->setBody((string)$body)
                ->setRequest([
                    'url'     => $url,
                    'args'    => $args,
                    'method'  => $method,
                    'options' => $options->toArray()
                ]);
        } catch (\Exception $exception) {
            if ($options->allowException()) {
                throw new Exception($exception->getMessage(), (int)$exception->getCode(), $exception);
            }

            $response
                ->setCode((int)$exception->getCode())
                ->setHeaders([])
                ->setBody($exception->getMessage());
        }

        return $response;
    }

    /**
     * @param array $requestList
     * @param array $options
     * @return Response[]
     * @throws Exception
     */
    public function multiRequest(array $requestList, array $options = [])
    {
        $cleanedRequestList = [];
        foreach ($requestList as $requestName => $requestData) {
            $args = $requestData[1] ?? null;
            $method = strtoupper($requestData[2] ?? 'GET') ?: 'GET';
            $requestOptions = new Options(array_merge($this->options->toArray(), (array)($requestData[3] ?? [])));

            $url = (string)($requestData[0] ?? '');
            $fullUri = $url;
            if ('GET' === $method) {
                $fullUri = Url::addArg((array)$args, $url);
                $args = null;
            }

            $cleanedRequestList[$requestName] = [$fullUri, $args, $method, $requestOptions];
        }

        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        $options = new Options(array_merge($this->options->toArray(), $options));
        $client = $this->getDriver($options);

        $responses = $client->multiRequest($cleanedRequestList);

        $results = [];

        foreach ($responses as $responseName => $responseData) {
            [$code, $headers, $body] = $responseData;
            [$uri, $args, $method, $options] = $cleanedRequestList[$responseName];

            $results[$responseName] = (new Response())
                ->setCode((int)$code)
                ->setHeaders((array)$headers)
                ->setBody((string)$body)
                ->setRequest([
                    'url'     => $uri,
                    'args'    => $args,
                    'method'  => $method,
                    'options' => $options->toArray()
                ]);
        }

        return $results;
    }

    /**
     * @param Options $options
     * @return AbstractDriver
     * @throws Exception
     * @psalm-suppress MoreSpecificReturnType
     * @psalm-suppress LessSpecificReturnStatement
     */
    protected function getDriver(Options $options): AbstractDriver
    {
        $driverName = $options->getDriver();

        $className = __NAMESPACE__ . "\\Driver\\{$driverName}";

        if (class_exists($className)) {
            return new $className($options);
        }

        throw new Exception("Driver '{$driverName}' not found");
    }
}
