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
    public function getALL(): array
    {

        $sql = "SELECT * 
                FROM task 
                ORDER BY name";
        
        $stmt = $this->conn->query($sql);

        //return $stmt->fetchALL(PDO::FETCH_ASSOC); Insted of writing like this, write down below to 
        //change the boolean data type from 1 or 0 to true or false. This is optional.
        $data = [];
        
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){


            $row['is_completed'] = (bool) $row['is_completed'];

            $data[] = $row;
        }

        return $data;
        
    }

}