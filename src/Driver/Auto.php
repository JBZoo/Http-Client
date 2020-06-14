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
class Auto extends Driver
{
    /**
     * @inheritdoc
     */
    public function request($url, $args, $method, Options $options)
    {
        return $this->getClient()->request($url, $args, $method, $options);
    }

    /**
     * @inheritdoc
     */
    public function multiRequest(array $urls)
    {
        return $this->getClient()->multiRequest($urls);
    }

    /**
     * @return Driver
     */
    protected function getClient()
    {
        if (class_exists(Client::class) && method_exists(Client::class, 'request')) {
            return new Guzzle();
        }

        return new Rmccue();
    }
}
