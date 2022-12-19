<?php
  require_once "framework/Model.php";
  require_once 'model/Repartition_templates.php';


  class Repartition_template_items extends Model
  {
    public ?int $weight;
    public ?User $user;
    public ?int $repartition_templates;


    public function __construct(?int $weight, string $user, int $repartition_templates)
    {
      $this->weight = $weight;
      $this->user = $user;
      $this->repartition_templates = $repartition_templates;
    }
    public function get_weight(): int
    {
      return $this->weight;
    }
    public function get_user() 
    {
      return $this->user;
    }
    

    public static function get_weight_by_user($user): int
    {
      $query = self::execute("SELECT weight FROM  `repartition_templates_items` where user=:user", array("user" => $user));
      $data = $query->fetch(); //un seul resultat max
      if ($query->rowCount() == 0) {
        return null;
      } else
        return ($data["weight"]);
    }

    public static function get_participations($id){
      $query = self::execute("SELECT u.full_name, sum(rti.weight)
                            from repartition_template_items rti, user u
                            where rti.user =u.id
                            and rti.repartition_templates=:id", array("id"=>$id));
      $data = $query->fetch();
      if($query->rowCount()==0){
        return null;
      }
      return $data;
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
    
  }


?>