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

use JBZoo\Data\Data;
use JBZoo\HttpClient\Exception;
use JBZoo\HttpClient\Options;
use JBZoo\Utils\Filter;
use JBZoo\Utils\Url;
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
            $this->getClientOptions($options)
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
    public function multiRequest(array $urls)
    {
        $requests = [];

        /** @var array|string $urlData */
        foreach ($urls as $urlName => $urlData) {
            if (is_string($urlData)) {
                $urlData = [$urlData, []];
            }

            $requestOptions = new Options((array)$urlData[1]);
            $urlParams = new Data((array)$urlData[1]);

            $method = (string)$urlParams->get('method', 'GET', 'up');
            $args = (array)$urlParams->get('args');

            $url = 'GET' === $method ? Url::addArg($args, (string)$urlData[0]) : (string)$urlData[0];
            $args = 'GET' !== $method ? $args : [];

            $requests[$urlName] = [
                'url'     => $url,
                'headers' => $requestOptions->getHeaders(),
                'data'    => $args,
                'type'    => $method,
                'options' => $this->getClientOptions($requestOptions),
            ];
        }

        $httpResults = Requests::request_multiple($requests);

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
    protected function getClientOptions(Options $options)
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
