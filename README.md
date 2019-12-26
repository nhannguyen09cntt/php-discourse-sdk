# Discourse SDK

Discourse SDK for PHP (4rum.vn)

## Installation

With Composer:

```
composer require nhannguyen09cntt/php-discourse-sdk
```

Or manually add it to your composer.json:

```
{
  "require": {
    "php": "^7.2",
    "nhannguyen09cntt/php-discourse-sdk": "^1.0"
  }
}
```

## Usage

```
<?php
namespace xxxx;
use Discourse\Discourse;
...
$discourse = new Discourse();
$response = $discourse->get('/c/11.json', ['page' => 1]);
$body = $response->getDecodedBody();
$topics = $body['topic_list']['topics'];
...
```
