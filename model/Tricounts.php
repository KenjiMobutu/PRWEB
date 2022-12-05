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
    public function get_id(){
      return $this->id;
    }
    public function get_title(){
      return $this->title;
    }
    public function get_description(){
      return $this->description;
    }
    public function get_created_at(){
      return $this->get_created_at;
    }

    public function get_creator_id(){
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


  }


?>
