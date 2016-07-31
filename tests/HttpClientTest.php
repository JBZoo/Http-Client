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

use JBZoo\HttpClient\HttpClient;
use JBZoo\HttpClient\Options;
use JBZoo\Utils\Url;

/**
 * Class HttpClientTest
 * @package JBZoo\PHPUnit
 */
class HttpClientTest extends PHPUnit
{
    /**
     * @var string
     */
    protected $_defaultDriver = 'Auto'; // For quick toggle tests (Auto|Guzzle5|Guzzle6|Rmccue)

    /**
     * @param array $options
     * @return HttpClient
     */
    protected function _getClient($options = array())
    {
        $options['driver'] = $this->_defaultDriver;

        return new HttpClient($options);
    }

    public function testSimple()
    {
        $url    = 'http://www.mocky.io/v2/579b43a91100006f1bcb7734';
        $result = $this->_getClient()->request($url);

        isSame(200, $result->code);
        isSame('42', $result->find('headers.x-custom-header'));
        isSame('application/json; charset=utf-8', $result->find('headers.content-type'));
        isSame('{"great-answer": "42"}', $result->body);
    }

    public function testPOSTPayload()
    {
        $payload = json_encode(array('key' => 'value'));

        $result = $this->_getClient()->request('http://mockbin.org/request', $payload, 'post', array(
            'verify' => false, // For travis ... =(
        ));

        $body = $result->getJSON();
        isSame($payload, $body->find('postData.text'));
    }

    public function testAllMethods()
    {
        $methods = array('get', 'post', 'post', 'put', 'delete');

        foreach ($methods as $method) {

            $uniq    = uniqid();
            $url     = 'http://httpbin.org/' . $method;
            $args    = array('qwerty' => $uniq);
            $message = 'Method: ' . $method;

            $result = $this->_getClient()->request($url, $args, $method);
            $body   = $result->getJSON();

            isSame(200, $result->getCode(), $message);
            isContain('application/json', $result->getHeader('Content-Type'), $message);

            if ($method === 'get') {
                isSame(Url::addArg($args, $url), $body->find('url'), $message);
                isSame($uniq, $body->find('args.qwerty'), $message);
            } else {
                isSame($url, $body->find('url'), $message);
                isSame($uniq, $body->find('form.qwerty'), $message);
            }
        }
    }

    public function testAuth()
    {
        $url    = 'http://httpbin.org/basic-auth/user/passwd';
        $result = $this->_getClient(array(
            'auth' => array('user', 'passwd')
        ))->request($url);

        isSame(200, $result->code);
        isSame(true, $result->getJSON()->get('authenticated'));
        isSame('user', $result->getJSON()->get('user'));
    }

    public function testGetQueryString()
    {
        $uniq = uniqid();

        $siteUrl = 'http://httpbin.org/get';
        $args    = array('qwerty' => $uniq);
        $url     = Url::addArg($args, $siteUrl);
        $result  = $this->_getClient()->request($url, $args);

        isSame(200, $result->code);
        isContain('application/json', $result->getHeader('Content-Type'));

        $body = $result->getJSON();
        isSame(Url::addArg($args, $url), $body->find('url'));
        isSame($uniq, $body->find('args.qwerty'));

        isSame(Options::DEFAULT_USER_AGENT, $body->find('headers.User-Agent'));
    }

    public function testPost()
    {
        $uniq = uniqid();
        $url  = 'http://httpbin.org/post';
        $args = array('qwerty' => $uniq);

        $result = $this->_getClient()->request($url, $args, 'post');

        isSame(200, $result->code);
        isContain('application/json', $result->find('headers.content-type'));

        $body = $result->get('body', null, 'data');
        isSame($body->find('url'), $url);
        isSame($body->find('form.qwerty'), $uniq);
    }

    public function testStatus404()
    {
        $result = $this->_getClient()->request('http://httpbin.org/status/404');

        isSame(404, $result->code);
    }

    /**
     * @expectedException \JBZoo\HttpClient\Exception
     */
    public function testStatus404Exceptions()
    {
        $this->_getClient(array(
            'exceptions' => true
        ))->request('http://httpbin.org/status/404');
    }

    public function testStatus500()
    {
        $result = $this->_getClient()->request('http://httpbin.org/status/500');

        isSame(500, $result->code);
    }

    /**
     * @expectedException \JBZoo\HttpClient\Exception
     */
    public function testStatus500Exceptions()
    {
        $this->_getClient(array(
            'exceptions' => true
        ))->request('http://httpbin.org/status/500');
    }

    public function testRedirect()
    {
        $url = Url::addArg(array('url' => 'http://example.com'), 'http://httpbin.org/redirect-to');

        $result = $this->_getClient()->request($url);

        isSame(200, $result->code);
        isContain('text/html', $result->find('headers.content-type'));
        isContain('Example', $result->body);
    }

    public function testHeaders()
    {
        $url = 'http://httpbin.org/headers';

        $uniq   = uniqid();
        $result = $this->_getClient(array(
            'headers' => array('X-Custom-Header' => $uniq)
        ))->request($url);

        isSame(200, $result->code);

        $body = $result->get('body', null, 'data');

        isSame($uniq, $body->find('headers.X-Custom-Header'));
    }

    public function testGzip()
    {
        $url = 'http://httpbin.org/gzip';

        $result = $this->_getClient()->request($url);

        isSame(200, $result->code);

        $body = $result->get('body', null, 'data');

        isSame(true, $body->find('gzipped'));
    }

    public function testMultiRedirects()
    {
        $url    = 'http://httpbin.org/absolute-redirect/9';
        $result = $this->_getClient()->request($url);

        isSame(200, $result->code);

        $body = $result->get('body', null, 'data');
        isSame('http://httpbin.org/get', $body->get('url'));
    }

    /**
     * @expectedException \JBZoo\HttpClient\Exception
     */
    public function testDelayError()
    {
        $this->_getClient(array(
            'timeout'    => 2,
            'exceptions' => true
        ))->request('http://httpbin.org/delay/5');
    }

    public function testDelayErrorExceptionsDisable()
    {
        $result = $this->_getClient(array(
            'timeout'    => 2,
            'exceptions' => false
        ))->request('http://httpbin.org/delay/5');

        isSame(0, $result->getCode());
        isSame(array(), $result->getHeaders());
        isTrue($result->getBody());
    }

    public function testDelay()
    {
        $url    = 'http://httpbin.org/delay/5';
        $result = $this->_getClient()->request($url);

        isSame(200, $result->code);

        $body = $result->get('body', null, 'data');
        isSame($url, $body->get('url'));
    }

    public function testSSL()
    {
        $url    = 'https://www.google.com';
        $result = $this->_getClient(array(
            'verify' => false
        ))->request($url);

        isSame(200, $result->code);
        isContain('google', $result->body);
    }
}
