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

namespace JBZoo\PHPUnit;

final class HttpClientPackageTest extends \JBZoo\Codestyle\PHPUnit\AbstractPackageTest
{
    protected string $packageName = 'Http-Client';

    protected static function stepBeforeTests(): ?array
    {
        return [
            'name' => 'Start HTTP Mock Server',
            'run'  => 'make start-mock-server --no-print-directory',
        ];
    }
}
