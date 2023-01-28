<?php
  require_once "framework/Model.php";
  require_once 'model/Repartition_templates.php';


  class Repartition_template_items extends Model
  {
    public  $weight;
    public  $user;
    public  $repartition_template;


    public function __construct( $weight,  $user, $repartition_template)
    {
      $this->weight = $weight;
      $this->user = $user;
      $this->repartition_template = $repartition_template;
    }
    public function get_weight(): int
    {
      return $this->weight;
    }

    public function get_rt(){
      return $this->repartition_templates;
    }

    public static function addNewItems($user,  $repartition_template , $weight){
      $query = self::execute("INSERT INTO
            repartition_template_items (`user`, `repartition_template`, `weight`)
            VALUES(:user,
            :repartition_template,
            :weight)",
          array(
            "weight"=>$weight,
            "user"=>$user,
            "repartition_template" => $repartition_template
          )
        );
      return $query;
    }

    public static function insertVladRTi(){
      $query = self::execute(
        "INSERT INTO `repartition_templates_items` (`weight`, `user`, `repartition_templates`)
                VALUES (:title,
                        :tricount,
                        :repartition_templates)",
        array(
            "weight" =>weight,
            "user" =>user,
            "repartition_templates" =>repartition_templates
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
      $query = self::execute("SELECT *
                              FROM  `repartition_template_items`
                              where user=:user
                              /*and repartition_template=:repartition_template*/ ",
                              array("user" => $user/*,"repartition_template"=>$repartition_template*/));
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
        return $data[0]; //ou return $data["SUM(weight)"]
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
