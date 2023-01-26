<?php
require_once "framework/Model.php";

class Repartition_templates extends Model
{
  public $id;
  public $title; //(varchar 256)
  public $tricount;

  public function __construct(?int $id, string $title, int $tricount)
  {
    $this->id = $id;
    $this->title = $title;
    $this->tricount = $tricount;
  }

  public function get_id(): int
  {
    return $this->id;
  }

  public function get_title(): string|null
  {
    return $this->title;
  }
  public function get_tricount(): int|null
  {
    return $this->tricount;
  }

  public static function get_by_id($id): Repartition_templates | null
  {
    $query = self::execute("SELECT * FROM  `repartition_templates` where id=:id", array("id" => $id));
    $data = $query->fetch(); //un seul resultat max
    if ($query->rowCount() == 0) {
      return null;
    } else {
      return new repartition_templates($data["id"], $data["title"], $data["tricount"]);
    }
  }
    public static function get_by_tricount($tricount) 
    {
      $query = self::execute("SELECT * FROM  `repartition_templates` where tricount=:tricount", array("tricount"=>$tricount));
            $data = $query->fetchAll();                 //c'était un fetch avant
            $templates = [];
            if ($query->rowCount() == 0){
                return null;
            } else{
              foreach($data as $row){
                  $templates[] = new Repartition_templates($row["id"],$row["title"],$row["tricount"]);
              }
                // return new repartition_templates($data["id"],$data["title"],$data["tricount"]);
                return $templates;
            }
    }

    public function get_items(){
      $query =self::execute("select rti.* from repartition_template_items rti, repartition_templates rt 
                            where rt.id = rti.repartition_template 
                            and rt.id=:id", array("id"=>$this->id));
      $data = $query->fetchAll();
      $items=[];
      if ($query->rowCount() == 0){
        return null;
      } else{
        foreach($data as $row){
          $items[] = new repartition_template_items($row["weight"],$row["user"],$row["repartition_template"]);
          // dans items je dois renvoyer un user de la même façon
        }
      }
      return $items;
    }
    
  

    public function delete_by_tricount($tricount){
      // Repartition_template_items::delete_by_user_id($id);
      // Repartition::delete_by_user_id($id);
      // Operation::delete_by_user_id($id);
      // Participation::delete_by_user_id($id);
      // Tricount::delete_by_user_id($id);
      Repartition_template_items::delete_by_repartition_template($this->id);
      $query=self::execute("DELETE from `repartition_templates` where tricount=:tricount", array("tricount"=>$tricount));
      if($query->rowCount()==0)
          return false;
      else
          return $query;
    }

    public function delete_by_id(){
      //doit supprimer le tricount depuis repartition_template_items
      Repartition_template_items::delete_by_repartition_template($this->id);
      $query=self::execute("DELETE from `repartition_templates` where id=:id", array("id"=>$this->id));
      if($query->rowCount()==0)
        return false;
      return $query;
    }

    public static function newTemplate($titre, $tricount){
      if($titre === null || $tricount === null)
        return null;
      else{
        $query = self::execute("INSERT INTO
                                repartition_templates
                                (title, tricount)
                                VALUES(:titre,
                                      :tricount)",
                                array(
                                  "titre"=>$titre,
                                  "tricount"=>$tricount
                                )
                              );
        $data = $query->fetch();

      }
        if($query->rowCount() ==0)
              return false;
        return new Repartition_template_items( self::lastInsertId(), $data["title"], $data["tricount"]);
    }


  public function update()
  {
    if (!is_null($this->id)) {
      self::execute("UPDATE `repartition_templates` SET
          title=:title,
          tricount=:tricount
          WHERE id=:id ",
        array(
          "id" => $this->id,
          "title" => $this->title,
          "tricount" => $this->tricount
        )
      );
    } else {
      self::execute("INSERT INTO
          `repartition_templates` (title,tricount)
          VALUES(:title,
          :tricount)",
        array(
          "title" => $this->title,
          "tricountn" => $this->tricount
        )
      );
    }
    return $this;
  }
}



?>