<?php
/**
 * JBZoo Http-Client
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package   Http-Client
 * @license   MIT
 * @copyright Copyright (C) JBZoo.com,  All rights reserved.
 * @link      https://github.com/JBZoo/Http-Client
 */

namespace JBZoo\HttpClient\Driver;

use JBZoo\HttpClient\Exception;
use JBZoo\HttpClient\Options;
use JBZoo\Utils\Filter;
use JBZoo\Utils\Url;

/**
 * Class Rmccue
 * @package JBZoo\HttpClient
 */
class Rmccue extends Driver
{
    /**
     * @inheritdoc
     */
    public function request($url, $args, $method, Options $options)
    {
        $headers = $options->get('headers', array());
        $method  = Filter::up($method);
        $args    = 'GET' !== $method ? $args : array();

        $httpResult = \Requests::request(
            $url,
            $headers,
            $args,
            $method,
            $this->_getClientOptions($options)
        );

        if ($options->isExceptions() && $httpResult->status_code >= 400) {
            throw new Exception($httpResult->body, $httpResult->status_code);
        }

        return array(
            $httpResult->status_code,
            $httpResult->headers->getAll(),
            $httpResult->body
        );
    }

    /**
     * @inheritdoc
     */
    public function multiRequest(array $urls)
    {
        $requests = array();
        foreach ($urls as $urlName => $urlData) {

            if (is_string($urlData)) {
                $urlData = array($urlData, array());
            }

            $urlOptions = new Options($urlData[1]);

            $method = $urlOptions->get('method', 'GET', 'up');
            $args   = $urlOptions->get('args');
            $url    = 'GET' === $method ? Url::addArg((array)$args, $urlData[0]) : $urlData[0];
            $args   = 'GET' !== $method ? $args : array();

            $requests[$urlName] = array(
                'url'     => $url,
                'headers' => $urlOptions->getHeaders(),
                'data'    => $args,
                'type'    => $method,
                'options' => $this->_getClientOptions($urlOptions),
            );
        }

        $httpResults = \Requests::request_multiple($requests);

        /** @var string $resName */
        /** @var \Requests_Response $httpResult */
        $result = array();
        foreach ($httpResults as $resName => $httpResult) {
            $result[$resName] = array(
                $httpResult->status_code,
                $httpResult->headers->getAll(),
                $httpResult->body
            );
        }

        return $result;
    }

    /**
     * @param Options $options
     * @return array
     */
    protected function _getClientOptions(Options $options)
    {
        return array(
            'timeout'          => $options->getTimeout(),
            'verify'           => $options->isVerify(),
            'follow_redirects' => $options->isAllowRedirects(),
            'redirects'        => $options->getMaxRedirects(),
            'useragent'        => $options->getUserAgent('Rmccue'),
            'auth'             => $options->getAuth(),
        );
    }
}
