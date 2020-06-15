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

namespace JBZoo\PHPUnit;

use JBZoo\HttpClient\HttpClient;

/**
 * Class OtherTest
 * @package JBZoo\PHPUnit
 */
class OtherTest extends PHPUnit
{
    public function testCheckDefaultDriver()
    {
        $client = new HttpClient();

        $response = $client->request('https://httpbin.org/user-agent');
        isSame('JBZoo/Http-Client (Guzzle)', $response->getJSON()->get('user-agent'));
    }
}
