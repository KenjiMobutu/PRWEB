<?php
require_once "model/User.php";
require_once 'framework/Model.php';
require_once 'model/Operation.php';

class Repartitions extends Model
{
    public ?int $weight;

    public ?int $operation;

    public ?int $user;

    public function __construct(int $weight = NULL, int $operation, int $user)
    {
        $this->weight = $weight;
        $this->operation = $operation;
        $this->user = $user;
    }

    public function getWeight()
    {
        return $this->weight;
    }

    public function getOperation()
    {
        return $this->operation;
    }

    public function getUser()
    {
        return $this->user;
    }

    public static function get_by_user($user)
    {
        $query = self::execute("SELECT operation FROM repartitions WHERE user=:user", array("user" => $user));
        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return $data;
        }
    }

    // public static function get_user_and_weight_by_operation_id($operation){
    //     $query = self::execute("SELECT user, weight FROM repartitions WHERE operation=:id", array("id"=>$operation));
    //     $data = $query->fetchAll();
    //     return $data;
    // }


    public static function get_by_operation($operationId)
    {
        $query = self::execute("SELECT weight, operation, user FROM repartitions WHERE operation=:id
        ", array("id" => $operationId));
        $repartitions = array();
        while ($data = $query->fetch()) {
            if ($data !== NULL) {
                $repartition = new Repartitions($data["weight"], $data["operation"], $data["user"]);
                $repartitions[] = $repartition;
            }
        }

        return $repartitions;
    }

    public static function update($operationId, $checkedUsers, $weights)
    {
        try {
            if (is_string($checkedUsers)) {
                $checkedUsers = array($checkedUsers);
            }
            for ($i = 0; $i < count($checkedUsers); $i++) {
                $userId = $checkedUsers[$i];
                $weight = $weights[$i];

                $query = self::execute(
                    "UPDATE repartitions SET weight = :weight WHERE operation_id = :operation_id AND user_id = :user_id",
                    array(
                        "operation_id" => $operationId,
                        "user_id" => $userId,
                        "weight" => $weight
                    )
                );
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }



// public static function create()
// {
//     $query = self::execute(
//         "INSERT INTO `repartitions` (`operation`, `user`, `weight`)
//         VALUES (':operation', ':user', ':weight');",
//         array(
//             "operation" => $this->operation,
//             "user" => $this->user,
//             "weight" => $this->weight
//         )
//     );

//     return $query->fetch();
// }

}

?>