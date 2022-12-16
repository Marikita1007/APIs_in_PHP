<?php

//This is a secure method to create create a Token.
class JWTCodec
{
    public function __construct(private string $key)
    {
        
    }

    public function encode(array $payload): string
    {
        $header = json_encode([
            "type" => "JWT",
            "alg" => "HS256"
        ]);
        $header = $this->base64urlEncode($header); 

        $payload = json_encode($payload);
        $payload = $this->base64urlEncode($payload);


        // 規格によれば、秘密キーは少なくともハッシュ出力と同じサイズの56ビットが必要。
        $signature = hash_hmac("sha256",// アルゴリズムに「sha256」を指定して、hash_hmac関数を呼び出す。
                                $header . "." . $payload,// ハッシュ化するデータは、トークンのヘッダー部分とペイロード部分を"."で区切ったもの。
                                $this->key,// この引数はオンラインジェネレータで生成したキー。
                                true);

        $signature = $this->base64urlEncode($signature);//ハッシュ値に対してbase64urlEncodeメソッドを呼び出す。

        return $header . "." . $payload . "." . $signature;//JWTはheader、payload、signatureから構成され、これらは"."で区切られている。
    }

    //70. Add a method to decode the payload from the JWT
    public function decode(string $token): array
    {
        if(preg_match("/^(?<header>.+)\.(?<payload>.+)\.(?<signature>.+)$/",
                $token,
                $matches) !== 1){
            
            throw new InvalidArgumentException("invalid token format"); 
        }

        // 規格によれば、秘密キーは少なくともハッシュ出力と同じサイズの56ビットが必要。
        $signature = hash_hmac("sha256",// アルゴリズムに「sha256」を指定して、hash_hmac関数を呼び出す。
                                $matches["header"] . "." . $matches["payload"],// ハッシュ化するデータは、トークンのヘッダー部分とペイロード部分を"."で区切ったもの。
                                //71. Pass in the secret key used for hashing as a dependency
                                $this->key,// この引数はオンラインジェネレータで生成したキー。
                                true);

        $signature_from_token = $this->base64urlDecode($matches["signature"]);

        if(!hash_equals($signature, $signature_from_token)){

            //throw new Exception("signature doesn't match");
            throw new InvalidSignatureException;
        }

        $payload = json_decode($this->base64urlDecode($matches["payload"]), true);

        return $payload;
    }

    private function base64urlEncode(string $text): string
    {
        return str_replace(
            ["+", "/", "="],
            ["-", "_", ""],
            base64_encode($text)
        );
    }

    //70. Add a method to decode the payload from the JWT
    private function base64urlDecode(string $text): string
    {
        return base64_decode(str_replace(
            ["-", "_"],
            ["+", "/"],
            $text)
        );
    } 

}