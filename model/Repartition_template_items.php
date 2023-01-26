<?php
  require_once "framework/Model.php";
  require_once 'model/Repartition_templates.php';


  class Repartition_template_items extends Model
  {
    public ?int $weight;
    public ?int $user;
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

    public function get_rt(){
      return $this->repartition_templates;
    }

    public function insertVladRTi(){
      $query = self::execute(
        "INSERT INTO `repartition_templates_items` (`weight`, `user`, `repartition_templates`) 
                VALUES (:title,
                        :tricount,
                        :repartition_templates)",
        array(
            "weight" => $this->weight,
            "user" => $this->user,
            "repartition_templates" => $this->repartition_templates
        )
    );
    $this->setRepartitionTemplateItemsId();
    return $query->fetch();
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setRepartitionTemplatesItemsId()
    {
        $query = self::execute("SELECT id FROM repartition_templates WHERE id = :id", array("id" => Model::lastInsertId()));
        $data = $query->fetchAll();
        foreach ($data as $row) {
            $id = $row['id'];
        }
        $this->setId($id);
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

    public static function get_by_user($user){
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
