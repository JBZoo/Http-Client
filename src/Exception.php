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

declare(strict_types=1);

namespace JBZoo\HttpClient;

use JBZoo\Event\EventManager;

/**
 * Class Exception
 * @package JBZoo\HttpClient
 */
final class Exception extends \RuntimeException
{
    /**
     * Exception constructor.
     * @param string          $message
     * @param int             $code
     * @param \Throwable|null $previous
     */
    public function __construct($message = '', $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        if (class_exists(EventManager::class) && $eManager = EventManager::getDefault()) {
            $eManager->trigger('jbzoo.http.exception', [$this]);
        }
    }
}
