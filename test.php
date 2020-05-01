<?php

require 'vendor/autoload.php';

$client = new Only6\Client('http://localhost:8888','mat', 'mat1234');
$res = $client->shorten('https://example.com');
echo $res['short'];