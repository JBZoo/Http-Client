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

/**
 * Class Auto
 *
 * @package JBZoo\HttpClient
 */
class Auto extends AbstractDriver
{
    /**
     * @inheritDoc
     */
    public function request(string $url, $args, string $method, Options $options)
    {
        return $this->getClient()->request($url, $args, $method, $options);
    }

    /**
     * @inheritDoc
     */
    public function multiRequest(array $requestList)
    {
        return $this->getClient()->multiRequest($requestList);
    }

    /**
     * @return AbstractDriver
     */
    protected function getClient(): AbstractDriver
    {
        if (class_exists(Client::class) && method_exists(Client::class, 'request')) {
            return new Guzzle();
        }

        return new Rmccue();
    }
}
