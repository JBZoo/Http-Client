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

namespace JBZoo\PHPUnit;

/**
 * Class DriverAutoTest
 * @package JBZoo\PHPUnit
 */
class DriverAutoTest extends DriverTest
{
    protected $driver = 'Auto';

    protected function setUp(): void
    {
        parent::setUp();
        sleep(1); // for mockservers
    }

    public function testCheckDefaultDriver()
    {
        $client = $this->getClient();

        $response = $client->request('https://httpbin.org/user-agent');
        isSame('JBZoo/Http-Client (Guzzle)', $response->getJSON()->get('user-agent'));

    }
}
