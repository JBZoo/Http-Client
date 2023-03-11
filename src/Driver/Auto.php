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

namespace JBZoo\HttpClient\Driver;

use GuzzleHttp\Client;
use JBZoo\HttpClient\Request;
use JBZoo\HttpClient\Response;

final class Auto extends AbstractDriver
{
    /**
     * {@inheritDoc}
     */
    public function request(Request $request): Response
    {
        return self::getClient()->request($request);
    }

    /**
     * {@inheritDoc}
     */
    public function multiRequest(array $requestList): array
    {
        return self::getClient()->multiRequest($requestList);
    }

    protected static function getClient(): AbstractDriver
    {
        if (\class_exists(Client::class) && \method_exists(Client::class, 'request')) {
            return new Guzzle();
        }

        return new Rmccue();
    }
}
