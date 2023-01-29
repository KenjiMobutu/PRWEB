<?php
require_once "framework/Model.php";
require_once 'model/Operation.php';
class Operation extends Model{

    public  $id;
    public string $title;
    public int $tricount;
    public float $amount;
    public String $operation_date;
    public int $initiator;
    public String $created_at;

    public function __construct(string $title, int $tricount, float $amount, string $operation_date, int $initiator, string $created_at, $id=NULL)
    {

        $this->title = $title;
        $this->tricount = $tricount;
        $this->amount = $amount;
        $this->operation_date = $operation_date;
        $this->initiator = $initiator; //user id
        $this->created_at = $created_at;
        $this->id = $id; //tricount id
    }

    public function getTitle(){
        return $this->title;
    }

    public function getTricount(){
        return $this->tricount;
    }

    public function getAmount(){
        return $this->amount;
    }

    public function getInitiator(){

        return $this->getUserFullName();
    }

    public function getCreatedAt(){
        return $this->created_at;
    }

    public function get_id(){
        return $this->id;
    }

    public static function getNbOfOperations($id){
        $query = self::execute("SELECT count(*) FROM operations  WHERE tricount =:id", array("id"=>$id));
        $data=$query->fetch();
        return $data;
    }

    public function getUserFullName(){
        $query = self::execute("SELECT * FROM users  WHERE users.id =:id", array("id"=>$this->initiator));
        $data=$query->fetch();
        return $data["full_name"];
    }

    public static function getNumberParticipantsByOperationId($id){
        $query = self::execute("SELECT count(user) FROM repartitions
        JOIN operations ON operations.id = repartitions.operation
        WHERE operations.id = :id", array("id"=>$id));
        $data = $query->fetch();
        return $data;
    }

    public static function getOperationByOperationId($id) {
        // database connection
        
        $query = self::execute("SELECT * FROM operations WHERE id =:id", array("id" => $id));
        $data = $query->fetchAll();
        if ($query->rowCount() == 0) {
            return null;
        } else
        {
            foreach($data as $row){      
                $operation_date = (string) $row["operation_date"];;
                $created_at = (string) $row["created_at"];
                $result = new Operation(
                    $row["title"],
                    $row["tricount"],
                    $row["amount"],
                    $operation_date,
                    $row["initiator"],
                    $created_at,
                    $row["id"]
                );
            }
        }
        return $result;
    }

    public function insert()
    {
        $query = self::execute(
            "INSERT INTO `operations` (`title`, `tricount`, `amount`, `operation_date`, `initiator`, `created_at`)
                    VALUES (:title,
                            :tricount,
                            :amount,
                            :operation_date,
                            :initiator,
                            :created_at)",
            array(
                "title" => $this->title,
                "tricount" => $this->tricount,
                "amount" => $this->amount,
                "operation_date" => $this->operation_date,
                "initiator" => $this->initiator,
                "created_at" => $this->created_at
            )
        );
        $this->setOperationId();
        return $query->fetch();
    }


    public function validate()
    {
        $errors = [];

        if ((isset($this->title) && strlen($this->title) < 3)) {
            $errors[] = "Title must be at least 3 characters.";
        }

        if ((isset($this->amount) && ($this->amount < 0))) {
            $errors[] = "The amount must be positive.";
        }

        return $errors;

    }

    public function update(): Operation
    {

        if (!is_null($this->id)) {
            $query = self::execute(
                "UPDATE operations SET
            
            `title`=:title,
            `tricount`=:tricount,
            `amount`=:amount,
            `operation_date`=:operation_date,
            `initiator`=:initiator,
            `created_at`=:created_at WHERE ID = :ID",
                array(
                    "ID" => $this->id,
                    "title" => $this->title,
                    "tricount" => $this->tricount,
                    "amount" => $this->amount,
                    "operation_date" => $this->operation_date,
                    "initiator" => $this->initiator,
                    "created_at" => $this->created_at,
                )
            );
        } else {
            self::execute(
                "INSERT INTO operations(
                 `title`,
                 `tricount`,
                 `amount`,
                 `operation_date`,
                 `initiator`,
                 `created_at`)
            VALUES(
                   :title,
                   :tricount,
                   :amount,
                   :operation_date,
                   :initiator,
                   :created_at)",
                [
                    
                    "title" => $this->title,
                    "tricount" => $this->tricount,
                    "amount" => $this->amount,
                    "operation_date" => $this->operation_date,
                    "intitiator" => $this->initiator,
                    "created_at" => $this->created_at,
                ]
            );
            $this->id = self::lastInsertId();
        }

        return $this;
    }

    public function delete()
    {
        $query0 = self::execute("DELETE FROM `repartitions` WHERE operation = :id", array("id" => $this->id));
        $query1 = self::execute("DELETE FROM `operations` WHERE id=:id", array("id" => $this->id));
        $data[] = $query0->fetchAll();
        $data[] = $query1->fetchAll();
        return $data;
    }

    private function validateDate(string $operation_date): bool
    {
        if (preg_match("/^(\d{4})-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $operation_date)) {
            return true;
        }
        return false;
    }

    public static function get_tricount_by_id(int $id): ?Operation
    {
        $query = self::execute("SELECT * FROM operations where id = :id", ["id" => $id]);
        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return null;
        } else {
            return new Operation(
                $data["title"],
                $data["tricount"],
                $data["amount"],
                $data["operation_date"],
                $data["initiator"],
                $data["created_at"],
                $data["id"]
            );
        }
    }

    // public static function getOperationByTricountId(int $id): array
    // {
    //     $result = [];
    //     $query = self::execute("SELECT * FROM operations where tricount = :id", array("id"=>$id));
    //     $data = $query->fetchAll();
    //     if ($query->rowCount() == 0) {
    //         return $result;
    //     } else {
    //         // echo '<pre>';
    //         // print_r($data);
    //         // echo '</pre>';
    //         // die();
    //         foreach ($data as $row) {
    //             $result[] = new Operation(
    //                 $row["id"],
    //                 $row["title"],
    //                 $row["tricount"],
    //                 $row["amount"],
    //                 strtotime($row["operation_date"]->format('Y/m/d')),
    //                 // new DateTime($row["operation_date"]->format('Y-m-d')),
    //                 $row["initiator"],
    //                 new DateTime($row["created_at"])
    //             );
    //         }
    //     }
    //     return $result;
    // }

    public static function getOperationId($tricountId){
        $query = self::execute("SELECT * FROM operations where tricount = :tricountId", array("tricountId"=>$tricountId));
        $data = $query->fetch();
        return $data;
    }

    public static function get_operations_by_tricount($id){
        $result = [];
        $query = self::execute("SELECT * FROM operations WHERE tricount = :id ORDER BY amount ASC",array("id"=>$id));
        $data = $query->fetchAll();
        if ($query->rowCount() == 0) {
            return null;
        } else
        {
            foreach($data as $row){
                $operation_date = (string) $row["operation_date"];;
                $created_at = (string) $row["created_at"];
                $result[] = new Operation(
                    $row["title"],
                    $row["tricount"],
                    $row["amount"],
                    $operation_date,
                    $row["initiator"],
                    $created_at,
                    $row["id"]
                );
            }
        }
        return $result;
    }

    public static function getOperationByUserID(int $initiator): array
    {
        $result = [];
        $query = self::execute("SELECT * FROM operations where initiator = :initiator", array("initiator"=>$initiator));
        $data = $query->fetchAll();
        if ($query->rowCount() == 0) {
            return $result;
        } else {
            foreach ($data as $row) {
                $result[] = new Operation(
                    $row["title"],
                    $row["tricount"],
                    $row["amount"],
                    date_format($row["operation_date"], "Y-m-d"),
                    $row["initiator"],
                    $row["created_at"],
                    $row["id"]
                );
            }

        }
        return $result;
    }

    public function setOperationId()
    {
        $query = self::execute("SELECT id FROM operations WHERE id = :id", array("id" => Model::lastInsertId()));
        $data = $query->fetchAll();
        foreach ($data as $row) {
            $id = $row['id'];
        }
        $this->setId($id);
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public static function byTricountId($tricount){
        $query = self::execute("SELECT o.*
                                FROM `operations` o
                                Where o.tricount =:id ",
                              array("id"=>$tricount));
      $data = $query->fetchAll();
        $operation = [];
      foreach ($data as $row) {
        $operation[] =new Operation(
            $row['id'],
            $row['title'],
            $row['tricount'],
            $row['amount'],
            $row['operation_date'],
            $row['initiator'],
            $row['created_at']
        );
      }
      return $operation;
    }


    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function setTricount(int $tricount): void
    {
        $this->tricount = $tricount;
    }

    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    public function setOperation_date(string $operation_date): void
    {
        $this->operation_date = $operation_date;
    }

    public function setInitiator(int $initiator): void
    {
        $this->initiator = $initiator;
    }

    public function setCreated_at(string $created_at): void
    {
        $this->created_at = $created_at;
    }

    


}
?>
