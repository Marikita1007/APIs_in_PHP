<?php

class Auth
{
    private int $user_id;

    public function __construct(private UserGateway $user_gateway,
                                private JWTCodec $codec)
    {
    }

    public function authenticateAPIKey() : bool
    {

        //50. Check the API key is present in the request and return 400 if not
        if(empty($_SERVER["HTTP_X_API_KEY"])){

            http_response_code(400);//400 : Bad Request
            echo json_encode(["message" => "missing API key"]);
            return false;
        }

        $api_key = $_SERVER["HTTP_X_API_KEY"];//$_SERVER URL : https://www.php.net/manual/ja/reserved.variables.server.php

        $user = $this->user_gateway->getByAPIKey($api_key);
        //52. Authenticate the API key and return a 401 status code if invalid
        //To call the request : http URL(file path) X-API-Key:USER_API_KEY
        if ($user === false){
            http_response_code(401);//401 : Unauthorized
            echo json_encode(["message" => "Invalid API key"]);
            return false;
        }

        $this->user_id = $user["id"];

        return true;
    }

    public function getUserID(): int
    {
        return $this->user_id;
    }

    public function authenticateAccessToken(): bool
    {
        if (!preg_match("/^Bearer\s+(.*)$/", $_SERVER["HTTP_AUTHORIZATION"], $matches))
        {
            http_response_code(400);
            echo json_encode(["message" => "incomplete authorization header"]);
            return false;
        }

        // $plain_text = base64_decode($matches[1], true);

        // if($plain_text === false){

        //     http_response_code(400);
        //     echo json_encode(["message" => "invalid authorization header"]);
        //     return false;
        // }

        // $data = json_decode($plain_text, true);

        // if($data === null){

        //     http_response_code(400);
        //     echo json_encode(["message" => "invalid JSON"]);
        //     return false;
        // }

        try{
            $data = $this->codec->decode($matches[1]);
        
        } catch (InvalidSignatureException){

            http_response_code(401);
            echo json_encode(["message" => "invalid signature"]);
            return false;
        } catch (TokenExpiredException){

            http_response_code(401);
            echo json_encode(["message" => "token has expired"]);
            return false;
        
        } catch (Exception $e){

            http_response_code(400);
            echo json_encode(["message" => $e->getMessage()]);
            return false;
        }
    
        $this->user_id = $data["sub"];

        return true;
    }
}