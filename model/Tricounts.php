//title,description,created_at,creator,id
<?php
  class Tricounts extends Model{

    public $title;//(varchar 256)
    public $description;//(varchar 1024)
    public $created_at;//(datetime)
    public $creator;//(int)
    public $id;//(int)

    public function __construct($id,$title, $description,$created_at, $creator){
      $this->id = $id;
      $this->title = $title;
      $this->description = $description;
      $this->created_at = $created_at;
      $this->creator = $creator;
    }
    //retourne l'id du tricount
    public function get_id():int{
      return $this->id;
    }
    //retourne le titre du tricount
    public function get_title():String{
      return $this->title;
    }
    //retourne la description
    public function get_description():String{
      return $this->description;
    }
    //retourne la date de création
    public function get_created_at():datetime{
      return $this->get_created_at;
    }

    //retourne l'id du créateur
    public function get_creator_id():int{
      return $this->get_creator_id;
    }

    //retourne le tricount par son id
    public static get_by_id($id){
      $query = self::execute("SELECT * FROM tricounts WHERE ID = :id", array("id"=>$id));
        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new User($data["ID"],$data["title "],$data["description"],$data["created_at"],$data["creator"]);
        }
    }

    //retourne le tricount par son créateur
    public static get_by_creator($creator){
      $query = self::execute("SELECT * FROM tricounts WHERE creator = :creator", array("creator"=>$creator));
        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new User($data["ID"],$data["title "],$data["description"],$data["created_at"],$data["creator"]);
        }
    }

    public function update() {
      if(!is_null($this->id)){
          self::execute("UPDATE tricounts SET
          title=:title,
          description=:description,
          created_at=:created_at,
          creator=:creator
          WHERE id=:id ",
                      array("id"=>$this->id,
                      "title"=>$this->title,
                      "description"=>$this->description,
                      "created_at"=>$this->created_at,
                      "creator"=>$this->creator,
                      ));
      }else{
          self::execute("INSERT INTO
          tricounts (title,description,
          created_at,
          creator)
          VALUES(:title,
          :description,
          :created_at,
          :creator)",
          array("title"=>$this->title,
                  "description"=>$this->description,
                  "created_at"=>$this->created_at,
                  "creator"=>$this->creator));
      }
      return $this;
    }

    public function delete ($id){
      Repartition_template::delete_by_tricount($tricount);
      Operation::delete_by_tricount($tricount);
      Participation::delete_by_tricount($tricount);
      $query=self::execute("DELETE from `tricounts` where id=:id", array("id"=>$id));
      if($query->rowCount()==0)
          return false;
      else
          return $query;
    }


  }


?>
