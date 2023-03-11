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

use JBZoo\HttpClient\Request;
use JBZoo\HttpClient\Response;

/**
 * @psalm-consistent-constructor
 */
abstract class AbstractDriver
{
    abstract public function request(Request $request): Response;

    /**
     * @param  Request[]  $requestList
     * @return Response[]
     */
    abstract public function multiRequest(array $requestList): array;
}
