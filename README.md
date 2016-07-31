# JBZoo Http-Client  [![Build Status](https://travis-ci.org/JBZoo/Http-Client.svg?branch=master)](https://travis-ci.org/JBZoo/Http-Client)      [![Coverage Status](https://coveralls.io/repos/github/JBZoo/Http-Client/badge.svg?branch=master)](https://coveralls.io/github/JBZoo/Http-Client?branch=master)

#### Simple HTTP-client. Usefull wrapper for Guzzle and rmccue/requests

[![License](https://poser.pugx.org/JBZoo/Http-Client/license)](https://packagist.org/packages/JBZoo/Http-Client)   [![Latest Stable Version](https://poser.pugx.org/JBZoo/Http-Client/v/stable)](https://packagist.org/packages/JBZoo/Http-Client) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/JBZoo/Http-Client/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/JBZoo/Http-Client/?branch=master)

### Example

```php
require_once './vendor/autoload.php'; // composer autoload.php

// Get needed classes
use JBZoo\HttpClient\HttpClient;

// Just use it!
$client = new HttpClient([

]);
$client->request('http://my.site.com/', [
    'key-1' => 'value-1'
    'key-2' => 'value-2'
], 'post');
```

## Unit tests and check code style
```sh
make
make test-all
```


### License

MIT
