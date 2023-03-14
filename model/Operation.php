<?php
require_once "framework/Model.php";
require_once 'model/Operation.php';
require_once 'framework/Tools.php';
class Operation extends Model
{

    public $id;
    public string $title;
    public int $tricount;
    public float $amount;
    public string $operation_date;
    public int $initiator;
    public string $created_at;

    public function __construct(string $title, int $tricount, float $amount, string $operation_date, int $initiator, string $created_at, $id = NULL)
    {

        $this->title = $title;
        $this->tricount = $tricount;
        $this->amount = $amount;
        $this->operation_date = $operation_date;
        $this->initiator = $initiator; //user id
        $this->created_at = $created_at;
        $this->id = $id; //tricount id
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getOperationDate()
    {
        return $this->operation_date;
    }

    public function getTricount()
    {
        return $this->tricount;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function getInitiator()
    {

        return $this->getUserFullName();
    }

    public function getInitiatorId()
    {

        return $this->initiator;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function get_id()
    {
        return $this->id;
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

    public static function getUsersFromTricount($tricountId)
    {
        $query = self::execute("SELECT user FROM subscriptions JOIN tricounts t on t.id = subscriptions.tricount
            WHERE tricount = :tricountId", array("tricountId" => $tricountId));
        $data = $query->fetch();
        return $data;
    }


    public static function getOperationId($tricountId)
    {
        $query = self::execute("SELECT * FROM operations where tricount = :tricountId", array("tricountId" => $tricountId));
        $data = $query->fetch();
        return $data;
    }

    public static function get_operations_by_tricount($id)
    {
        $result = [];
        $query = self::execute("SELECT * FROM operations WHERE tricount = :id ORDER BY amount ASC", array("id" => $id));
        $data = $query->fetchAll();
        if ($query->rowCount() == 0) {
            return null;
        } else {
            foreach ($data as $row) {
                $operation_date = (string) $row["operation_date"];
                ;
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
        $query = self::execute("SELECT * FROM operations where initiator = :initiator", array("initiator" => $initiator));
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

    public static function getNbOfOperations($id)
    {
        $query = self::execute("SELECT count(*) FROM operations  WHERE tricount =:id", array("id" => $id));
        $data = $query->fetch();
        return $data;
    }

    public function getUserFullName()
    {
        $query = self::execute("SELECT * FROM users  WHERE users.id =:id", array("id" => $this->initiator));
        $data = $query->fetch();
        return $data["full_name"];
    }

    public static function getNumberParticipantsByOperationId($id)
    {
        $query = self::execute("SELECT count(user) FROM repartitions
        JOIN operations ON operations.id = repartitions.operation
        WHERE operations.id = :id", array("id" => $id));
        $data = $query->fetch();
        return $data;
    }

    public static function getOperationByOperationId($id)
    {
        // database connection

        $query = self::execute("SELECT * FROM operations WHERE id =:id", array("id" => $id));
        $data = $query->fetchAll();
        if ($query->rowCount() == 0) {
            return null;
        } else {
            foreach ($data as $row) {
                $operation_date = (string) $row["operation_date"];
                ;
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

    public static function total_by_user($userId, $operationId)
    {
        $query = self::execute("SELECT (SELECT o.amount/SUM(r.weight)
                                                FROM repartitions r, operations o 
                                                where r.operation = o.id 
                                                and o.id =:id GROUP BY o.amount) *  
                                                (SELECT weight FROM repartitions 
                                                WHERE user = :user AND operation = :id) 
                                                AS result LIMIT 1", array("id" => $operationId, "user" => $userId));
        $data = $query->fetch();
        return $data["result"];
    }

    public static function get_users_from_operation($operationId)
    {
        $query = self::execute("SELECT user FROM repartitions WHERE operation = :operationId", array("operationId" => $operationId));
        $data = $query->fetchAll();
        return $data;
    }

    public static function get_dette_by_operation($operationId, $userId)
    {
        $query = self::execute("SELECT (SELECT o.amount/SUM(r.weight)
                                    FROM repartitions r, operations o 
                                    where r.operation = o.id 
                                    and o.id =:operation GROUP BY o.amount) *  
                                    (SELECT weight FROM repartitions 
                                    WHERE user = :user AND operation = :operation) 
                                    AS result LIMIT 1", array("operation" => $operationId, "user" => $userId));
        $data = $query->fetch();
        return $data;
    }

    public static function get_by_id($id)
    {
        $query = self::execute("SELECT * FROM operations where id =:id", array("id" => $id));
        $data = $query->fetch();
        $operation_date = (string) $data["operation_date"];
        $created_at = (string) $data["created_at"];
        return new Operation(
            $data["title"],
            $data["tricount"],
            $data["amount"],
            $operation_date,
            $data["initiator"],
            $created_at,
            $data["id"]
        );
    }


    public function is_in_operation($operationId)
    {
        $query = self::execute(
            "SELECT user FROM repartitions WHERE operation = :id ",
            array("id" => $this->id)
        );
        if ($query->rowCount() == 0) {
            return false;
        }
        return $query;
    }

    public static function total_alberti($tricId, $userId)
    {
        $query = self::execute("SELECT DISTINCT o.initiator, SUM((rp.weight / total_weight) * o.amount) AS balance
                                FROM operations o
                                JOIN tricounts t ON t.id = o.tricount
                                JOIN (
                                SELECT operation, SUM(weight) AS total_weight
                                FROM repartitions
                                GROUP BY operation
                                ) r ON r.operation = o.id
                                JOIN repartitions rp ON rp.operation = o.id
                                WHERE t.id = :id AND o.initiator =:user
                                GROUP BY o.initiator;", array("id" => $tricId, "user" => $userId));
        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return false;
        }
        return $data["balance"];
    }



    public static function exists($id)
    {
        $query = self::execute("SELECT id FROM operations WHERE id=:id LIMIT 1", array("id" => $id));
        $data = $query->fetch();
        return $data;
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


    public static function insertRepartition($id, $weight, $initiator)
    {

        $query = self::execute("INSERT INTO repartitions (operation, weight, user) 
                                VALUES (:operation_id, :weight, :user)",
            array(
                "operation_id" => $id,
                "weight" => $weight,
                "user" => $initiator
            )
        );
        return $query;
    }

    public static function deleteRepartition($idOperation){
    //     DELETE
    // FROM repartition_template_items
    // where repartition_template=:repartition_template",
    // array("repartition_template"=>$repartition_template)
        $query = self::execute("DELETE
                        FROM repartitions where operation =:operation",
            array(
                "operation" =>$idOperation
            )
        );
        return $query;
    }

    public static function validateTitle($title)
    {
        $query = self::execute("SELECT title from operations WHERE title=:title",
            array(
                "title" => $title
            )
        );
        if ($query->rowCount() == 0) {
            return "Title already exists in the database.";
        }
        return;
    }


    public function validate()
    {
        $errors = [];

        if ((isset($this->title) && strlen($this->title) < 3)) {
            $errors[] = "Title must be at least 3 characters.";
        }

        if ($this->title && !self::validateTitle($this->title)) {
            $errors[] = "Title already exists in the database.";
        }

        if ((isset($this->amount) && ($this->amount < 0))) {
            $errors[] = "The amount must be positive.";
        }

        return $errors;

    }

    public function update(): Operation
    {

        if (!is_null($this->id)) {
            self::execute(
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
    public static function delete_by_tricount($id)
    {
        $query = self::execute("DELETE from `operations` where tricount=:id", array("id" => $id));
        if ($query->rowCount() == 0)
            return false;
        else
            return $query;
    }

    private function validateDate(string $operation_date): bool
    {
        if (preg_match("/^(\d{4})-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $operation_date)) {
            return true;
        }
        return false;
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

    public static function byTricountId($tricount)
    {
        $query = self::execute("SELECT o.*
                                FROM `operations` o
                                Where o.tricount =:id ",
            array("id" => $tricount)
        );
        $data = $query->fetchAll();
        $operation = [];
        foreach ($data as $row) {
            $operation[] = new Operation(
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

    public function get_previous_operation_by_tricount($id, $tricount)
    {
        $query = self::execute("SELECT * 
                                FROM `operations` o
                                where o.id < :id 
                                and o.tricount = :tricount
                                ORDER BY o.id DESC
                                LIMIT 1",
            array(
                "id" => $id,
                "tricount" => $tricount
            )
        );
        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return null;
        }
        $operation_date = (string) $data["operation_date"];
        ;
        $created_at = (string) $data["created_at"];
        return new Operation(
            $data["title"],
            $data["tricount"],
            $data["amount"],
            $operation_date,
            $data["initiator"],
            $created_at,
            $data["id"]
        );
    }


    public function get_next_operation_by_tricount($id, $tricount)
    {
        $query = self::execute("SELECT o.* 
                                FROM `operations` o
                                WHERE o.tricount = :tricount
                                AND o.id > :id
                                ORDER BY o.id ASC
                                LIMIT 1",
            array(
                "id" => $id,
                "tricount" => $tricount
            )
        );
        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return null;
        }
        $operation_date = (string) $data["operation_date"];
        ;
        $created_at = (string) $data["created_at"];
        return new Operation(
            $data["title"],
            $data["tricount"],
            $data["amount"],
            $operation_date,
            $data["initiator"],
            $created_at,
            $data["id"]
        );
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