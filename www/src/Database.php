<?php

class Database
{
    public function __construct(
        private string $host,
        private string $name,
        private string $user,
        private string $password
    ){        
    }

    public function getConnection(): PDO
    {
        //$dsn stand for data source name 
        $dsn = "mysql:host={$this->host};dbname={$this->name};charset=utf8";

        return new PDO($dsn, $this->user, $this->password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            //Add down below because Json returns data as a string regardless of its data type.
            //By adding them integer becomes numbers. データベース内の数値は、JSONとして返すときに文字列に変換されない。
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_STRINGIFY_FETCHES => false,
        ]);
    }
}