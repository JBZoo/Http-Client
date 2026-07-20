# JBZoo / Http-Client

[![CI](https://github.com/JBZoo/Http-Client/actions/workflows/main.yml/badge.svg?branch=master)](https://github.com/JBZoo/Http-Client/actions/workflows/main.yml?query=branch%3Amaster)
[![Coverage Status](https://coveralls.io/repos/github/JBZoo/Http-Client/badge.svg?branch=master)](https://coveralls.io/github/JBZoo/Http-Client?branch=master)
[![Psalm Coverage](https://shepherd.dev/github/JBZoo/Http-Client/coverage.svg)](https://shepherd.dev/github/JBZoo/Http-Client)
[![Psalm Level](https://shepherd.dev/github/JBZoo/Http-Client/level.svg)](https://shepherd.dev/github/JBZoo/Http-Client)
[![CodeFactor](https://www.codefactor.io/repository/github/jbzoo/http-client/badge)](https://www.codefactor.io/repository/github/jbzoo/http-client/issues)

[![Stable Version](https://poser.pugx.org/jbzoo/http-client/version)](https://packagist.org/packages/jbzoo/http-client/)
[![Total Downloads](https://poser.pugx.org/jbzoo/http-client/downloads)](https://packagist.org/packages/jbzoo/http-client/stats)
[![Dependents](https://poser.pugx.org/jbzoo/http-client/dependents)](https://packagist.org/packages/jbzoo/http-client/dependents?order_by=downloads)
[![GitHub License](https://img.shields.io/github/license/jbzoo/http-client)](https://github.com/JBZoo/Http-Client/blob/master/LICENSE)

A simple, intuitive PHP HTTP client that provides a clean wrapper around popular HTTP libraries like Guzzle and rmccue/requests. Make HTTP requests with minimal code and maximum flexibility.

## Features

- **Simple API**: Clean, one-line HTTP requests without complex configuration
- **Multiple Backends**: Automatic driver selection (Guzzle preferred, rmccue/requests fallback)
- **Parallel Requests**: Built-in support for concurrent HTTP requests using curl_multi_*
- **Flexible Response Handling**: Access response data via methods, properties, or array syntax
- **JSON Support**: Built-in JSON parsing with JBZoo/Data integration
- **Event System**: Hook into request lifecycle with event listeners
- **PHP 8.3+ Ready**: Modern PHP with strict typing and best practices


## Requirements

- PHP 8.3 or higher
- ext-json

## Installation

```sh
composer require guzzlehttp/guzzle --no-update # Recommended, but not required
composer require jbzoo/http-client
```

## Quick Start

```php
use JBZoo\HttpClient\HttpClient;

// Simple GET request
$client = new HttpClient();
$response = $client->request('https://api.github.com/users/octocat');

echo $response->getBody(); // JSON response
echo $response->getCode(); // 200
```

## Usage
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
    'max_redirects'   => 10,        // How much to redirect?
    'user_agent'      => "It's me", // Custom UserAgent
]);

// Just request
$response = $httpClient->request('http://my.site.com/', [
    'key-1' => 'value-1',
    'key-2' => 'value-2'
], 'post');
```

### Response Methods

```php
// Get status code
$code = $response->getCode();
$code = $response->code;
$code = $response['code'];

// Get headers
$headers = $response->getHeaders();
$headers = $response->headers;
$headers = $response['headers'];
$header  = $response->getHeader('X-Custom-Header-Response');
$header  = $response->find('headers.x-custom-header-response', 'default-value', 'trim');

// Get response body
$body = $response->getBody();
$body = $response->body;
$body = $response['body'];

// Parse JSON response (uses JBZoo/Data)
$json = $response->getJSON();
$value = $json->get('key', 'default', 'trim');
$value = $json->find('key.nested', 'default', 'trim');
```

## Parallel Requests

```php
use JBZoo\HttpClient\HttpClient;

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

## Development

### Running Tests

```sh
make update          # Install/update dependencies
make test-all        # Run tests and code style checks
make test            # Run PHPUnit tests only
make codestyle       # Run code style checks only
```

### Mock Server

For testing purposes, you can start a mock HTTP server:

```sh
make start-mock-server  # Starts httpbin on port 8087
```

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Run tests (`make test-all`)
4. Commit your changes (`git commit -am 'Add amazing feature'`)
5. Push to the branch (`git push origin feature/amazing-feature`)
6. Open a Pull Request

## License

MIT - see [LICENSE](LICENSE) file for details.
