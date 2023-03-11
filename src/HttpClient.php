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

namespace JBZoo\HttpClient;

use JBZoo\Event\EventManager;
use JBZoo\HttpClient\Driver\AbstractDriver;

final class HttpClient
{
    private Options       $options;
    private ?EventManager $eManager     = null;
    private ?Request      $lastRequest  = null;
    private ?Response     $lastResponse = null;

    public function __construct(array $options = [])
    {
        $this->options = new Options($options);
    }

    public function request(
        string $url,
        array|string $args = null,
        string $method = Request::DEFAULT_METHOD,
        array $options = [],
    ): Response {
        $this->lastRequest = (new Request())
            ->setUrl($url)
            ->setArgs($args)
            ->setMethod($method)
            ->setOptions(\array_merge($this->options->toArray(), $options));

        $startTime = \microtime(true);

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

        if ($response->time === null) {
            $response->setTime(\microtime(true) - $startTime);
        }

        $this->lastResponse = $response;

        return $response;
    }

    /**
     * @return Response[]
     */
    public function multiRequest(array $requestList, array $options = []): array
    {
        /** @var Request[] $cleanedRequestList */
        $cleanedRequestList = [];

        foreach ($requestList as $name => $requestData) {
            $requestOptions = \array_merge($this->options->toArray(), $options, (array)($requestData[3] ?? []));

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

    public function setEventManager(EventManager $eManager): self
    {
        $this->eManager = $eManager;

        return $this;
    }

    public function trigger(string $eventName, array $context = [], ?\Closure $callback = null): int
    {
        if (!$this->eManager) {
            return 0;
        }

        \array_unshift($context, $this);

        return $this->eManager->trigger("jbzoo.http.{$eventName}", $context, $callback);
    }

    public function getLastResponse(): ?Response
    {
        return $this->lastResponse;
    }

    public function getLastRequest(): ?Request
    {
        return $this->lastRequest;
    }

    /**
     * @throws Exception
     */
    private function getDriver(): AbstractDriver
    {
        $driverName = $this->options->getDriver();

        $className = __NAMESPACE__ . "\\Driver\\{$driverName}";

        if (\class_exists($className)) {
            /** @var AbstractDriver $driver */
            return new $className();
        }

        throw new Exception("Driver '{$driverName}' not found");
    }
}
