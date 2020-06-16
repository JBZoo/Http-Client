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
use JBZoo\HttpClient\Response;

/**
 * Class OtherTest
 * @package JBZoo\PHPUnit
 */
class OtherTest extends PHPUnit
{
    protected $jsonFixture = '{"key-1":"value-1","key-2":"value-2"}';

    public function testGetSameJSONFromResponse()
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

    public function testGetRequestDefault()
    {
        $client = new HttpClient();
        $response = $client->request('https://httpbin.org/get');

        isSame('JBZoo/Http-Client (Guzzle)', $response->getJSON()->find('headers.User-Agent'));

        $request = $response->getRequest();
        isSame('https://httpbin.org/get', $request->get('url'));
        isSame(null, $request->get('args'));
        isSame('GET', $request->get('method'));
        isSame([
            'auth'            => [],
            'headers'         => [],
            'driver'          => 'Guzzle',
            'timeout'         => 10,
            'verify'          => true,
            'exceptions'      => false,
            'allow_redirects' => true,
            'max_redirects'   => 10,
            'user_agent'      => 'JBZoo/Http-Client'
        ], $request->get('options'));
    }

    public function testGetRequestGlobalOptions()
    {
        $client = new HttpClient(['user_agent' => 'Qwerty Client']);
        $response = $client->request('https://httpbin.org/get', ['param' => 'value']);

        isSame('Qwerty Client', $response->getJSON()->find('headers.User-Agent'));

        $request = $response->getRequest();
        isSame('https://httpbin.org/get?param=value', $request->get('url'));
        isSame(['param' => 'value'], $request->get('args'));
        isSame('GET', $request->get('method'));
        isSame([
            "auth"            => [],
            "headers"         => [],
            "driver"          => "Guzzle",
            "timeout"         => 10,
            "verify"          => true,
            "exceptions"      => false,
            "allow_redirects" => true,
            "max_redirects"   => 10,
            "user_agent"      => 'Qwerty Client'
        ], $request->get('options'));
    }

    public function testGetRequestRequestOptions()
    {
        $client = new HttpClient();
        $response = $client->request('https://httpbin.org/post', ['param' => 'value'], 'POST', [
            'user_agent' => 'Qwerty Client2'
        ]);

        isSame('Qwerty Client2', $response->getJSON()->find('headers.User-Agent'));

        $request = $response->getRequest();
        isSame('https://httpbin.org/post', $request->get('url'));
        isSame(['param' => 'value'], $request->get('args'));
        isSame('POST', $request->get('method'));
        isSame([
            "auth"            => [],
            "headers"         => [],
            "driver"          => "Guzzle",
            "timeout"         => 10,
            "verify"          => true,
            "exceptions"      => false,
            "allow_redirects" => true,
            "max_redirects"   => 10,
            "user_agent"      => 'Qwerty Client2'
        ], $request->get('options'));
    }

    public function testGetRequestRequestOptionsWithPostBody()
    {
        $client = new HttpClient();
        $response = $client->request('https://httpbin.org/post', 'qwerty', 'POST', [
            'user_agent' => 'Qwerty Client2'
        ]);

        isSame('Qwerty Client2', $response->getJSON()->find('headers.User-Agent'));

        $request = $response->getRequest();
        isSame('https://httpbin.org/post', $request->get('url'));
        isSame('qwerty', $request->get('args'));
        isSame('POST', $request->get('method'));
        isSame([
            "auth"            => [],
            "headers"         => [],
            "driver"          => "Guzzle",
            "timeout"         => 10,
            "verify"          => true,
            "exceptions"      => false,
            "allow_redirects" => true,
            "max_redirects"   => 10,
            "user_agent"      => 'Qwerty Client2'
        ], $request->get('options'));
    }

    public function testGetRequestMergingOptions()
    {
        $randomValue = random_int(0, 100000);

        $client = new HttpClient([
            'user_agent' => 'Qwerty Client3',
            'driver'     => 'Rmccue'
        ]);

        $response = $client->request('https://httpbin.org/get', ['param' => 'value'], 'GET', [
            'user_agent' => 'Custom Agent',
            'headers'    => ['X-Custom-Header' => $randomValue]
        ]);

        isSame('Custom Agent', $response->getJSON()->find('headers.User-Agent'));
        isSame((string)$randomValue, $response->getJSON()->find('headers.X-Custom-Header'));

        $request = $response->getRequest();
        isSame('https://httpbin.org/get?param=value', $request->get('url'));
        isSame(['param' => 'value'], $request->get('args'));
        isSame('GET', $request->get('method'));
        isSame([
            "auth"            => [],
            "headers"         => ['X-Custom-Header' => $randomValue],
            "driver"          => "Rmccue",
            "timeout"         => 10,
            "verify"          => true,
            "exceptions"      => false,
            "allow_redirects" => true,
            "max_redirects"   => 10,
            "user_agent"      => 'Custom Agent'
        ], $request->get('options'));
    }

    public function testCheckDefaultDriver()
    {
        $client = new HttpClient();

        $response = $client->request('https://httpbin.org/user-agent');
        isSame('JBZoo/Http-Client (Guzzle)', $response->getJSON()->get('user-agent'));
    }
}
