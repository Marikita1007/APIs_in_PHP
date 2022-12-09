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
                //var_dump($data);
                
                $errors = $this->getValidationError($data);

                if(!empty($errors)){

                    $this->respondUnprocessableEntity($errors);
                    return;
                }

                
                $id = $this->gateway->create($data);

                $this->respondCreated($id);

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

                    $data = (array) json_decode(file_get_contents("php://input"), true);
                    
                    $errors = $this->getValidationError($data, false);
    
                    if(!empty($errors)){
    
                        $this->respondUnprocessableEntity($errors);
                        return;
                    }

                    //echo "update $id";
                    $rows = $this->gateway->update($id, $data);
                    echo json_encode(["message" => "Task updated", "rows" => $rows]);
                    break;

                case "DELETE":
                    //echo "delete $id";
                    $rows = $this->gateway->delete($id);
                    echo json_encode(["message" => "Task deleted", "rows" => $rows]);
                    break;
                
                default:
                    $this->respondMethodNotAllowed("GET, PATCH, DELETE ");
            }
        }
    }

    private function respondUnprocessableEntity(array $errors): void
    {
        http_response_code(422);
        echo json_encode(["errors" => $errors]);
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

    private function respondCreated(string $id): void
    {
        http_response_code(201);
        echo json_encode(["message" => "Task created", "id" => $id]);
    }

    //If post method has name(databa column name),but empty data, it gets inside this function.  
    //private method to validate this array.
    private function getValidationError(array $data, bool $is_new = true): array
    {
        $errors = [];

        if($is_new && empty($data["name"])){

            $errors[] = "name is required";
        }

        if(!empty($data["priority"])){

            if(filter_var($data["priority"], FILTER_VALIDATE_INT) === false ){

                $errors[] = "priority must be an integer";
            }
        }

        return $errors;
    }
}