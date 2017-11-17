<?php
/**
 * JBZoo Http-Client
 *
 * This file is part of the JBZoo CCK package.
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
 * Class Driver
 * @package JBZoo\HttpClient
 */
abstract class Driver
{
    /**
     * @param string  $url
     * @param array   $args
     * @param string  $method
     * @param Options $options
     * @return array
     */
    abstract public function request($url, $args, $method, Options $options);

    /**
     * @param array $urls
     * @return array
     */
    abstract public function multiRequest(array $urls);
}
