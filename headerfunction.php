<?php

$ch = curl_init();

$headers = [
    "Authorization: Client-ID MY-ID"
];

$response_headers = [];

//つまり、先ほど作成した配列変数をこの関数内で使用することができます。
//親スコープの変数にアンパサンドを付けてuse参照で渡されます。
//関数本体では、まず文字列の長さを求める関数を使って、ヘッダーの長さを求めます。
$header_callback = function ($ch, $heder) use (&$response_headers){
    $len = strlen($heder);

    $parts = explode(":", $heder, 2);//ヘッダー名とその値を配列で分離したい場合は、まず、コロンをセパレータとしてヘッダーを分割し、最大2つのパートを指定する必要があります。

    if(count($parts) < 2){
        return $len;
    }

    $response_headers[$parts[0]] = trim($parts[1]);//そうでなければ、最初の部分を配列のキーとして、2番目の部分を値として使用することになります。

    return $len;
};

curl_setopt_array($ch,[
    CURLOPT_URL => "https://api.unsplash.com/photos/random",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => $headers,
    CURLOPT_HEADERFUNCTION => $header_callback
]);

$response = curl_exec($ch);

$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

echo $status_code . "\n";

print_r($response_headers);

echo $response . "\n";