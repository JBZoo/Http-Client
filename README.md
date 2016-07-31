# JBZoo Http Client  [![Build Status](https://travis-ci.org/JBZoo/Http-Client.svg?branch=master)](https://travis-ci.org/JBZoo/Http-Client)      [![Coverage Status](https://coveralls.io/repos/github/JBZoo/Http-Client/badge.svg?branch=master)](https://coveralls.io/github/JBZoo/Http-Client?branch=master)

#### Useful wrapper for Guzzle and rmccue/requests

[![License](https://poser.pugx.org/JBZoo/Http-Client/license)](https://packagist.org/packages/JBZoo/Http-Client)   [![Latest Stable Version](https://poser.pugx.org/JBZoo/Http-Client/v/stable)](https://packagist.org/packages/JBZoo/Http-Client) [![Build Status](https://scrutinizer-ci.com/g/JBZoo/Http-Client/badges/build.png?b=master)](https://scrutinizer-ci.com/g/JBZoo/Http-Client/build-status/master)

### Install
```sh
composer require jbzoo/http-client
```

### Documentation

"Talk is cheap. Show me the code!" (Linus Torvalds)

```php
require_once './vendor/autoload.php'; // composer autoload.php

// Get needed classes
use JBZoo\HttpClient\HttpClient;

// Configure client (no options required!)
$client = new HttpClient([
    'auth'            => array(     // Simple HTTP auth
        'http-user-name',
        'http-password'
    ),
    'headers'         => array(     // You custom headers
        'X-Custom-Header' => 42,
    ),
    'driver'          => 'auto',    // (Auto|Guzzle5|Guzzle6|Rmccue)
    'timeout'         => 10,        // Wait in seconds
    'verify'          => false,     // check cert for SSL
    'exceptions'      => false,     // Show exceptions for 
    'allow_redirects' => true,      // Show real 3xx-header or result?
    'max_redirects'   => 10,        // How much to reirect?
    'user_agent'      => 'JBZoo/Http-Client v1.x-dev', // Custom UA
]);

// Just request
$response = $client->request('http://my.site.com/', [
    'key-1' => 'value-1'
    'key-2' => 'value-2'
], 'post');


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
$body = $response->['body'];

// Get body like JSON (see JBZoo/Data lib)
$json = $response->getJSON();
$value = $json->get('key', 'default', 'trim');
$value = $json->find('key.nested', 'default', 'trim');

```

## Unit tests and check code style
```sh
make
make test-all
```

### License

MIT
