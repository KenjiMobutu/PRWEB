<?php
  require_once "framework/Model.php";
  require_once 'model/Repartition_templates.php';


  class Repartition_template_items extends Model
  {
    public ?int $weight;
    public ?int $user;
    public ?int $repartition_template;


    public function __construct(?int $weight, int $user, int $repartition_template)
    {
      $this->weight = $weight;
      $this->user = $user;
      $this->repartition_template = $repartition_template;
    }
    public function get_weight(): int
    {
      return $this->weight;
    }
    public function get_user() : int
    {
      return $this->user;
    }
    public function get_repartition_template() : int{
      return $this->repartition_template;
    }
    

    public static function get_weight_by_user($user): int
    {
      $query = self::execute("SELECT * FROM  `repartition_template_items` where user=:user", array("user" => $user));
      $data = $query->fetch(); //un seul resultat max
      if ($query->rowCount() == 0) {
        return null;
      } else
        return ($data["weight"]);
    }

    public function get_Sum_Weight(){
      $query = self::execute("SELECT SUM(weight)
                                  FROM `repartition_template_items`
                                  WHERE repartition_template =:repartition_template;", 
                              array("repartition_template"=>$this->repartition_template));
      $data = $query->fetch();
      if ($query->rowCount() == 0) {
        return null;
      } else
        return $data["SUM(weight)"];
    }

    public static function get_participations($id){
      $query = self::execute("SELECT u.full_name, sum(rti.weight)
                            from repartition_template_items rti, user u
                            where rti.user =u.id
                            and rti.repartition_template=:id", array("id"=>$id));
      $data = $query->fetch();
      if($query->rowCount()==0){
        return null;
      }
      return $data;
    }

    public function get_by_user_2(){
      
    }

    public static function get_by_user($user){ //à refaire
      $query = self::execute("SELECT * FROM  repartition_template_items rti, repartition_templates rt 
                              where rti.repartition_template = rt.id 
                              and rti.user=:user",
                              array("user" => $user));
      $data = $query->fetchAll();
      if ($query->rowCount() == 0) {
        return null;
      } else
        return $data;
    }

    public function get_user_info(){  // on récupère les noms des utilisateurs lié a un template_items
      $query = self::execute("SELECT * 
                              from users 
                              where id=:id",
                              array("id"=>$this->user));
      $data = $query->fetch();
      if ($query->rowCount() == 0) {
        return null;
      } else
        return $data["full_name"];
    }

  }


?>