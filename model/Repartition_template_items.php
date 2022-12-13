<?php
require_once "framework/Model.php";

  class Repartition_template_items extends Model{
    public ?int $weight;
    public ?int $user;
    public ?int $repartition_templates;


  public function __construct(?int $weight,String $user,int $repartition_templates){
    $this->weight = $weight;
    $this->user = $user;
    $this->repartition_templates= $repartition_templates;
  }
  public function get_weight(): int {
    return $this->weight;
  }

  public function get_weight_by_user($user): int {
    $query = self::execute("SELECT weight FROM  `Repartition_template_items` where user=:user", array("user"=>$user));
          $data = $query->fetch();//un seul resultat max
          if ($query->rowCount() == 0){
              return null;
          } else
            return ($data["weight"]);
  }
}


?>
