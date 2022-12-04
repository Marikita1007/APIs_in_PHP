<?php

declare(strict_types=1); //declareで厳密な型チェックを有効にし、strict_types 宣言を 1 に設定。
//declare() has to be the first statement in the script.

//ini_set("display errors", "Oh");

require dirname(__DIR__) . "/vendor/autoload.php";

set_exception_handler("ErrorHandler::handleException");

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

//Because now we have composer Autoloader, we don't need this.
//require dirname(__DIR__) . "/src/TaskController.php";//dirname関数と__DIR__定数を使って、現在のフォルダの親フォルダを取得します。

header("Content-Type: application/json; charset=UTF-8"); //This shows Content-Type as application/json instead of text/html

$controller = new TaskController;//Create a new object of the class (TaskCOntroller in src folder)

$controller->processRequests($_SERVER['REQUEST_METHOD'], $id);//$id send null if there is no id in HTTP.  