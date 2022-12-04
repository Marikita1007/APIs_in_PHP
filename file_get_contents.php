<?php
//(php index.php)を実行すると、APIへのリクエスト(file_get_contents)を行い、JSON形式のレスポンスを取得します(json_decode)。
//これをPHPの配列に変換し、そのデータから名前を取得しています。

if(! empty($_GET["name"])){ //before we call the API, check to see if the "name" element of the $_GET array isn't empty.

    //file_get_contents : Reads entire file into a string. The URL is "endpoint"
    $response = file_get_contents("https://api.agify.io/?name={$_GET['name']}");//We'll pass the contents of the text input in the query string : ?name={$_GET['name'].
    //さらに、file_get_contentsはallow_url_fopenの設定が有効になっている必要があります。
    //共有ホスティング(オンラインのサイト一般)ではこれが有効になっていないことが一般的なので、動作しないでしょう。そのため、file_get_contentsの代わりに、APIを利用する一般的な方法として、cURLが挙げられます。(https://curl.se/) cURLは、コマンドラインやコードから使用することができます。

    //To see the detail of datas, it calls API Call
    //echo $response;//To see this in console/terminal write : php FILENAME.php
    $data = json_decode($response, true);//By passing true as the second argument to jason_decode, it shows datas as arguments

    //echo $data["results"][0]["name"]["first"], "\n";
    $age = $data["age"];

    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Example</title>
</head>
<body>

<?php if(isset($age)): ?>

    Age: <?= $age ?>

<?php endif; ?>

    <form action="">
        <label for="">Name</label>
        <input name="name" id="name">

        <button>Guess Age</button>
    </form>
    
</body>
</html>



