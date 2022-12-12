<?php
require_once "framework/Model.php";

  class Repartition_templates extends Model{
    public ?int $id;
    public String $title;//(varchar 256)
    public int $tricount;

    public function __construct(?int $id,String $title,int $tricount){
      $this->id = $id;
      $this->title = $title;
      $this->tricount= $tricount;
    }

    public function get_id(): int {
      return $this->id;
    }
    public function get_title(): string | null{
      return $this->title;
    }
    public function get_by_id($id): int {
      $query = self::execute("SELECT * FROM  `repartition_templates` where id=:id", array("id"=>$id));
            $data = $query->fetch();//un seul resultat max
            if ($query->rowCount() == 0){
                return null;
            } else{
                return new repartition_templates($data["id"],$data["title"],$data["tricount"]);
            }
    }
    public function get_by_tricount($tricount): int {
      $query = self::execute("SELECT * FROM  `repartition_templates` where tricount=:tricount", array("tricount"=>$tricount));
            $data = $query->fetch();//un seul resultat max
            if ($query->rowCount() == 0){
                return null;
            } else{
                return new repartition_templates($data["id"],$data["title"],$data["tricount"]);
            }
    }

    public function delete_by_tricount($tricount) ($id){
      // Repartition_template_items::delete_by_user_id($id);
      // Repartition::delete_by_user_id($id);
      // Operation::delete_by_user_id($id);
      // Participation::delete_by_user_id($id);
      // Tricount::delete_by_user_id($id);
      $query=self::execute("DELETE from `repartition_templates` where tricount=:tricount", array("tricount"=>$tricount));
      if($query->rowCount()==0)
          return false;
      else
          return $query;
    }

    public function update() {
      if(!is_null($this->id)){
          self::execute("UPDATE `repartition_templates` SET
          title=:title,
          tricount=:tricount
          WHERE id=:id ",
                      array("id"=>$this->id,
                      "title"=>$this->title,
                      "tricount"=>$this->tricount
                      ));
      }else{
          self::execute("INSERT INTO
          `repartition_templates` (title,tricount)
          VALUES(:title,
          :tricount)",
          array("title"=>$this->title,
                  "tricountn"=>$this->tricount));
      }
      return $this;
    }



  }



?>
