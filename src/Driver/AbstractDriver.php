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

use JBZoo\HttpClient\Options;

/**
 * Class AbstractDriver
 * @package JBZoo\HttpClient
 */
abstract class AbstractDriver
{
    /**
     * @param string            $url
     * @param array|string|null $args
     * @param string            $method
     * @param Options           $options
     * @return array
     */
    abstract public function request(string $url, $args, string $method, Options $options);

    /**
     * @param array $urls
     * @return array
     */
    abstract public function multiRequest(array $urls);
}
