<?php

class TaskController
{
    public function processRequests(string $method, ?string $id): void//$method coontains request methos such as GET,PATCH etc. $id comes from URL so it's a string.
    //?string $id : $id(引数)の型宣言の前に疑問符を付けることで、nullableとしてマークすることができます。
    {
        if($id === null){

            if($method == "GET"){
                echo "index";
            }elseif($method == "POST"){
                echo "create";
            }else{
                
                //http_response_code(405);
                //header("Allow: GET, POST");
                $this->respondMethodNotAllowed("GET, POST");
            }

        } else {

            switch($method){
                
                case"GET":
                    echo "show $id";
                    break;

                case "PATCH":
                    echo "update $id";
                    break;

                case "DELETE":
                    echo "delete $id";
                    break;
                
                default:
                    $this->respondMethodNotAllowed("GET, PATCH, DELETE ");
            }
        }
    }

    private function respondMethodNotAllowed(string $allowed_methods): void
    {
        http_response_code(405);
        header("Allow: $allowed_methods");
    }

}