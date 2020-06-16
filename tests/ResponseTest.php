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

use JBZoo\HttpClient\Response;

/**
 * Class ResponseTest
 * @package JBZoo\PHPUnit
 */
class ResponseTest extends PHPUnit
{
    protected $jsonFixture = '{"key-1":"value-1","key-2":"value-2"}';

    public function testGetSameJSON()
    {
        $resp = new Response();

        $resp->setBody($this->jsonFixture);

        $json1 = $resp->getJSON();
        $json2 = $resp->getJSON();

        isSame('value-1', $resp->getJSON()->get('key-1'));
        isSame('value-2', $resp->getJSON()->find('key-2'));
        isSame($json1, $json2);
        isSame($json1, $resp->getJSON());
        isSame($json2, $resp->getJSON());

        $resp->setBody($this->jsonFixture);
        isNotSame($json1, $resp->getJSON());
        isNotSame($json2, $resp->getJSON());
        isSame($resp->getJSON(), $resp->getJSON());
    }
}
