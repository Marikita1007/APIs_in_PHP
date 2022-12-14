<?php
//2:テーブルデータゲートウェイ:オブジェクトがデータベーステーブルへのゲートウェイとして機能するデザインパターンです。アイデアは、データベースからアイテムをフェッチする責任を、それらのオブジェクトの実際の使用法から分離すること
//1:このクラスはテーブルデータゲートウェイパターンに従って、タスクテーブルにアクセスするための別クラス - 基本的には タスクテーブルへのゲートウェイとして動作するオブジェクト。

class TaskGateway
{
    private PDO $conn;//4:まず最初に、データベース接続を保存するためのプライベートプロパティを宣言します。PDO を使用しているので、PDO オブジェクトとなります。を使用しているので、そのための型宣言(PDO)を含めます。

    //3:このクラスはデータベースにアクセスする必要があるため、先ほど追加したDatabaseクラスのオブジェクトを必要とします。ここでオブジェクトを作成する代わりに、コンストラクタでオブジェクトを渡すことで、この依存関係を渡します。＝Database $database
    public function __construct(Database $database)
    {
        $this->conn = $database->getConnection();//4:次にコンストラクタで、データベースオブジェクトの getConnection メソッドを呼び出し、この をプロパティに格納します。
    }

    //getAll function : タスクのレコードをすべて取得するメソッド。これは配列を返す。
    public function getALLForUser(int $user_id): array 
    {

        $sql = "SELECT * 
                FROM task 
                WHERE user_id = :user_id
                ORDER BY name";
        
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);

        $stmt->execute();
        //return $stmt->fetchALL(PDO::FETCH_ASSOC); Insted of writing like this, write down below to 
        //change the boolean data type from 1 or 0 to true or false. This is optional.
        
        $data = [];
        
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){

            $row['is_completed'] = (bool) $row['is_completed'];

            $data[] = $row;

        }

        return $data;
    }

    public function getForUser(int $user_id, string $id): array | false
    {
        $sql = "SELECT *
                FROM task
                WHERE id = :id
                AND user_id = :user_id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);

        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if($data != false){
            
            $data['is_completed'] = (bool) $data['is_completed'];
        }
        
        return $data;
    }

    public function createForUser(int $user_id, array $data): string
    {
        $sql = "INSERT INTO task (name, priority, is_completed, user_id)
                VALUES (:name, :priority, :is_completed, :user_id)";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":name", $data["name"], PDO::PARAM_STR);


        if(empty($data["priority"])){

            $stmt->bindValue(":priority", null, PDO::PARAM_INT);

        }else{

            $stmt->bindValue(":priority", $data["priority"], PDO::PARAM_INT);
        }

        $stmt->bindValue(":is_completed", $data["is_completed"] ?? false,
                        PDO::PARAM_BOOL);

        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);
        
        $stmt->execute();
    
        return $this->conn->lastInsertId();
    }

    public function updateForUser(int $user_id, string $id, array $data): int
    {
        $fields = [];

        if(array_key_exists("name", $data)){

            $fields["name"] = [
                $data["name"],
                PDO::PARAM_STR
            ];
        }

        if(array_key_exists("priority", $data)){

            $fields["priority"] = [
                $data["priority"],
                //PDO::PARAM_INT
                $data["priority"] === null ? PDO::PARAM_NULL : PDO::PARAM_INT
            ];
        }

        if(array_key_exists("is_completed", $data)){

            $fields["is_completed"] = [
                $data["is_completed"],
                PDO::PARAM_BOOL
            ];
        }

        if(empty($fields)){
            
            return 0;//If no field to updates.   

        }else{

            //print_r($fields);
            //exit;
            $sets = array_map(function($value){

                return "$value = :$value";
            
            }, array_keys($fields));
            
            $sql = "UPDATE task"
                . " SET "  . implode(", ", $sets)
                . " WHERE id = :id"
                . " AND user_id = :user_id";

            
            $stmt = $this->conn->prepare($sql);
            
            //bind the value to the placeholder.
            $stmt ->bindValue(":id", $id, PDO::PARAM_INT);
            $stmt ->bindValue(":user_id", $user_id, PDO::PARAM_INT);

            foreach($fields as $name => $values){

                $stmt->bindValue(":$name", $values[0], $values[1]);
            }

            $stmt->execute();

            return $stmt->rowCount();//rowCountメソッド:戻り値は、更新された行の数を返す。

        }

    }

    public function deleteForUser(int $user_id, string $id): int
    {
        $sql = "DELETE FROM task
                WHERE id = :id
                AND user_id = :user_id";
        
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount();

    }
}