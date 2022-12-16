<?php 

declare(strict_types=1);

require __DIR__ . "/bootstrap.php";

if($_SERVER["REQUEST_METHOD"] !== "POST"){

    http_response_code(405);
    header("Allow: POST");
    exit;
}

$data = (array) json_decode(file_get_contents("php://input"), true); 

if(!array_key_exists("username", $data) ||
   !array_key_exists("password", $data)){

    http_response_code(400);
    echo json_encode(["message" => "missing login credentials"]);
    exit;
}

$database = new Database($_ENV["DB_HOST"],
                         $_ENV["DB_NAME"],
                         $_ENV["DB_USER"],
                         $_ENV["DB_PASS"]);

$user_gateway = new UserGateway($database);

$user = $user_gateway->getByUsername($data["username"]);

//Keep the json_encode message as less as possible to give details to potential attackers
if($user === false){

    http_response_code(401);
    echo json_encode(["message" => "invalid authentication"]);
    exit;
}

if (!password_verify($data["password"], $user["password_hash"])){

    http_response_code(401);
    echo json_encode(["message" => "invalid authentication"]);
    exit;
}

//echo json_encode("Successful authentication");
//63. Generate an encoded access token containing the user details
//Remember the idea with an access token is that we can authorise a request to the API without doing
$payload = [
    //In a JWT, the indexes for the items in the payload need to be specific values.
    //JWTでは、ペイロードのアイテムのインデックス("id","name")が特定の値である必要があり。url:https://www.iana.org/assignments/jwt/jwt.xhtml
    "sub" => $user["id"],//For the "id", we use "sub" as subject claim.
    "name" => $user["name"]
    //JWT(JSON Web Token Claims)の名前規定に従うことで、ペイロードは、認証に成功したときにJWTアクセストークンにエンコードされるユーザーに関するデータにエンコードされる。
];

//このアクセストークンは、ユーザーのIDと名前をJONでエンコード(0、1の組み合わせ（コード）と文字の対応表』を別の対応表に切り替えること」)し、次にBase64でエンコードする単純な文字列。このトークンを検証するときは、このプロセスを逆にして解読するだけ。これは簡単に偽造可能。base64_encodeでなく、安全なアクセストークンを作成する方法が必要。そこで、業界標準の方法、JSON Web Tokens もしくは JWTsを使用する。
//$access_token = base64_encode(json_encode($payload));

//69. Generate a JWT access token in the login endpoint containing JWT claims
$codec = new JWTCodec;
$access_token = $codec->encode($payload);

echo json_encode([
    "access_token" => $access_token
]);