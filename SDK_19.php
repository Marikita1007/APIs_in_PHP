<?php
//SDF : Softwear Developement Kit ソフトウエア開発キット

// SDKは、インストール可能な単一のパッケージまたはライブラリにあらかじめ書き込まれたコンポーネントのセットです。
// APIで言えば、SDKは基本的にAPIを呼び出してくれるツールです。
// APIを直接呼び出すことなく、アプリケーションにAPIを統合する方法です。
// また、SDKには、複数のAPIにアクセスする機能や、APIの限られた部分にアクセスする機能もあります。
// すべてのAPIにSDKがあるわけではない。
// SDKがどのように機能するかはプロバイダに依存し、標準的な構造もありません。

//https://dashboard.stripe.com/test/customers 

$api_key = "MY-API-KEY"; //These can be generated in the Stripe account dashboard.

$data = [
    "name" => "Emma",
    "email" => "emma@example.com",
];

//$id = "cus_Mu7t8FqiZjTsdX";

//This is by SDK
require __DIR__ . "/vendor/autoload.php";

$stripe = new \Stripe\StripeClient($api_key);

$customer = $stripe->customers->delete($id);

echo $customer;

//Down below is without SDK
// $ch = curl_init();

// curl_setopt_array($ch, [
//     CURLOPT_URL => 'https://api.stripe.com/v1/customers',
//     CURLOPT_RETURNTRANSFER => true,
//     CURLOPT_USERPWD => $api_key,
//     CURLOPT_POSTFIELDS => http_build_query($data) //このデータをAPIに渡すために、POSTFIELDSオプションを設定して、データの配列(array)を渡します。これにより、リクエストメソッドは自動的にPOSTに設定されます。しかし、このAPIは、データをJSONでフォーマットする代わりに、URLエンコードされたクエリー文字列としてフォーマットする必要があるため、http_build_query関数でこれができる。
// ]);

// $response = curl_exec($ch);

// curl_close($ch);

// echo $response, "\n";

