<?php
// Use Unsplash to get Images 
//Authorization: Client-ID YOUR_ACCESS_KEY
$ch = curl_init();

//Common response headers include details about the response body, like its length, the language it uses
//and the type, for example, HTML, JSON and so on.
$headers = [//This time using a header to call the API 
    "Authorization: Client-ID MY-ID"
];

curl_setopt_array($ch,[
    CURLOPT_URL => "https://api.unsplash.com/photos/random",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => $headers,
    CURLOPT_HEADER => true //To view all the response headers, we set the CURLOPT_HEADER setting to true.
]);

$response = curl_exec($ch);

$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

$content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

$content_length = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);


curl_close($ch);

echo $status_code . "<br>";
echo $content_type . "<br>";
echo $content_type . "<br>";
echo $response . "<br>";