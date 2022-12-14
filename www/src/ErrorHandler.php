<?php 

class ErrorHandler
{

    //if post methods is sent with empty data, this error handler shows up 
    public static function handleError(
        int $errno,
        string $errstr,
        string $errfile,
        int $errline): void
    {
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }

    public static function handleException(Throwable $exception): void
    {   
        http_response_code(500);//This shows 500 Internal Server Error instead of 200

        echo json_encode([
            "code" => $exception->getCode(),
            "message" => $exception->getMessage(),
            "file" => $exception->getFile(),//ファイルのパスを表示
            "line" => $exception->getLine()//行番号を表示
        ]);
    }
}