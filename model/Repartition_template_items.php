<?php
require_once "framework/Model.php";
require_once 'model/Repartition_templates.php';


class Repartition_template_items extends Model
{
  public $weight;
  public $user;
  public $repartition_template;


  public function __construct($weight, $user, $repartition_template)
  {
    $this->weight = $weight;
    $this->user = $user;
    $this->repartition_template = $repartition_template;
  }
  public function get_weight(): int
  {
    return $this->weight;
  }
  public function get_user()
  {
    return $this->user;
  }
  public function get_rt()
  {
    return $this->repartition_template;
  }

  public function setId($id)
  {
    return $this->user;
  }


  // public static function insertVladRTi(){
  //   $query = self::execute(
  //     "INSERT INTO `repartition_templates_items` (`weight`, `user`, `repartition_templates`)
  //             VALUES (:title,
  //                     :tricount,
  //                     :repartition_templates)",
  //     // array(
  //     //     "weight" =>weight,
  //     //     "user" =>user,
  //     //     "repartition_templates" =>repartition_templates
  //     )
  // );
  // // $this->setRepartitionTemplatesItemsId();
  // return $query->fetch();
  // }
  public static function get_weight_by_user($user, $repartition_template): int
  {
    $query = self::execute("SELECT *
                              FROM  `repartition_template_items`
                              where user=:user
                              and repartition_template=:repartition_template ",
      array("user" => $user, "repartition_template" => $repartition_template)
    );
    $data = $query->fetch(); //un seul resultat max
    if ($query->rowCount() == 0) {
      return 0;
    } else
      return ($data["weight"]);
  }

  public static function get_all_from_template_by_id($templateId)
  {
      $query = self::execute("SELECT * FROM `repartition_template_items` WHERE `repartition_template`=:templateId", array("templateId" => $templateId));
      $repartition_template_items = array();
      while ($data = $query->fetch()) {
          $repartition_template_item = new Repartition_template_items($data["user"], $data["repartition_template"], $data["weight"]);
          $repartition_template_items[] = $repartition_template_item;
      }
      return $repartition_template_items;
  }
  

  public function get_template(){
    return Repartition_templates::get_by_id($this->repartition_template);
  }


  public function get_Sum_Weight()
  {
    $query = self::execute("SELECT SUM(weight)
                                  FROM `repartition_template_items`
                                  WHERE repartition_template =:repartition_template;",
      array("repartition_template" => $this->repartition_template)
    );
    $data = $query->fetch();
    if ($query->rowCount() == 0) {
      return null;
    } else
      return $data[0]; //ou return $data["SUM(weight)"]
  }

  public static function get_participations($id)
  {
    $query = self::execute("SELECT u.full_name, sum(rti.weight)
                            from repartition_template_items rti, user u
                            where rti.user =u.id
                            and rti.repartition_template=:id", array("id" => $id));
    $data = $query->fetch();
    if ($query->rowCount() == 0) {
      return null;
    }
    return $data;
  }

  public static function get_by_user_and_tricount($user, $tricountId)
  { 
    $query = self::execute("SELECT * FROM  repartition_template_items rti, repartition_templates rt
                              where rti.repartition_template = rt.id
                              and rti.user=:user and rt.tricount=:tricount",
      array("user" => $user, "tricount" => $tricountId)
    );
    $data = $query->fetchAll();
    
      return $data;
  }
  public static function by_user($user)
  {
    $query = self::execute("SELECT *
                              FROM  repartition_template_items rti, repartition_templates rt
                              where rti.repartition_template = rt.id
                              and rti.user=:user",
      array("user" => $user)
    );
    $data = $query->fetchAll();
    $items = [];
    if ($query->rowCount() == 0)
      return null;
    foreach ($data as $row) {
      $items[] = new Repartition_template_items($row["weight"], $row["user"], $row["repartition_template"]);
    }
    return $items;
  }
  public static function get_by_tricount($tricount)
  {
    $query = self::execute("SELECT *
                              FROM repartition_templates rt
                              JOIN repartition_template_items rti
                              ON rt.id = rti.repartition_template
                              WHERE rt.tricount = :tricount",
      array("tricount" => $tricount)
    );
    $data = $query->fetchAll();
    $items = [];
    if ($query->rowCount() == 0)
      return null;
    foreach ($data as $row) {
      $items[] = new Repartition_template_items($row["weight"], $row["user"], $row["repartition_template"]);
    }
    return $items;
  }

  public static function get_user_by_repartition($repartition)
  {
    $query = self::execute("SELECT rti.*
                            FROM repartition_template_items rti
                            where repartition_template = :repartition", array("repartition" => $repartition));
    $data = $query->fetchAll();
    $items = [];
    if ($query->rowCount() == 0)
      return null;
    foreach ($data as $row) {
      $items[] = new Repartition_template_items($row["weight"], $row["user"], $row["repartition_template"]);
    }
    return $items;
  }
  public static function newTemplateItems($weight, int $templateId, $user)
  {
    if ($weight === null || $templateId === null || $user === null) {
      return null;
    } else {
      $query = self::execute("INSERT INTO
                                  repartition_template_items
                                  (`weight`,
                                  user,
                                  repartition_template)
                                  VALUES (:`weight`,
                                    :user,
                                    :template) ",
        array(
          "weight" => $weight,
          "template" => $templateId,
          "user" => $user
        )
      );
      return $query;
    }
  }

  public function get_user_info()
  { // on récupère les noms des utilisateurs lié a un template_items
    $query = self::execute("SELECT *
                              from users u, repartition_template_items rti
                              where rti.user = u.id
                              and  rti.repartition_template =:repartition_template
                              and u.id=:id",
      array("id" => $this->user, "repartition_template" => $this->repartition_template)
    );
    $data = $query->fetch();
    if ($query->rowCount() == 0) {
      return null;
    } else
      return $data["full_name"];
  }


  public static function delete_by_repartition_template($repartition_template)
  {
    $query = self::execute("DELETE
                              FROM repartition_template_items
                              where repartition_template=:repartition_template",
      array("repartition_template" => $repartition_template)
    );
    if ($query->rowCount() == 0)
      return false;
    return $query;
  }


  public static function addNewItems($user, $repartition_template, $weight)
  {
    $query = self::execute("INSERT INTO
            repartition_template_items (`user`, `repartition_template`, `weight`)
            VALUES(:user,
            :repartition_template,
            :weight)",
      array(
        "weight" => $weight,
        "user" => $user,
        "repartition_template" => $repartition_template
      )
    );
    return $query;
  }


  public function update()
  {
    if (!is_null($this->repartition_template)) {
      self::execute("UPDATE `repartition_template_items` SET
            weight=:weight,
            user =:user

            WHERE repartition_template=:repartition_template ",
        array(
          "weight" => $this->weight,
          "user" => $this->user,
          "repartition_template" => $this->repartition_template
        )
      );
    } else {
      self::execute("INSERT INTO
            `repartition_template_items` (weight,user, repartition_template)
            VALUES(:`weight`,
            :`user`,
            :repartition_template)",
        array(
          "weight" => $this->weight,
          "user" => $this->user,
          "repartition_template" => $this->repartition_template
        )
      );
    }
    return $this;
  }
}

?>