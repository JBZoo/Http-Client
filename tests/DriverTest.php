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

use JBZoo\HttpClient\HttpClient;
use JBZoo\HttpClient\Options;
use JBZoo\HttpClient\Response;
use JBZoo\Utils\Url;

/**
 * Class DriverTest
 *
 * @package JBZoo\PHPUnit
 */
abstract class DriverTest extends PHPUnit
{
    /**
     * @var string
     */
    protected $driver = 'Undefined'; // For quick toggling tests (Auto|Guzzle|Rmccue)

    /**
     * @var array
     */
    protected $methods = ['GET', 'POST', 'PATCH', 'PUT', 'DELETE'];

    /**
     * @param array $options
     * @return HttpClient
     */
    protected function getClient($options = [])
    {
        $options['driver'] = $this->driver;

        return new HttpClient($options);
    }

    public function testSimple()
    {
        $url = 'http://www.mocky.io/v2/579b43a91100006f1bcb7734';
        $result = $this->getClient()->request($url);

        isSame(200, $result->code);
        isSame('42', $result->find('headers.x-custom-header'));
        isContain('application/json', $result->find('headers.content-type'));
        isSame('{"great-answer": "42"}', $result->body);
    }

    public function testBinaryData()
    {
        $result = $this->getClient()->request('https://httpbin.org/image/png');

        isSame(200, $result->getCode());
        isSame('image/png', $result->getHeader('Content-Type'));
        isContain('PNG', $result->getBody());
    }

    public function testPOSTPayload()
    {
        $uniq = uniqid('', true);
        $payload = json_encode(['key' => $uniq]);

        $url = 'http://mockbin.org/request?key=value';
        $result = $this->getClient()->request($url, $payload, 'post');
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
                isContain(Url::addArg($args, $url), $body->find('url'), $message);
                isSame($uniq, $body->find('queryString.qwerty'), $message);
            } else {
                isContain($url, $body->find('url'), $message);
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
        isSame(Url::addArg($args, $url), $body->find('url'));
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
        isContain('application/json', $result->find('headers.content-type'));
        isSame($url, $body->find('url'));
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
        isContain('text/html', $result->find('headers.content-type'));
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
        $body = $result->get('body', null, 'data');

        isSame(200, $result->code);
        isSame(true, $body->find('gzipped'));
    }

    public function testMultiRedirects()
    {
        $url = 'http://httpbin.org/absolute-redirect/9';
        $result = $this->getClient()->request($url);
        $body = $result->getJSON();

        isSame(200, $result->code);
        isSame('http://httpbin.org/get', $body->get('url'));
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
        isSame($url, $body->get('url'));
    }

    public function testSSL()
    {
        $url = 'https://www.google.com';
        $result = $this->getClient([
            'verify' => false,
        ])->request($url);

        isSame(200, $result->code);
        isContain('google', $result->body);
    }

    public function testMultiRequest()
    {
        $results = $this->getClient()->multiRequest([
            'request_0' => 'http://mockbin.org/request?qwerty=123456',
            'request_1' => [
                'http://mockbin.org/request',
                [
                    'args' => ['key' => 'value'],
                ],
            ],
            'request_2' => [
                'http://mockbin.org/request',
                [
                    'method' => 'post',
                    'args'   => ['key' => 'value'],
                ],
            ],
        ]);

        /** @var Response $body1 */
        $body1 = $results['request_0']->getJSON();
        isSame('GET', $body1->find('method'));
        isSame('123456', $body1->find('queryString.qwerty'));

        /** @var Response $body1 */
        $body1 = $results['request_1']->getJSON();
        isSame('GET', $body1->find('method'));
        isSame('value', $body1->find('queryString.key'));

        /** @var Response $body2 */
        $body2 = $results['request_2']->getJSON();
        isSame('POST', $body2->find('method'));
        isSame('value', $body2->find('postData.params.key'));
    }
}
