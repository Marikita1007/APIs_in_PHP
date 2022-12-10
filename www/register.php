<?php
//48. Add a register page to insert a new user record and generate a new API key
require __DIR__ . "/vendor/autoload.php";

if($_SERVER["REQUEST_METHOD"] === "POST"){

    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);//データベース接続の認証情報を読み込むために、.env パッケージを使用。
    
    //.envファイルの場所として現在のフォルダを指定。
    $dotenv->load();

    //.envファイルから接続設定を渡して、新しいデータベースオブジェクトを作成。
    $database = new Database($_ENV["DB_HOST"],
                            $_ENV["DB_NAME"],
                            $_ENV["DB_USER"],
                            $_ENV["DB_PASS"]);

    $conn = $database->getConnection();

    $sql = "INSERT INTO user (name, username, password_hash, api_key)
            VALUES (:name, :username, :password_hash, :api_key)";

    $stmt = $conn->prepare($sql);

    //パスワードもフォームから取得するが、入力されたのパスワードの代わりにパスワードハッシュを保存するため、まず password_hash関数を呼び出し、フォームから入力された値を渡してデフォルトのアルゴリズムを使用。
    $password_hash = password_hash($_POST["password"], PASSWORD_DEFAULT); 
    
    //!重要!:APIキーには、ランダムな文字列を生成したい。random_bytes関数は、ランダムなバイト列を生成。しかし、random_bytes関数はバイト列となり、簡単に使える文字ではない。これを文字と数字の文字列に変換する。bin2hex関数を使用。
    $api_key = bin2hex(random_bytes(16));//32文字のストリングに変換。このためこのデータベースコラム(api_key)はvarchar(32)に設定。

    $stmt->bindValue(":name", $_POST["name"], PDO::PARAM_STR);
    $stmt->bindValue(":username", $_POST["username"], PDO::PARAM_STR);
    $stmt->bindValue(":password_hash", $password_hash, PDO::PARAM_STR);//password_hash関数のアルゴリズムをプレースホルダーにバインド。
    $stmt->bindValue(":api_key", $api_key, PDO::PARAM_STR);

    $stmt->execute();

    echo "Thank you for registering. Your API key is ", $api_key;
    exit;

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <!-- Pico CSS -->
    <link rel="stylesheet" href="https://unpkg.com/@picocss/pico@latest/css/pico.min.css">
</head>
<body>
    
    <main class="container">
        <h1>Register</h1>

        <form action="" method="POST">

            <label for="name">
                Name
                <input name="name" id="name">
            </label>

            <label for="username">
                Username
                <input name="username" id="username">
            </label>

            <label for="password">
                Password
                <input type="password" name="password" id="password">
            </label>

            <button>Register</button>

        </form>
    </main>

</body>
</html>