<?php
require_once 'framework/Model.php';

class Operation extends Model
{

    private int $id;
    private string $title;
    private int $tricount;
    private ?int $amount;
    private ?DateTime $operation_date;
    private int $initiator;
    private ?Datetime $created_at;

    public function __construct(int $id, string $title, int $tricount, int $amount, DateTime $operation_date, int $initiator, Datetime $created_at)
    {
        
        $this->$id = $id; //tricount id
        $this->$title = $title;
        $this->$tricount = $tricount;
        $this->$amount = $amount;
        $this->$operation_date =$operation_date;
        $this->$initiator = $initiator; //user id
        $this->$created_at = $created_at;
    }


    public function create()
    {
        $query = self::execute(
            "INSERT INTO `operations` (`id`, `title`, `tricount`, `amount`, `operation_date`, `inititator`,`created_at`)
                        VALUES (:id,
                                :title,
                                :tricount,
                                :amount,
                                :operation_date
                                (SELECT u.ID from User u where u.ID = :user),
                                :created_at)",
            array(
                "id" => $this->id,
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

    }

    public function update(): Operation
    {

        if (!is_null($this->id)) {
            $query = self::execute(
                "UPDATE operations SET
            `id`=:id,
            `title`=:title,
            `tricount`=:tricount,
            `amount`=:amount,
            `operation_date`=:operation_date,
            `initiator`=:initiator,
            `created_at`=:created_at WHERE ID = :ID",
                array(
                    "id" => $this->id,
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
                 `id`,
                 `title`,
                 tricount,
                 `amount`,
                 `operation_date`,
                 `initiator`,
                 `created_at`)
            VALUES(:id,
                   :title,
                   :Title,
                   :tricount,
                   :amount,
                   :operation_date,
                   :initiator,
                   :created_at)",
                [
                    "id" => $this->id,
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
        $query0 = self::execute("DELETE FROM `operations` WHERE initiator = :id", array("id" => $this->id));
        $query1 = self::execute("DELETE FROM `tricounts` WHERE id = :id", array("id" => $this->id));
        $query2 = self::execute("DELETE FROM `repartitions` WHERE operation=:id", array("id" => $this->id));
        $data[] = $query0->fetchAll();
        $data[] = $query1->fetchAll();
        $data[] = $query2->fetchAll();
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
                $data["id"],
                $data["title"],
                $data["tricount"],
                $data["amount"],
                $data["operation_date"],
                $data["initiator"],
                $data["created_at"]
            );
        }
    }

    public static function getOperationByTricountId(int $id): array
    {
        $result = [];
        $query = self::execute("SELECT * FROM operations where tricount = :id", array("id"=>$id));
        $data = $query->fetchAll();
        if ($query->rowCount() == 0) {
            return $result;
        } else {
            // echo '<pre>';
            // print_r($data);
            // echo '</pre>';
            // die();
            foreach ($data as $row) {
                $result[] = new Operation(
                    $row["id"],
                    $row["title"],
                    $row["tricount"],
                    $row["amount"],
                    strtotime($row["operation_date"]->format('Y/m/d')),
                    // new DateTime($row["operation_date"]->format('Y-m-d')),
                    $row["initiator"],
                    new DateTime($row["created_at"])
                );
            }
        }
        return $result;
    }

    public static function getOperationId($tricountId){
        $query = self::execute("SELECT * FROM operations where tricount = :tricountId", array("tricountId"=>$tricountId));
        $data = $query->fetch();
        return $data;
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
                    $row["id"],
                    $row["title"],
                    $row["tricount"],
                    $row["amount"],
                    $row["operation_date"],
                    $row["initiator"],
                    $row["created_at"]
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

}