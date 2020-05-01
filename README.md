o6c.ca client
=============

***Only 6 characters*** URL-shortener PHP client.

> See [locomotivemtl/o6c-api](https://github.com/locomotivemtl/o6c-api)

# How to install

```shell
composer require locomotivemtl/o6c-client
```

# Dependencies

- php 7.3+
- ext-json
- guzzlehttp/guzzle


# How to use

```php
$client = new \Only6\Client('https://o6c.ca', $username, $password);
$ret = $client->shorten('https://example.com/my-long-url');
echo $ret['short'];
```