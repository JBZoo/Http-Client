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
        $args    = 'GET' !== $method ? $args : array();

        $httpResult = \Requests::request($url, $headers, $args, $method, array(
            'timeout'          => $options->getTimeout(),
            'verify'           => $options->isVerify(),
            'follow_redirects' => $options->isAllowRedirects(),
            'redirects'        => $options->getMaxRedirects(),
            'useragent'        => $options->getUserAgent('Rmccue'),
            'auth'             => $options->getAuth(),
        ));

        if ($options->isExceptions() && $httpResult->status_code >= 400) {
            throw new Exception($httpResult->body, $httpResult->status_code);
        }

        return array(
            $httpResult->status_code,
            $httpResult->headers->getAll(),
            $httpResult->body
        );
    }
}
