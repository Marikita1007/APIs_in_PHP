<?php

class TaskController
{
    public function __construct(private TaskGateway $gateway)
    {

    }

    public function processRequests(string $method, ?string $id): void//$method coontains request methos such as GET,PATCH etc. $id comes from URL so it's a string.
    //?string $id : $id(引数)の型宣言の前に疑問符を付けることで、nullableとしてマークすることができます。
    {
        if($id === null){

            if($method == "GET"){
                //echo "index";
                echo json_encode($this->gateway->getALL());
                
            }elseif($method == "POST"){
                //echo "create";
                //print_r($_POST); 
                $data = (array) json_decode(file_get_contents("php://input"), true);
                var_dump($data);

            }else{
                
                //http_response_code(405);
                //header("Allow: GET, POST");
                $this->respondMethodNotAllowed("GET, POST");
            }

        } else {

            $task = $this->gateway->get($id);

            if($task === false){

                $this->respondNotFound($id);
                return;//If this does happen, we don't want to continue in this method, so we'll simply return at this point.
            
            }

            switch($method){
                
                case"GET":
                    //echo "show $id";
                    //echo json_encode($this->gateway->get($id));
                    echo json_encode($task);//We added 404(respondNotFound function) for not found id numbers so we show $task.
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

    private function respondNotFound(string $id): void
    {
        http_response_code(404);
        echo json_encode(["message" => "Task with ID $id not found"]);
    }

}