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
use JBZoo\HttpClient\Options;
use JBZoo\Utils\Url;

/**
 * Class DriverTest
 *
 * @package JBZoo\PHPUnit
 */
abstract class AbstractDriverTest extends PHPUnit
{
    /**
     * @var string
     */
    protected $driver = 'Auto';

    /**
     * @var array
     */
    protected $methods = ['GET', 'POST', 'PATCH', 'PUT', 'DELETE'];

    /**
     * @param array $options
     * @return HttpClient
     */
    protected function getClient(array $options = []): HttpClient
    {
        $options['driver'] = $this->driver;
        return new HttpClient($options);
    }

    public function testSimple()
    {
        $url = 'http://www.mocky.io/v2/579b43a91100006f1bcb7734';
        $result = $this->getClient()->request($url);

        isSame(200, $result->code);
        isSame('42', $result->getHeader('x-custom-header'));
        isContain('application/json', $result->getHeader('content-type'));
        isSame('{"great-answer": "42"}', $result->body);
    }

    public function testBinaryData()
    {
        $result = $this->getClient()->request('https://httpbin.org/image/png');

        isSame(200, $result->getCode());
        isSame('image/png', $result->getHeader('CONTENT-TYPE'));
        isContain('PNG', $result->getBody());
    }

    public function testPOSTPayload()
    {
        $uniq = uniqid('', true);
        $payload = json_encode(['key' => $uniq]);

        $url = 'http://mockbin.org/request?key=value';
        $result = $this->getClient(['exceptions' => true])->request($url, $payload, 'post');
        $body = $result->getJSON();

        isSame($payload, $body->find('postData.text'));
        isSame('value', $body->find('queryString.key'));
        isSame('POST', $body->find('method'));
    }

    public function testAllMethods()
    {
        foreach ($this->methods as $method) {
            $uniq = uniqid('', true);
            $url = "http://mockbin.org/request?method={$method}&qwerty=remove_me";
            $args = ['qwerty' => $uniq];
            $message = 'Method: ' . $method;

            $result = $this->getClient()->request($url, $args, $method);
            $body = $result->getJSON();

            if (!$result->getCode()) {
                fail($message . ' Body: ' . $result->getBody());
            }

            isSame(200, $result->getCode(), $message);
            isContain('application/json', $result->getHeader('Content-Type'), $message);
            isSame($method, $body->find('queryString.method'), $message);
            isSame($method, $body->find('method'), $message);

            if ($method === 'GET') {
                $this->isSameUrl(Url::addArg($args, $url), $body->find('url'), $message);
                isSame($uniq, $body->find('queryString.qwerty'), $message);
            } else {
                $this->isContainUrl($url, $body->find('url'), $message);
                if ($this->driver === 'Rmccue' && $method === 'DELETE') {
                    skip('DELETE is not supported with Rmccue/Requests correctly');
                }
                isSame($uniq, $body->find('postData.params.qwerty'), $message);
            }
        }
    }

    public function testAuth()
    {
        $url = 'http://httpbin.org/basic-auth/user/passwd';
        $result = $this->getClient([
            'auth' => ['user', 'passwd'],
        ])->request($url);

        isSame(200, $result->code);
        isSame(true, $result->getJSON()->get('authenticated'));
        isSame('user', $result->getJSON()->get('user'));
    }

    public function testGetQueryString()
    {
        $uniq = uniqid('', true);

        $siteUrl = 'https://httpbin.org/get?key=value';
        $args = ['qwerty' => $uniq];
        $url = Url::addArg($args, $siteUrl);
        $result = $this->getClient()->request($url, $args);

        isSame(200, $result->code);
        isContain('application/json', $result->getHeader('Content-Type'));

        $body = $result->getJSON();
        $this->isSameUrl(Url::addArg($args, $url), $body->find('url'));
        isSame($uniq, $body->find('args.qwerty'));
        isSame('value', $body->find('args.key'));
    }

    public function testUserAgent()
    {
        $result = $this->getClient()->request('https://httpbin.org/user-agent');
        $body = $result->getJSON();

        isSame(200, $result->code);
        isContain('application/json', $result->getHeader('Content-Type'));
        isContain(Options::DEFAULT_USER_AGENT . ' (', $body->find('user-agent'));
    }

    public function testPost()
    {
        $uniq = uniqid('', true);
        $url = 'https://httpbin.org/post?key=value';
        $args = ['qwerty' => $uniq];

        $result = $this->getClient()->request($url, $args, 'post');
        $body = $result->getJSON();

        isSame(200, $result->code);
        isContain('application/json', $result->getHeader('content-type'));
        $this->isSameUrl('//httpbin.org/post?key=value', $body->find('url'));
        isSame($uniq, $body->find('form.qwerty'));
        isSame('value', $body->find('args.key'));
    }

    public function testStatus404()
    {
        $result = $this->getClient()->request('http://httpbin.org/status/404');

        isSame(404, $result->code);
    }

    public function testStatus404Exceptions()
    {
        $this->expectException(\JBZoo\HttpClient\Exception::class);

        $this->getClient([
            'exceptions' => true,
        ])->request('http://httpbin.org/status/404');
    }

    public function testStatus500()
    {
        $result = $this->getClient()->request('http://httpbin.org/status/500');
        isSame(500, $result->code);
    }

    public function testStatus500Exceptions()
    {
        $this->expectException(\JBZoo\HttpClient\Exception::class);

        $this->getClient([
            'exceptions' => true,
        ])->request('http://httpbin.org/status/500');
    }

    public function testRedirect()
    {
        $url = Url::addArg(['url' => 'http://example.com'], 'http://httpbin.org/redirect-to');

        $result = $this->getClient()->request($url);

        isSame(200, $result->code);
        isContain('text/html', $result->getHeader('content-type'));
        isContain('Example', $result->body);
    }

    public function testHeaders()
    {
        $url = 'http://httpbin.org/headers';

        $uniq = uniqid('', true);
        $result = $this->getClient([
            'headers' => ['X-Custom-Header' => $uniq],
        ])->request($url);

        $body = $result->getJSON();

        isSame(200, $result->code);
        isSame($uniq, $body->find('headers.X-Custom-Header'));
    }

    public function testGzip()
    {
        $url = 'http://httpbin.org/gzip';

        $result = $this->getClient()->request($url);

        isSame(200, $result->code);
        isSame(true, $result->getJSON()->find('gzipped'));
    }

    public function testMultiRedirects()
    {
        $url = 'http://httpbin.org/absolute-redirect/9';
        $result = $this->getClient()->request($url);
        $body = $result->getJSON();

        isSame(200, $result->code);
        $this->isSameUrl('http://httpbin.org/get', $body->get('url'));
    }

    public function testDelayError()
    {
        $this->expectException(\JBZoo\HttpClient\Exception::class);

        $this->getClient([
            'timeout'    => 2,
            'exceptions' => true,
        ])->request('http://httpbin.org/delay/5');
    }

    public function testDelayErrorExceptionsDisable()
    {
        $result = $this->getClient([
            'timeout'    => 2,
            'exceptions' => false,
        ])->request('http://httpbin.org/delay/5');

        isSame(0, $result->getCode());
        isSame([], $result->getHeaders());
        isTrue(is_string($result->getBody()));
    }

    public function testDelay()
    {
        $url = 'https://httpbin.org/delay/5';
        $result = $this->getClient()->request($url);
        $body = $result->getJSON();

        isSame(200, $result->code);
        $this->isSameUrl($url, $body->get('url'));
    }

    public function testSSL()
    {
        $url = 'https://www.google.com';
        $result = $this->getClient(['verify' => false])->request($url);

        isSame(200, $result->code);
        isContain('google', $result->body);

        $url = 'https://www.google.com';
        $result = $this->getClient(['verify' => true])->request($url);

        isSame(200, $result->code);
        isContain('google', $result->body);
    }

    public function testMultiRequest()
    {
        $responseList = $this->getClient(['user_agent' => 'Qwerty Agent v123'])->multiRequest([
            'request_0' => ['http://mockbin.org/request?qwerty=123456'],
            'request_1' => ['http://mockbin.org/request', ['key' => 'value']],
            'request_2' => [
                'http://mockbin.org/request',
                ['key' => 'value'],
                'post',
                [
                    'user_agent' => 'Qwerty Agent v456',
                    'headers'    => [
                        'x-custom-header' => '123'
                    ]
                ]
            ],
        ]);

        // Response - 0
        $request0 = $responseList['request_0']->getRequest();
        isSame('http://mockbin.org/request?qwerty=123456', $request0->get('url'));
        isSame('Qwerty Agent v123', $request0->find('options.user_agent'));
        isSame('GET', $request0->get('method'));

        $jsonBody0 = $responseList['request_0']->getJSON();
        isSame('http://mockbin.org/request?qwerty=123456', $jsonBody0->find('url'));
        isSame('GET', $jsonBody0->find('method'));
        isSame('123456', $jsonBody0->find('queryString.qwerty'));


        // Response - 1
        $request1 = $responseList['request_1']->getRequest();
        isSame('http://mockbin.org/request?key=value', $request1->get('url'));
        isSame('Qwerty Agent v123', $request1->find('options.user_agent'));
        isSame('GET', $request1->get('method'));

        $jsonBody1 = $responseList['request_1']->getJSON();
        isSame('http://mockbin.org/request?key=value', $jsonBody1->find('url'));
        isSame('GET', $jsonBody1->find('method'));
        isSame('value', $jsonBody1->find('queryString.key'));


        // Response - 2
        $request2 = $responseList['request_2']->getRequest();
        isSame('http://mockbin.org/request', $request2->get('url'));
        isSame('Qwerty Agent v456', $request2->find('options.user_agent'));
        isSame('123', $request2->find('options.headers.x-custom-header'));
        isSame('POST', $request2->get('method'));

        $jsonBody2 = $responseList['request_2']->getJSON();
        isSame('123', $jsonBody2->find('headers.x-custom-header'));
        isSame('http://mockbin.org/request', $jsonBody2->find('url'));
        isSame('POST', $jsonBody2->find('method'));
        isSame('value', $jsonBody2->find('postData.params.key'));
    }

    /**
     * @param string $expected
     * @param string $actual
     * @param string $message
     */
    private function isSameUrl(string $expected, string $actual, string $message = '')
    {
        isSame(
            str_replace(['https://', 'http://'], '//', $expected),
            str_replace(['https://', 'http://'], '//', $actual),
            $message
        );
    }

    /**
     * @param string $expected
     * @param string $actual
     * @param string $message
     */
    private function isContainUrl(string $expected, string $actual, string $message = '')
    {
        isContain(
            str_replace(['https://', 'http://'], '//', $expected),
            str_replace(['https://', 'http://'], '//', $actual),
            false,
            $message
        );
    }
}
