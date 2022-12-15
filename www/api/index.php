<?php

declare(strict_types=1); //declareで厳密な型チェックを有効にし、strict_types 宣言を 1 に設定。
//declare() has to be the first statement in the script.

require __DIR__ . "/bootstrap.php";

//ini_set("display errors", "Oh");

require dirname(__DIR__) . "/vendor/autoload.php";

set_error_handler("ErrorHandler::handleError");
set_exception_handler("ErrorHandler::handleException");

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

//これを取り除くには、 parse_url 関数を使用して PHP_URL_PATH を第二引数として渡します。By doing so, it won't show the URL after ?
//ex:?page=1 wouldn't show up ,but /task/123 shows up
$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

$parts = explode("/", $path);

$resource = $parts[5];

$id = $parts[6] ?? null;

if($resource != "tasks"){
    // My server of HTTP that I'll pass in : HTTP 1.1:
    //header("HTTP/1.1 404 Not Found");

    //To avoid hard code
    //header("{$_SERVER['SERVER_PROTOCOL']} 404 Not Found");

    //http_response_code functionは、PHPのソースコードにハードコードされたリストから、自動的に理由フレーズを設定します。
    http_response_code(404);//This is recommended way !
    exit;
}

//49. Send the API key with the request: query string or request header
//認証の詳細を渡すためリクエストヘッダを使用する方が一般的。リクエストヘッダは、URLに値を追加しないので、リクエストが明確。APIキーを送信するため、X-API-keyというキーを持つヘッダーを使用するのが一般的。
//ex : http http://localhost/Udemy/APIs_in_PHP/www/api/tasks X-API-Key:APIKEY *X-API-KeyとAPIKEYの間には":"を入れること!


//$database = new Database("{{hostname with port number}}", "{{database name}}", "{{user name}}", "{{user_password}}");
$database = new Database($_ENV["DB_HOST"], $_ENV["DB_NAME"], $_ENV["DB_USER"], $_ENV["DB_PASS"]);

//51. Create a table data gateway class for the user table
$user_gateway = new UserGateway($database);

//ex request: http post http://localhost/Udemy/APIs_in_PHP/www/api/tasks "Authorization:Bearer {password}"
//var_dump($_SERVER["HTTP_AUTHORIZATION"]);
//$headers = apache_request_headers();
//echo $headers["Authorization"];

$auth = new Auth($user_gateway);

if( ! $auth->authenticateAccessToken()){
    exit;
} 

$user_id = $auth->getUserID();

//$api_key = $_GET["api-key"];
//print_r($_SERVER);//X-API-Key:APIKEY
//echo $api_key;
//exit;

//Because now we have composer Autoloader, we don't need this.
//require dirname(__DIR__) . "/src/TaskController.php";//dirname関数と__DIR__定数を使って、現在のフォルダの親フォルダを取得します。

//$database -> getConnection(); //Now .env file is working, we can remove getConneciton call 
$task_gateway = new TaskGateway($database);

$controller = new TaskController($task_gateway, $user_id);//Create a new object of the class (TaskCOntroller in src folder)

$controller->processRequests($_SERVER['REQUEST_METHOD'], $id);//$id send null if there is no id in HTTP.

