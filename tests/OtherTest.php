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

use JBZoo\Event\EventManager;
use JBZoo\HttpClient\HttpClient;
use JBZoo\HttpClient\Request;
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
        isSame('https://httpbin.org/get', $request->getUri());
        isSame(null, $request->getArgs());
        isSame('GET', $request->getMethod());
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
        ], $request->getOptions()->toArray());
    }

    public function testGetRequestGlobalOptions()
    {
        $client = new HttpClient(['user_agent' => 'Qwerty Client']);
        $response = $client->request('https://httpbin.org/get', ['param' => 'value']);

        isSame('Qwerty Client', $response->getJSON()->find('headers.User-Agent'));

        $request = $response->getRequest();
        isSame('https://httpbin.org/get?param=value', $request->getUri());
        isSame(null, $request->getArgs());
        isSame('GET', $request->getMethod());
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
        ], $request->getOptions()->toArray());
    }

    public function testGetRequestRequestOptions()
    {
        $client = new HttpClient();
        $response = $client->request('https://httpbin.org/post', ['param' => 'value'], 'POST', [
            'user_agent' => 'Qwerty Client2'
        ]);

        isSame('Qwerty Client2', $response->getJSON()->find('headers.User-Agent'));

        $request = $response->getRequest();
        isSame('https://httpbin.org/post', $request->getUri());
        isSame(['param' => 'value'], $request->getArgs());
        isSame('POST', $request->getMethod());
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
        ], $request->getOptions()->toArray());
    }

    public function testGetRequestRequestOptionsWithPostBody()
    {
        $client = new HttpClient();
        $response = $client->request('https://httpbin.org/post', 'qwerty', 'POST', [
            'user_agent' => 'Qwerty Client2'
        ]);

        isSame('Qwerty Client2', $response->getJSON()->find('headers.User-Agent'));

        $request = $response->getRequest();
        isSame('https://httpbin.org/post', $request->getUri());
        isSame('qwerty', $request->getArgs());
        isSame('POST', $request->getMethod());
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
        ], $request->getOptions()->toArray());
    }

    public function testGetRequestMergingOptions()
    {
        $randomValue = random_int(0, 100000);

        $client = new HttpClient([
            'user_agent' => 'Qwerty Client3',
            'driver'     => 'Rmccue'
        ]);

        $response = $client->request('https://httpbin.org/get?key=val', ['param' => 'value'], 'GET', [
            'user_agent' => 'Custom Agent',
            'headers'    => ['X-Custom-Header' => $randomValue]
        ]);

        isSame($response->code, $response->getCode());
        isSame(200, $response->code);
        isSame($response->headers, $response->getHeaders());
        isSame($response->body, $response->getBody());
        isSame($response->time, $response->getTime());
        isTrue($response->time > 0);

        isSame($response, $client->getLastResponse());
        isSame($client->getLastRequest(), $client->getLastResponse()->getRequest());

        isSame('Custom Agent', $client->getLastResponse()->getJSON()->find('headers.User-Agent'));
        isSame((string)$randomValue, $client->getLastResponse()->getJSON()->find('headers.X-Custom-Header'));

        $request = $client->getLastRequest();
        isSame('https://httpbin.org/get?key=val&param=value', $request->getUri());
        isSame(null, $request->getArgs());
        isSame('GET', $request->getMethod());
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
        ], $request->getOptions()->toArray());


        isSame($request->toArray(), $response->toArray()['request']);

        isSame(['uri', 'method', 'args', 'headers', 'options'], array_keys($request->toArray()));
        isSame(['request', 'response'], array_keys($response->toArray()));
        isSame(['code', 'body', 'headers', 'time'], array_keys($response->toArray()['response']));
    }

    public function testCheckDefaultDriver()
    {
        $client = new HttpClient();

        $response = $client->request('https://httpbin.org/user-agent');
        isSame('JBZoo/Http-Client (Guzzle)', $response->getJSON()->get('user-agent'));
    }

    public function testEventManager()
    {
        $eManager = new EventManager();

        $client = new HttpClient();
        $client->setEventManager($eManager);

        $counter = 0;
        $eManager
            ->once('jbzoo.http.request.before', function (HttpClient $client, Request $request) use (&$counter) {
                isSame('https://httpbin.org/get', $client->getLastRequest()->getUri());
                isSame('https://httpbin.org/get', $request->getUri());
                $counter++;
            })
            ->once('jbzoo.http.request.after',
                function (HttpClient $client, Response $response, Request $request) use (&$counter) {
                    isSame('https://httpbin.org/get', $client->getLastRequest()->getUri());
                    isSame('https://httpbin.org/get', $request->getUri());
                    isSame('httpbin.org', $response->getJSON()->find('headers.Host'));
                    $counter++;
                });

        $response = $client->request('https://httpbin.org/get');
        isSame('httpbin.org', $response->getJSON()->find('headers.Host'));

        isSame(2, $counter);
    }
}
