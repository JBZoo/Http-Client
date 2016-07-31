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

/**
 * Class RmccueDriverTest
 * @package JBZoo\PHPUnit
 */
class RmccueDriverTest extends DriverTest
{
    protected $_driver = 'Rmccue';

    protected $_methods = array('GET', 'POST', 'PATCH', 'PUT'); // TODO add 'DELETE'
}
