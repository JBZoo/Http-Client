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

namespace JBZoo\HttpClient;

use JBZoo\HttpClient\Driver\Driver;
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
    public function request($url, $args = null, $method = Options::DEFAULT_METHOD, array $options = []): Response
    {
        $method = Filter::up($method);
        $url = 'GET' === $method ? Url::addArg((array)$args, $url) : $url;

        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        $options = new Options(array_merge($this->options->getArrayCopy(), $options));

        $client = $this->getClient($options);
        $response = new Response();

        try {
            [$code, $headers, $body] = $client->request($url, $args, $method, $options);

            $response->setCode($code);
            $response->setHeaders($headers);
            $response->setBody($body);
        } catch (\Exception $e) {
            if ($options->isExceptions()) {
                throw new Exception($e->getMessage(), $e->getCode(), $e);
            }

            $response->setCode($e->getCode());
            $response->setHeaders([]);
            $response->setBody($e->getMessage());
        }

        return $response;
    }

    /**
     * @param array $urls
     * @param array $options
     * @return Response[]
     * @throws Exception
     */
    public function multiRequest(array $urls, array $options = [])
    {
        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        $options = new Options(array_merge($this->options->getArrayCopy(), $options));
        $client = $this->getClient($options);

        $httpResults = $client->multiRequest($urls);

        $results = [];
        foreach ($httpResults as $resKey => $resultRow) {
            [$code, $headers, $body] = $resultRow;

            $results[$resKey] = new Response();
            $results[$resKey]->setCode($code);
            $results[$resKey]->setHeaders($headers);
            $results[$resKey]->setBody($body);
        }

        return $results;
    }

    /**
     * @param Options $options
     * @return Driver
     * @throws Exception
     */
    protected function getClient(Options $options)
    {
        $className = '\JBZoo\HttpClient\\Driver\\' . $options->getDriver();

        return new $className($options);
    }
}
