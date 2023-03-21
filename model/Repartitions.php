<?php
require_once "model/User.php";
require_once 'framework/Model.php';
require_once 'model/Operation.php';

class Repartitions extends Model
{
    private ?int $weight;

    public ?int $operation;

    public ?int $user;

    public function __construct(int $weight=NULL, int $operation, int $user)
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

    public static function get_user_and_weight_by_operation_id($operation){
        $query = self::execute("SELECT user, weight FROM repartitions WHERE operation=:id", array("id"=>$operation));
        $data = $query->fetchAll();
        return $data;
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
