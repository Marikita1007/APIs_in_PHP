<?php

$ch = curl_init();

$headers = [
    "Authorization: token MY-GIT-TOKEN",
    //"User-Agent : Marikita1007"
];


curl_setopt_array($ch,[
    CURLOPT_URL => "https://api.github.com/repos/Marikita1007/french_school_mvc/stargazers", //Where can I get a right https for calling the URL ?
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => $headers,
    CURLOPT_USERAGENT => "Marikita1007"
]);

$response = curl_exec($ch);

$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

echo $status_code . "\n";

echo $response . "\n";