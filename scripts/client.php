<?php
require_once __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;

$client = new Client();
$res = $client->request(
    'POST',
    'http://localhost:8888/actor'
);
echo $res->getStatusCode();
echo $res->getBody();
    
