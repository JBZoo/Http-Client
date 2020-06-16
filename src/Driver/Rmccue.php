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

use JBZoo\HttpClient\Exception;
use JBZoo\HttpClient\Options;
use JBZoo\Utils\Filter;
use Requests;

/**
 * Class Rmccue
 * @package JBZoo\HttpClient
 */
class Rmccue extends AbstractDriver
{
    private const INVALID_CODE_LINE = 400;

    /**
     * @inheritdoc
     */
    public function request(string $url, $args, string $method, Options $options)
    {
        $headers = $options->getHeaders();
        $method = Filter::up($method);
        $args = 'GET' !== $method ? $args : [];

        /**
         * @psalm-suppress PossiblyInvalidArgument
         * @phpstan-ignore-next-line
         */
        $httpResult = Requests::request(
            $url,
            $headers,
            /** @phan-suppress-next-line PhanPartialTypeMismatchArgument */
            $args,
            $method,
            $this->getDriverOptions($options)
        );


        if ($httpResult->status_code >= self::INVALID_CODE_LINE && $options->allowException()) {
            throw new Exception($httpResult->body, (int)$httpResult->status_code);
        }

        return [
            $httpResult->status_code,
            /** @phan-suppress-next-line PhanPossiblyNonClassMethodCall */
            $httpResult->headers->getAll(),
            $httpResult->body
        ];
    }

    /**
     * @inheritdoc
     */
    public function multiRequest(array $requestList)
    {
        $requestResults = [];
        foreach ($requestList as $requestName => $requestParams) {
            [$uri, $args, $method, $options] = $requestParams;

            $requestResults[$requestName] = [
                'url'     => $uri,
                'data'    => $args,
                'type'    => $method,
                'headers' => $options->getHeaders(),
                'options' => $this->getDriverOptions($options),
            ];
        }

        $httpResults = Requests::request_multiple($requestResults);

        $result = [];
        foreach ($httpResults as $resName => $httpResult) {
            $result[$resName] = [
                (int)$httpResult->status_code,
                $httpResult->headers->getAll(),
                $httpResult->body
            ];
        }

        return $result;
    }

    /**
     * @param Options $options
     * @return array
     */
    protected function getDriverOptions(Options $options)
    {
        return [
            'timeout'          => $options->getTimeout(),
            'verify'           => $options->isVerify(),
            'follow_redirects' => $options->isAllowRedirects(),
            'redirects'        => $options->getMaxRedirects(),
            'useragent'        => $options->getUserAgent('Rmccue'),
            'auth'             => $options->getAuth() ?: false,
        ];
    }
}
