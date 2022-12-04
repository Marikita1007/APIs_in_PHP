<?php 
//Guzzle is a PHP HTTP client that makes working with APIs very simple, with easy to read object-oriented code.
//To use gizzle, it's easier to use composer : composer require guzzlehttp/guzzle

require __DIR__ . "/vendor/autoload.php";

$client = new GuzzleHttp\Client;

$response = $client->request("GET", "https://api.github.com/user/repos", [
    "headers" => [
        "Authorization" => "token MY-TOKEN",
        "User-Agent" => "Marikita1007"
    ]
]);

echo $response->getStatusCode(),"\n";

echo $response->getHeader("content-type")[0],"\n";

echo substr($response->getBody(), 0, 200), "...\n";







