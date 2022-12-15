<?php

class Auth
{
    public function __construct(private UserGateway $user_gateway)
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

        //52. Authenticate the API key and return a 401 status code if invalid
        //To call the request : http URL(file path) X-API-Key:USER_API_KEY
        if ($this->user_gateway->getByAPIKey($api_key) === false){
            http_response_code(401);//401 : Unauthorized
            echo json_encode(["message" => "Invalid API key"]);
            return false;
        }

        return true;
    }
}