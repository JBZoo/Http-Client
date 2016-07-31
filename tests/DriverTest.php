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
use JBZoo\Utils\Env;
use JBZoo\Utils\Url;

/**
 * Class DriverTest
 * @package JBZoo\PHPUnit
 */
abstract class DriverTest extends PHPUnit
{
    /**
     * @var string
     */
    protected $_driver = 'Undefined'; // For quick toggle tests (Auto|Guzzle5|Guzzle6|Rmccue)

    /**
     * @var array
     */
    protected $_methods = array('GET', 'POST', 'PATCH', 'PUT', 'DELETE');

    /**
     * @return bool
     */
    protected function _isPHP53()
    {
        return version_compare(Env::getVersion(), '5.4', '<');
    }

    /**
     * @param array $options
     * @return HttpClient
     */
    protected function _getClient($options = array())
    {
        $options['driver'] = $this->_driver;

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

    public function testBinaryData()
    {
        $result = $this->_getClient()->request('https://httpbin.org/image/png');

        isSame(200, $result->getCode());
        isSame('image/png', $result->getHeader('Content-Type'));
        isContain('PNG', $result->getBody());
    }

    public function testPOSTPayload()
    {
        $uniq    = uniqid();
        $payload = json_encode(array('key' => $uniq));

        if ($this->_isPHP53()) {
            $url    = 'http://httpbin.org/post?key=value';
            $result = $this->_getClient()->request($url, $payload, 'post');
            $body   = $result->getJSON();

            isSame('value', $body->find('args.key'));
            isSame('', $body->find('form.' . $payload));
            isSame($url, $body->find('url'));

        } else {
            $url    = 'http://mockbin.org/request?key=value';
            $result = $this->_getClient()->request($url, $payload, 'post');
            $body   = $result->getJSON();

            isSame($payload, $body->find('postData.text'));
            isSame('value', $body->find('queryString.key'));
            isSame('POST', $body->find('method'));
        }
    }

    public function testAllMethods()
    {
        foreach ($this->_methods as $method) {

            $uniq    = uniqid();
            $url     = 'http://mockbin.org/request?method=' . $method . '&qwerty=remove_me';
            $args    = array('qwerty' => $uniq);
            $message = 'Method: ' . $method;

            $result = $this->_getClient()->request($url, $args, $method);
            $body   = $result->getJSON();

            if (!$result->getCode()) {
                var_dump($result->getBody());
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
                isSame($uniq, $body->find('postData.params.qwerty'), $message);
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

        $siteUrl = 'http://httpbin.org/get?key=value';
        $args    = array('qwerty' => $uniq);
        $url     = Url::addArg($args, $siteUrl);
        $result  = $this->_getClient()->request($url, $args);

        isSame(200, $result->code);
        isContain('application/json', $result->getHeader('Content-Type'));

        $body = $result->getJSON();
        isSame(Url::addArg($args, $url), $body->find('url'));
        isSame($uniq, $body->find('args.qwerty'));
        isSame('value', $body->find('args.key'));
    }

    public function testUserAgent()
    {
        $result = $this->_getClient()->request('https://httpbin.org/user-agent');
        $body   = $result->getJSON();

        isSame(200, $result->code);
        isContain('application/json', $result->getHeader('Content-Type'));
        isContain(Options::DEFAULT_USER_AGENT . ' (', $body->find('user-agent'));
    }

    public function testPost()
    {
        $uniq = uniqid();
        $url  = 'http://httpbin.org/post?key=value';
        $args = array('qwerty' => $uniq);

        $result = $this->_getClient()->request($url, $args, 'post');
        $body   = $result->getJSON();

        isSame(200, $result->code);
        isContain('application/json', $result->find('headers.content-type'));
        isSame($url, $body->find('url'));
        isSame($uniq, $body->find('form.qwerty'));
        isSame('value', $body->find('args.key'));
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

        $body = $result->getJSON();

        isSame(200, $result->code);
        isSame($uniq, $body->find('headers.X-Custom-Header'));
    }

    public function testGzip()
    {
        $url = 'http://httpbin.org/gzip';

        $result = $this->_getClient()->request($url);
        $body   = $result->get('body', null, 'data');

        isSame(200, $result->code);
        isSame(true, $body->find('gzipped'));
    }

    public function testMultiRedirects()
    {
        $url    = 'http://httpbin.org/absolute-redirect/9';
        $result = $this->_getClient()->request($url);
        $body   = $result->getJSON();

        isSame(200, $result->code);
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
        isTrue(is_string($result->getBody()));
    }

    public function testDelay()
    {
        $url    = 'http://httpbin.org/delay/5';
        $result = $this->_getClient()->request($url);
        $body   = $result->getJSON();

        isSame(200, $result->code);
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
