# JBZoo / Http-Client

[![Build Status](https://travis-ci.org/JBZoo/Http-Client.svg?branch=master)](https://travis-ci.org/JBZoo/Http-Client)    [![Coverage Status](https://coveralls.io/repos/JBZoo/Http-Client/badge.svg)](https://coveralls.io/github/JBZoo/Http-Client?branch=master)    [![Psalm Coverage](https://shepherd.dev/github/JBZoo/Http-Client/coverage.svg)](https://shepherd.dev/github/JBZoo/Http-Client)    
[![Stable Version](https://poser.pugx.org/jbzoo/http-client/version)](https://packagist.org/packages/jbzoo/http-client)    [![Latest Unstable Version](https://poser.pugx.org/jbzoo/http-client/v/unstable)](https://packagist.org/packages/jbzoo/http-client)    [![Dependents](https://poser.pugx.org/jbzoo/http-client/dependents)](https://packagist.org/packages/jbzoo/http-client/dependents?order_by=downloads)    [![GitHub Issues](https://img.shields.io/github/issues/jbzoo/http-client)](https://github.com/JBZoo/Http-Client/issues)    [![Total Downloads](https://poser.pugx.org/jbzoo/http-client/downloads)](https://packagist.org/packages/jbzoo/http-client/stats)    [![GitHub License](https://img.shields.io/github/license/jbzoo/http-client)](https://github.com/JBZoo/Http-Client/blob/master/LICENSE)



Just make HTTP requests in one line and don't care about terrible syntax of GuzzleHttp ;)


## Install
```sh
composer require guzzlehttp/guzzle --no-update # Recomended, but not required
composer require jbzoo/http-client
```

### Usage
```php
use JBZoo\HttpClient\HttpClient;

// Configure client (no options required!)
$httpClient = new HttpClient([
    'auth'            => [          // Simple HTTP auth
        'http-user-name',
        'http-password'
    ],
    'headers'         => [          // Your custom headers
        'X-Custom-Header' => 42,
    ],
    'driver'          => 'auto',    // (Auto|Guzzle5|Guzzle6|Rmccue)
    'timeout'         => 10,        // Wait in seconds
    'verify'          => false,     // Check cert for SSL
    'exceptions'      => false,     // Show exceptions for statuses 4xx and 5xx
    'allow_redirects' => true,      // Show real 3xx-header or result?
    'max_redirects'   => 10,        // How much to reirect?
    'user_agent'      => "It's me", // Custom UserAgent
]);

// Just request
$response = $httpClient->request('http://my.site.com/', [
    'key-1' => 'value-1',
    'key-2' => 'value-2'
], 'post');
```

Methods of response
```php
// Get code
$code = $response->getCode();
$code = $response->code;
$code = $response['code'];

// Get headers
$headers = $response->getHeaders();
$headers = $response->headers;
$headers = $response['headers'];
$header  = $response->getHeader('X-Custom-Header-Response');
$header  = $response->find('headers.x-custom-header-response', 'default-value', 'trim');

// Get body
$body = $response->getBody();
$body = $response->body;
$body = $response['body'];

// Get body like JSON (see JBZoo/Data lib)
$json = $response->getJSON();
$value = $json->get('key', 'default', 'trim');
$value = $json->find('key.nested', 'default', 'trim');
```


## Asynchronous requests (curl_multi_* for parallels)
```php
$httpClient = new HttpClient();

$results = $httpClient->multiRequest(array(
    'request_0' => 'http://mockbin.org/request',
    
    'request_1' => ['http://mockbin.org/request', [
        'args' => ['key' => 'value']
    ]],
    
    'request_2' => ['http://mockbin.org/request', [
        'method' => 'post',
        'args'   => ['key' => 'value'],
        'headers'         => [
            'X-Custom-Header' => 42,
        ],
        'timeout'         => 10,
        'verify'          => false,
        'exceptions'      => false,
        'allow_redirects' => true,
        'max_redirects'   => 10, 
        'user_agent'      => 'JBZoo/Http-Client v1.x-dev'
    ]]
]);

$results['request_0']->getBody(); 
$results['request_1']->getBody(); 
$results['request_2']->getBody();
```

## Unit tests and check code style
```sh
make update
make test-all
```

### License

MIT
