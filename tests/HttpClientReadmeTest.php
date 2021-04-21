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

namespace JBZoo\PHPUnit;

/**
 * Class HttpClientReadmeTest
 *
 * @package JBZoo\PHPUnit
 */
class HttpClientReadmeTest extends AbstractReadmeTest
{
    protected $packageName = 'Http-Client';

    protected function setUp(): void
    {
        parent::setUp();
        $this->params['strict_types'] = true;
    }
}
