<?php

//echo json_encode("Successful authentication");
//63. Generate an encoded access token containing the user details
//Remember the idea with an access token is that we can authorise a request to the API without doing
$payload = [
    //In a JWT, the indexes for the items in the payload need to be specific values.
    //JWTでは、ペイロードのアイテムのインデックス("id","name")が特定の値である必要があり。url:https://www.iana.org/assignments/jwt/jwt.xhtml
    "sub" => $user["id"],//For the "id", we use "sub" as subject claim.
    "name" => $user["name"],
    //JWT(JSON Web Token Claims)の名前規定に従うことで、ペイロードは、認証に成功したときにJWTアクセストークンにエンコードされるユーザーに関するデータにエンコードされる。
    "exp" => time() + 20 //76. Add an expiry claim to the access token payload when logging in
];

//このアクセストークンは、ユーザーのIDと名前をJONでエンコード(0、1の組み合わせ（コード）と文字の対応表』を別の対応表に切り替えること」)し、次にBase64でエンコードする単純な文字列。このトークンを検証するときは、このプロセスを逆にして解読するだけ。これは簡単に偽造可能。base64_encodeでなく、安全なアクセストークンを作成する方法が必要。そこで、業界標準の方法、JSON Web Tokens もしくは JWTsを使用する。
//$access_token = base64_encode(json_encode($payload));

$access_token = $codec->encode($payload);

$refresh_token_expiry = time() + 43200;

//78. Issue a refresh token in addition to the access token when logging in
//To avoid users to relogin to get a refresh token
$refresh_token = $codec->encode([
    "sub" => $user["id"],
    "exp" => $refresh_token_expiry
]);

echo json_encode([
    "access_token" => $access_token,
    "refresh_token" => $refresh_token
]);