<?php

//$ch = curl_init("https://randomuser.me/api"); We can give the URL directly like so or

$ch= curl_init(); // または、オプションとして設定することができます。
//curl_setopt($ch, CURLOPT_URL, "https://randomuser.me/api");// 転送のオプションを設定するには、curl_setopt 関数を呼び出して、ハンドル、設定したい値を示す定数、および値そのものを渡します。
//オプションにすることでいろんなURLに使える
//curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);//This returns a string instead of being output directly

curl_setopt_array($ch,[ //curl_setopt array version. Both works fine ! 
    CURLOPT_URL => "https://api.openweathermap.org/data/2.5/weather?q=London&appid=MYID",
    CURLOPT_RETURNTRANSFER => true
]);

$response = curl_exec($ch);//curl_exec() function excutes the request by passing in the handle(In this case its $ch)

$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);//ペイロードとはAPIが返してくる内容のことを指す。サーバーはステイタスコードを返す。これがよく見る404とかの数字。数字によってそのページが存在するのかとか中身が把握できる。全部三桁。一般に、200の範囲は、リクエストがOKで、サーバーが正常に返せたことを意味します。400の範囲は、リクエストに何か問題があることを意味し、500の範囲は、リクエストに何か問題があることを意味します。

curl_close($ch);//最後に、curl_closeを呼び出してハンドルを閉じ、使用していたシステムリソースを解放します。

echo $status_code, "\n";

echo $response, "\n";




