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

declare(strict_types=1);

namespace JBZoo\HttpClient;

use JBZoo\Event\EventManager;
use JBZoo\HttpClient\Driver\AbstractDriver;

/**
 * Class HttpClient
 * @package JBZoo\HttpClient
 */
class HttpClient
{
    /**
     * @var Options
     */
    private $options;

    /**
     * @var EventManager|null
     */
    private $eManager;

    /**
     * @var Request|null
     */
    private $lastRequest;

    /**
     * @var Response|null
     */
    private $lastResponse;

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
     */
    public function request(
        string $url,
        $args = null,
        string $method = Request::DEFAULT_METHOD,
        array $options = []
    ): Response {
        $this->lastRequest = (new Request())
            ->setUrl($url)
            ->setArgs($args)
            ->setMethod($method)
            ->setOptions(array_merge($this->options->toArray(), $options));

        $startTime = microtime(true);

        try {
            $this->trigger('request.before', [$this->lastRequest]);
            $response = $this->getDriver()->request($this->lastRequest);
            $this->trigger('request.after', [$response, $this->lastRequest]);
        } catch (\Exception $exception) {
            if ($this->lastRequest->getOptions()->allowException()) {
                throw new Exception($exception->getMessage(), (int)$exception->getCode(), $exception);
            }

            $response = (new Response())
                ->setCode((int)$exception->getCode())
                ->setHeaders([])
                ->setBody($exception->getMessage())
                ->setRequest($this->lastRequest);
        }

        if (null === $response->time) {
            $response->setTime(microtime(true) - $startTime);
        }

        $this->lastResponse = $response;

        return $response;
    }

    /**
     * @param array $requestList
     * @param array $options
     * @return Response[]
     */
    public function multiRequest(array $requestList, array $options = []): array
    {
        /** @var Request[] $cleanedRequestList */
        $cleanedRequestList = [];

        foreach ($requestList as $name => $requestData) {
            $requestOptions = array_merge($this->options->toArray(), $options, (array)($requestData[3] ?? []));

            $cleanedRequestList[$name] = (new Request())
                ->setUrl($requestData[0] ?? '')
                ->setArgs($requestData[1] ?? null)
                ->setMethod($requestData[2] ?? Request::DEFAULT_METHOD)
                ->setHeaders($requestOptions['headers'] ?? [])
                ->setOptions($requestOptions);
        }

        $this->trigger('multi-request.before', [$cleanedRequestList]);

        $responseList = $this->getDriver()->multiRequest($cleanedRequestList);

        $this->trigger('multi-request.after', [$responseList, $cleanedRequestList]);

        return $responseList;
    }

    /**
     * @return AbstractDriver
     * @throws Exception
     * @psalm-suppress MoreSpecificReturnType
     * @psalm-suppress LessSpecificReturnStatement
     */
    protected function getDriver(): AbstractDriver
    {
        $driverName = $this->options->getDriver();

        $className = __NAMESPACE__ . "\\Driver\\{$driverName}";

        if (class_exists($className)) {
            return new $className();
        }

        throw new Exception("Driver '{$driverName}' not found");
    }

    /**
     * @param EventManager $eManager
     * @return $this
     */
    public function setEventManager(EventManager $eManager)
    {
        $this->eManager = $eManager;
        return $this;
    }

    /**
     * @param string        $eventName
     * @param array         $context
     * @param \Closure|null $callback
     * @return int|string
     */
    public function trigger(string $eventName, array $context = [], ?\Closure $callback = null)
    {
        if (!$this->eManager) {
            return 0;
        }

        array_unshift($context, $this);

        return $this->eManager->trigger("jbzoo.http.{$eventName}", $context, $callback);
    }

    /**
     * @return Response|null
     */
    public function getLastResponse(): ?Response
    {
        return $this->lastResponse;
    }

    /**
     * @return Request|null
     */
    public function getLastRequest(): ?Request
    {
        return $this->lastRequest;
    }
}
