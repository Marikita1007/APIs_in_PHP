<?php

//This is a secure method to create create a Token.
class JWTCodec
{
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
                                "4226452948404D635166546A576E5A7234753778214125442A462D4A614E6452",// この引数はオンラインジェネレータで生成したキー。
                                true);

        $signature = $this->base64urlEncode($signature);//ハッシュ値に対してbase64urlEncodeメソッドを呼び出す。

        return $header . "." . $payload . "." . $signature;//JWTはheader、payload、signatureから構成され、これらは"."で区切られている。
    }

    private function base64urlEncode(string $text): string
    {
        return str_replace(
            ["+", "/", "="],
            ["-", "_", ""],
            base64_encode($text)
        );
    }
}