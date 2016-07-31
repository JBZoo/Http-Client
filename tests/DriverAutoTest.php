<?php
/**
 * JBZoo Http-Client
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package   Http-Client
 * @license   MIT
 * @copyright Copyright (C) JBZoo.com,  All rights reserved.
 * @link      https://github.com/JBZoo/Http-Client
 */

namespace JBZoo\PHPUnit;

use JBZoo\Utils\Env;

/**
 * Class DriverAutoTest
 * @package JBZoo\PHPUnit
 */
class DriverAutoTest extends DriverTest
{
    protected $_driver = 'Auto';

    protected function setUp()
    {
        parent::setUp();

        if (version_compare(Env::getVersion(), '5.4', '<')) {
            $this->_methods = array('get', 'post', 'post', 'put');
        }
    }
}
