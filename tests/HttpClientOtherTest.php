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

use JBZoo\Event\EventManager;
use JBZoo\HttpClient\HttpClient;
use JBZoo\HttpClient\HttpCodes;
use JBZoo\HttpClient\Request;
use JBZoo\HttpClient\Response;

final class HttpClientOtherTest extends PHPUnit
{
    protected string $mockServerUrl = 'http://0.0.0.0:8087';

    protected string $jsonFixture = '{"key-1":"value-1","key-2":"value-2"}';

    public function testGetSameJSONFromResponse(): void
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

    public function testGetRequestDefault(): void
    {
        $client   = new HttpClient();
        $response = $client->request("{$this->httpBinHost}/get");

        isSame('JBZoo/Http-Client (Guzzle)', $response->getJSON()->find('headers.User-Agent'));

        $request = $response->getRequest();
        isSame("{$this->httpBinHost}/get", $request->getUri());
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
            'user_agent'      => 'JBZoo/Http-Client',
        ], $request->getOptions()->toArray());
    }

    public function testGetRequestGlobalOptions(): void
    {
        $client   = new HttpClient(['user_agent' => 'Qwerty Client']);
        $response = $client->request("{$this->httpBinHost}/get", ['param' => 'value']);

        isSame('Qwerty Client', $response->getJSON()->find('headers.User-Agent'));

        $request = $response->getRequest();
        isSame("{$this->httpBinHost}/get?param=value", $request->getUri());
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
            'user_agent'      => 'Qwerty Client',
        ], $request->getOptions()->toArray());
    }

    public function testGetRequestRequestOptions(): void
    {
        $client   = new HttpClient();
        $response = $client->request("{$this->httpBinHost}/post", ['param' => 'value'], 'POST', [
            'user_agent' => 'Qwerty Client2',
        ]);

        isSame('Qwerty Client2', $response->getJSON()->find('headers.User-Agent'));

        $request = $response->getRequest();
        isSame("{$this->httpBinHost}/post", $request->getUri());
        isSame(['param' => 'value'], $request->getArgs());
        isSame('POST', $request->getMethod());
        isSame([
            'auth'            => [],
            'headers'         => [],
            'driver'          => 'Guzzle',
            'timeout'         => 10,
            'verify'          => true,
            'exceptions'      => false,
            'allow_redirects' => true,
            'max_redirects'   => 10,
            'user_agent'      => 'Qwerty Client2',
        ], $request->getOptions()->toArray());
    }

    public function testGetRequestRequestOptionsWithPostBody(): void
    {
        $client   = new HttpClient();
        $response = $client->request("{$this->httpBinHost}/post", 'qwerty', 'POST', [
            'user_agent' => 'Qwerty Client2',
        ]);

        isSame('Qwerty Client2', $response->getJSON()->find('headers.User-Agent'));

        $request = $response->getRequest();
        isSame("{$this->httpBinHost}/post", $request->getUri());
        isSame('qwerty', $request->getArgs());
        isSame('POST', $request->getMethod());
        isSame([
            'auth'            => [],
            'headers'         => [],
            'driver'          => 'Guzzle',
            'timeout'         => 10,
            'verify'          => true,
            'exceptions'      => false,
            'allow_redirects' => true,
            'max_redirects'   => 10,
            'user_agent'      => 'Qwerty Client2',
        ], $request->getOptions()->toArray());
    }

    public function testGetRequestMergingOptions(): void
    {
        $randomValue = \random_int(0, 100000);

        $client = new HttpClient([
            'user_agent' => 'Qwerty Client3',
            'driver'     => 'Guzzle',
        ]);

        $response = $client->request("{$this->httpBinHost}/get?key=val", ['param' => 'value'], 'GET', [
            'user_agent' => 'Custom Agent',
            'headers'    => ['X-Custom-Header' => $randomValue],
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
        isSame("{$this->httpBinHost}/get?key=val&param=value", $request->getUri());
        isSame(null, $request->getArgs());
        isSame('GET', $request->getMethod());
        isSame([
            'auth'            => [],
            'headers'         => ['X-Custom-Header' => $randomValue],
            'driver'          => 'Guzzle',
            'timeout'         => 10,
            'verify'          => true,
            'exceptions'      => false,
            'allow_redirects' => true,
            'max_redirects'   => 10,
            'user_agent'      => 'Custom Agent',
        ], $request->getOptions()->toArray());

        isSame($request->toArray(), $response->toArray()['request']);

        isSame(['uri', 'method', 'args', 'headers', 'options'], \array_keys($request->toArray()));
        isSame(['request', 'response'], \array_keys($response->toArray()));
        isSame(['code', 'body', 'headers', 'time'], \array_keys($response->toArray()['response']));
    }

    public function testCheckDefaultDriver(): void
    {
        $client = new HttpClient();

        $response = $client->request("{$this->httpBinHost}/user-agent");
        isSame('JBZoo/Http-Client (Guzzle)', $response->getJSON()->get('user-agent'));
    }

    public function testEventManager(): void
    {
        $eManager = new EventManager();

        $client = new HttpClient();
        $client->setEventManager($eManager);

        $urlToTestGet = "{$this->httpBinHost}/get";

        $counter = 0;
        $eManager
            ->once(
                'jbzoo.http.request.before',
                static function (HttpClient $client, Request $request) use (&$counter, $urlToTestGet): void {
                    isSame($urlToTestGet, $client->getLastRequest()->getUri());
                    isSame($urlToTestGet, $request->getUri());
                    $counter++;
                },
            )
            ->once(
                'jbzoo.http.request.after',
                static function (HttpClient $client, Response $response, Request $request) use (&$counter, $urlToTestGet): void {
                    isSame($urlToTestGet, $client->getLastRequest()->getUri());
                    isSame($urlToTestGet, $request->getUri());
                    isContain('0.0.0.0', $response->getJSON()->find('headers.Host'));
                    $counter++;
                },
            );

        $response = $client->request($urlToTestGet);
        isSame('0.0.0.0:8087', $response->getJSON()->find('headers.Host'));

        isSame(2, $counter);
    }

    public function testEventManagerException(): void
    {
        $eManager = new EventManager();
        EventManager::setDefault($eManager);

        $client = new HttpClient(['exceptions' => true]);
        $client->setEventManager($eManager);

        $counter = 0;
        $eManager
            ->once('jbzoo.http.exception', static function (\Exception $exception) use (&$counter): void {
                isSame(404, $exception->getCode());
                $counter++;
            });

        try {
            $client->request("{$this->httpBinHost}/status/404");
            fail();
        } catch (\Exception $exception) {
            success();
        }

        isSame(1, $counter);
    }

    public function testHttpCodes(): void
    {
        isSame(true, HttpCodes::isSuccessful(200));
        isSame(true, HttpCodes::isRedirect(301));
        isSame(true, HttpCodes::isError(400));
        isSame(true, HttpCodes::isNotFound(404));
        isSame(true, HttpCodes::isFatalError(500));
        isSame(true, HttpCodes::isForbidden(403));
        isSame(true, HttpCodes::isUnauthorized(401));
        isSame(true, HttpCodes::hasAccess(200));
        isSame(false, HttpCodes::hasAccess(403));
        isSame(false, HttpCodes::hasAccess(401));
        isSame('OK', HttpCodes::getDescriptionByCode(200));
        isSame(null, HttpCodes::getDescriptionByCode(2000));
    }
}
