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

use JBZoo\HttpClient\Request;
use JBZoo\HttpClient\Response;

/**
 * Class AbstractDriver
 * @package JBZoo\HttpClient\Driver
 */
abstract class AbstractDriver
{
    /**
     * @param Request $request
     * @return Response
     */
    abstract public function request(Request $request): Response;

    /**
     * @param Request[] $requestList
     * @return Response[]
     */
    abstract public function multiRequest(array $requestList): array;
}
