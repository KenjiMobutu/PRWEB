<?php
require_once "model/User.php";
require_once 'framework/Model.php';
require_once 'model/Operation.php';

class Repatitions extends Model
{
    public int $weight;

    public int $operation;

    public int $user;

    public function __construct(int $weight, int $operation, int $user)
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

    public function get_weight_par_user($operation)
    {
        $query = self::execute("SELECT weight, user FROM repartitions WHERE operation=:operation", array("operation" => $operation));
        $data = $query->fetch();
        return new Repatitions($data["weight"], $data["operation"], $data["user"]);
    }


    public static function get_full_weight($operation)
    {
        //poids / total poids
        $query = self::execute("SELECT SUM(weight) FROM repartitions WHERE operation=:operation", array("operation" => $operation));
        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return $data;
        }
    }

    public function create()
    {
        $query = self::execute(
            "INSERT INTO `repartitions` (`operation`, `user`, `weight`) 
            VALUES (':operation', ':user', ':weight');",
            array(
                "operation" => $this->operation,
                "user" => $this->user,
                "weight" => $this->weight
            )
        );

        return $query->fetch();
    }

}

?>