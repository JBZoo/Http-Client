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

$default = include __DIR__ . '/vendor/jbzoo/codestyle/src/phan/default.php';

return array_merge($default, [
    'directory_list' => [
        'src',

        'vendor/jbzoo/data',
        'vendor/jbzoo/utils',
        'vendor/jbzoo/event',
        'vendor/guzzlehttp/guzzle',
        'vendor/guzzlehttp/promises',
        'vendor/psr/http-message',
        'vendor/rmccue/requests',
    ]
]);
