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
 * Class Guzzle5
 * @package JBZoo\HttpClient
 */
abstract class Guzzle extends Driver
{
    /**
     * @param Options $options
     * @return array|bool
     */
    protected function _getAllowRedirects(Options $options)
    {
        $allowRedirects = false;

        if ($options->isAllowRedirects()) {
            $allowRedirects = array(
                'max' => $options->getMaxRedirects(),
            );
        }

        return $allowRedirects;
    }
}
