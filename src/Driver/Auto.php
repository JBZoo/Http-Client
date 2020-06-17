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
use JBZoo\HttpClient\Request;
use JBZoo\HttpClient\Response;

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
    public function request(Request $request): Response
    {
        return $this->getClient()->request($request);
    }

    /**
     * @inheritDoc
     */
    public function multiRequest(array $requestList): array
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
