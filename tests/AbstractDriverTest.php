<?php

/**
 * JBZoo Toolbox - Http-Client.
 *
 * This file is part of the JBZoo Toolbox project.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @see        https://github.com/JBZoo/Http-Client
 */

declare(strict_types=1);

namespace JBZoo\PHPUnit;

use JBZoo\HttpClient\HttpClient;
use JBZoo\HttpClient\Options;
use JBZoo\Utils\Url;
use JBZoo\Utils\Xml;

abstract class AbstractDriverTest extends PHPUnit
{
    protected string $driver  = 'Auto';
    protected array  $methods = [
        'GET',
        'POST',
        'PATCH',
        'PUT',
        'DELETE', // must be the last in the list
    ];

    protected string $mockServerUrl = 'http://0.0.0.0:8087';

    public function testSimple(): void
    {
        $url    = 'https://run.mocky.io/v3/92e40ef8-6328-4b8e-af3c-9a26c72abd3c';
        $result = $this->getClient()->request($url);

        isSame(200, $result->code);
        isSame('42', $result->getHeader('x-custom-header'));
        isContain('application/json', $result->getHeader('content-type'));
        isSame('{"great-answer": "42"}', $result->body);
    }

    public function testBinaryData(): void
    {
        $result = $this->getClient()->request("{$this->mockServerUrl}/image/png");

        isSame(200, $result->getCode());
        isSame('image/png', $result->getHeader('CONTENT-TYPE'));
        isContain('PNG', $result->getBody());
    }

    public function testPostPayload(): void
    {
        if ($this->driver === 'Rmccue') {
            skip('Curl driver does not support post payload');
        }

        $uniq    = \uniqid('', true);
        $payload = \json_encode(['key' => $uniq], \JSON_THROW_ON_ERROR);

        $url    = "{$this->mockServerUrl}/anything?key=value";
        $result = $this->getClient(['exceptions' => true])->request($url, $payload, 'post');
        $body   = $result->getJSON();

        isSame($payload, $body->find('data'));
        isSame('value', $body->find('args.key'));
    }

    public function testAllMethods(): void
    {
        foreach ($this->methods as $method) {
            $uniq = \uniqid('', true);
            $url  = "{$this->mockServerUrl}/anything?method={$method}&qwerty=remove_me";

            $args    = ['qwerty' => $uniq];
            $message = 'Method: ' . $method;

            $result = $this->getClient()->request($url, $args, $method);
            $body   = $result->getJSON();

            if (!$result->getCode()) {
                fail($message . ' Body: ' . $result->getBody());
            }

            isSame(200, $result->getCode(), $message);
            isContain('application/json', $result->getHeader('Content-Type'), false, $message);
            isSame($method, $body->find('args.method'), $message);
            isSame($method, $body->find('method'), $message);

            if ($method === 'GET') {
                $this->isSameUrl(Url::addArg($args, $url), $body->find('url'), $message);
                isSame($uniq, $body->find('args.qwerty'), $message);
            } else {
                $this->isContainUrl($url, $body->find('url'), $message);
                if ($this->driver === 'Rmccue' && $method === 'DELETE') {
                    skip('DELETE is not supported with Rmccue/Requests correctly');
                }

                isSame($uniq, $body->find('form.qwerty'), $message);
            }
        }
    }

    public function testAuth(): void
    {
        $url    = "{$this->mockServerUrl}/basic-auth/user/passwd";
        $result = $this->getClient([
            'auth' => ['user', 'passwd'],
        ])->request($url);

        isSame(200, $result->code);
        isSame(true, $result->getJSON()->get('authenticated'));
        isSame('user', $result->getJSON()->get('user'));
    }

    public function testGetQueryString(): void
    {
        $uniq = \uniqid('', true);

        $siteUrl = "{$this->mockServerUrl}/get?key=value";
        $args    = ['qwerty' => $uniq];
        $url     = Url::addArg($args, $siteUrl);
        $result  = $this->getClient()->request($url, $args);

        isSame(200, $result->code);
        isContain('application/json', $result->getHeader('Content-Type'));

        $body = $result->getJSON();
        $this->isSameUrl(Url::addArg($args, $url), $body->find('url'));
        isSame($uniq, $body->find('args.qwerty'));
        isSame('value', $body->find('args.key'));
    }

    public function testUserAgent(): void
    {
        $result = $this->getClient()->request("{$this->mockServerUrl}/user-agent");
        $body   = $result->getJSON();

        isSame(200, $result->code);
        isContain('application/json', $result->getHeader('Content-Type'));
        isContain(Options::DEFAULT_USER_AGENT . ' (', $body->find('user-agent'));
    }

    public function testPost(): void
    {
        $uniq = \uniqid('', true);
        $url  = "{$this->mockServerUrl}/post?key=value";
        $args = ['qwerty' => $uniq];

        $result = $this->getClient()->request($url, $args, 'post');
        $body   = $result->getJSON();

        isSame(200, $result->code);
        isContain('application/json', $result->getHeader('content-type'));
        $this->isSameUrl("{$this->mockServerUrl}/post?key=value", $body->find('url'));
        isSame($uniq, $body->find('form.qwerty'));
        isSame('value', $body->find('args.key'));
    }

    public function testStatus404(): void
    {
        $result = $this->getClient()->request("{$this->mockServerUrl}/status/404");

        isSame(404, $result->code);
    }

    public function testStatus404Body(): void
    {
        $result = $this->getClient()->request('https://run.mocky.io/v3/54bdf866-5da9-4e15-aeb4-4d51ee870dc4');

        isSame(404, $result->code);
        is('{"error": "mock_not_found"}', $result->getBody());
        isSame('123', $result->getHeader('x-custom_header'));
    }

    public function testStatus404Exceptions(): void
    {
        $this->expectException(\JBZoo\HttpClient\Exception::class);

        $this->getClient([
            'exceptions' => true,
        ])->request("{$this->mockServerUrl}/status/404");
    }

    public function testStatus500(): void
    {
        $result = $this->getClient()->request("{$this->mockServerUrl}/status/500");
        isTrue($result->code >= 500);
    }

    public function testStatus500Exceptions(): void
    {
        $this->expectException(\JBZoo\HttpClient\Exception::class);

        $this->getClient([
            'exceptions' => true,
        ])->request("{$this->mockServerUrl}/status/500");
    }

    public function testRedirect(): void
    {
        $url = Url::addArg(['url' => 'https://google.com'], "{$this->mockServerUrl}/redirect-to");

        $result = $this->getClient()->request($url);

        isSame(200, $result->code);
        isContain('text/html', $result->getHeader('content-type'));
        isContain('google.com', $result->body);
    }

    public function testHeaders(): void
    {
        $url = "{$this->mockServerUrl}/headers";

        $uniq   = \uniqid('', true);
        $result = $this->getClient([
            'headers' => ['X-Custom-Header' => $uniq],
        ])->request($url);

        $body = $result->getJSON();

        isSame(200, $result->code);
        isSame($uniq, $body->find('headers.X-Custom-Header'));
    }

    public function testGzip(): void
    {
        $url = "{$this->mockServerUrl}/gzip";

        $result = $this->getClient()->request($url);

        isSame(200, $result->code);
        isSame(true, $result->getJSON()->find('gzipped'));
    }

    public function testMultiRedirects(): void
    {
        $url    = "{$this->mockServerUrl}/absolute-redirect/2";
        $result = $this->getClient()->request($url);
        $body   = $result->getJSON();

        isSame(200, $result->code);
        $this->isSameUrl("{$this->mockServerUrl}/get", $body->get('url'));
    }

    public function testDelayError(): void
    {
        $this->expectException(\JBZoo\HttpClient\Exception::class);

        $this->getClient([
            'timeout'    => 2,
            'exceptions' => true,
        ])->request("{$this->mockServerUrl}/delay/5");
    }

    public function testDelayErrorExceptionsDisable(): void
    {
        $result = $this->getClient([
            'timeout'    => 2,
            'exceptions' => false,
        ])->request("{$this->mockServerUrl}/delay/5");

        isSame(0, $result->getCode());
        isSame([], $result->getHeaders());
        isTrue(\is_string($result->getBody()));
    }

    public function testDelay(): void
    {
        $url    = "{$this->mockServerUrl}/delay/5";
        $result = $this->getClient()->request($url);
        $body   = $result->getJSON();

        isSame(200, $result->code);
        $this->isSameUrl($url, $body->get('url'));
    }

    public function testSSL(): void
    {
        $url = 'https://www.google.com';

        $result = $this->getClient(['verify' => false])->request($url);
        isSame(200, $result->code);
        isContain('google', $result->body);

        $result = $this->getClient(['verify' => true])->request($url);
        isSame(200, $result->code);
        isContain('google', $result->body);
    }

    public function testXmlAsResponse(): void
    {
        $result = $this->getClient()->request("{$this->mockServerUrl}/xml");

        isSame(200, $result->code);
        isSame('application/xml', $result->getHeader('Content-Type'));
        isSame('Yours Truly', $result->getXml()->find('_children.0._attrs.author'));
        isSame([
            '_node'     => '#document',
            '_text'     => null,
            '_cdata'    => null,
            '_attrs'    => [],
            '_children' => [
                [
                    '_node'  => 'slideshow',
                    '_text'  => null,
                    '_cdata' => null,
                    '_attrs' => [
                        'title'  => 'Sample Slide Show',
                        'date'   => 'Date of publication',
                        'author' => 'Yours Truly',
                    ],
                    '_children' => [
                        [
                            '_node'     => 'slide',
                            '_text'     => null,
                            '_cdata'    => null,
                            '_attrs'    => ['type' => 'all'],
                            '_children' => [
                                [
                                    '_node'     => 'title',
                                    '_text'     => 'Wake up to WonderWidgets!',
                                    '_cdata'    => null,
                                    '_attrs'    => [],
                                    '_children' => [],
                                ],
                            ],
                        ],
                        [
                            '_node'     => 'slide',
                            '_text'     => null,
                            '_cdata'    => null,
                            '_attrs'    => ['type' => 'all'],
                            '_children' => [
                                [
                                    '_node'     => 'title',
                                    '_text'     => 'Overview',
                                    '_cdata'    => null,
                                    '_attrs'    => [],
                                    '_children' => [],
                                ],
                                [
                                    '_node'     => 'item',
                                    '_text'     => null,
                                    '_cdata'    => null,
                                    '_attrs'    => [],
                                    '_children' => [
                                        [
                                            '_node'     => '#text',
                                            '_text'     => null,
                                            '_cdata'    => null,
                                            '_attrs'    => [],
                                            '_children' => [],
                                        ],
                                        [
                                            '_node'     => 'em',
                                            '_text'     => 'WonderWidgets',
                                            '_cdata'    => null,
                                            '_attrs'    => [],
                                            '_children' => [],
                                        ],
                                        [
                                            '_node'     => '#text',
                                            '_text'     => null,
                                            '_cdata'    => null,
                                            '_attrs'    => [],
                                            '_children' => [],
                                        ],
                                    ],
                                ],
                                [
                                    '_node'     => 'item',
                                    '_text'     => null,
                                    '_cdata'    => null,
                                    '_attrs'    => [],
                                    '_children' => [],
                                ],
                                [
                                    '_node'     => 'item',
                                    '_text'     => null,
                                    '_cdata'    => null,
                                    '_attrs'    => [],
                                    '_children' => [
                                        [
                                            '_node'     => '#text',
                                            '_text'     => null,
                                            '_cdata'    => null,
                                            '_attrs'    => [],
                                            '_children' => [],
                                        ],
                                        [
                                            '_node'     => 'em',
                                            '_text'     => 'buys',
                                            '_cdata'    => null,
                                            '_attrs'    => [],
                                            '_children' => [],
                                        ],
                                        [
                                            '_node'     => '#text',
                                            '_text'     => null,
                                            '_cdata'    => null,
                                            '_attrs'    => [],
                                            '_children' => [],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ], $result->getXml()->getArrayCopy());

        // TODO: fix parser in \JBZoo\Utils\Xml to find text node mixed in tags
        isSame(
            \implode("\n", [
                '<?xml version="1.0" encoding="UTF-8"?>',
                '<slideshow title="Sample Slide Show" date="Date of publication" author="Yours Truly">',
                '  <slide type="all">',
                '    <title>Wake up to WonderWidgets!</title>',
                '  </slide>',
                '  <slide type="all">',
                '    <title>Overview</title>',
                '    <item>',
                '      <em>WonderWidgets</em>',
                '    </item>',
                '    <item/>',
                '    <item>',
                '      <em>buys</em>',
                '    </item>',
                '  </slide>',
                '</slideshow>',
                '',
            ]),
            Xml::array2Dom($result->getXml()->getArrayCopy())->saveXML(),
        );
    }

    public function testMultiRequest(): void
    {
        $responseList = $this->getClient(['user_agent' => 'Qwerty Agent v123'])->multiRequest([
            'request_0' => ["{$this->mockServerUrl}/anything?qwerty=123456"],
            'request_1' => ["{$this->mockServerUrl}/anything", ['key' => 'value']],
            'request_2' => [
                "{$this->mockServerUrl}/anything",
                ['key' => 'value'],
                'post',
                [
                    'user_agent' => 'Qwerty Agent v456',
                    'headers'    => [
                        'x-custom-header' => '123',
                    ],
                ],
            ],
        ]);

        // Response - 0
        $request0 = $responseList['request_0']->getRequest();
        isSame("{$this->mockServerUrl}/anything?qwerty=123456", $request0->getUri());
        isSame('Qwerty Agent v123', $request0->getOptions()->getUserAgent());
        isSame('GET', $request0->getMethod());

        $jsonBody0 = $responseList['request_0']->getJSON();
        isSame("{$this->mockServerUrl}/anything?qwerty=123456", $jsonBody0->find('url'));
        isSame('GET', $jsonBody0->find('method'));
        isSame('123456', $jsonBody0->find('args.qwerty'));

        // Response - 1
        $request1 = $responseList['request_1']->getRequest();
        isSame("{$this->mockServerUrl}/anything?key=value", $request1->getUri());
        isSame('Qwerty Agent v123', $request1->getOptions()->getUserAgent());
        isSame('GET', $request1->getMethod());

        $jsonBody1 = $responseList['request_1']->getJSON();
        isSame("{$this->mockServerUrl}/anything?key=value", $jsonBody1->find('url'));
        isSame('GET', $jsonBody1->find('method'));
        isSame('value', $jsonBody1->find('args.key'));

        // Response - 2
        $request2 = $responseList['request_2']->getRequest();
        isSame("{$this->mockServerUrl}/anything", $request2->getUri());
        isSame('Qwerty Agent v456', $request2->getOptions()->getUserAgent());
        isSame('123', $request2->getHeaders()['x-custom-header']);
        isSame('POST', $request2->getMethod());

        $jsonBody2 = $responseList['request_2']->getJSON();
        isSame('123', $jsonBody2->find('headers.X-Custom-Header'));
        isSame("{$this->mockServerUrl}/anything", $jsonBody2->find('url'));
        isSame('POST', $jsonBody2->find('method'));
        isSame('value', $jsonBody2->find('form.key'));
    }

    protected function getClient(array $options = []): HttpClient
    {
        $options['driver'] = $this->driver;

        return new HttpClient($options);
    }

    private function isSameUrl(string $expected, string $actual, string $message = ''): void
    {
        isSame(
            \str_replace(['https://', 'http://'], '//', $expected),
            \str_replace(['https://', 'http://'], '//', $actual),
            $message,
        );
    }

    private function isContainUrl(string $expected, string $actual, string $message = ''): void
    {
        isContain(
            \str_replace(['https://', 'http://'], '//', $expected),
            \str_replace(['https://', 'http://'], '//', $actual),
            false,
            $message,
        );
    }
}
