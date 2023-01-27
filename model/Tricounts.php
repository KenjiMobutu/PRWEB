<!-- //title,description,created_at,creator,id -->
<?php

  class Tricounts extends Model
{

  private $title; //(varchar 256)
  private $description; //(varchar 1024)
  private $created_at; //(datetime)
  private $creator; //(int)
  private $id; //(int)


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
    public function get_description(){
      return $this->description;
    }
    //retourne la date de création
    public function get_created_at():datetime{
      return $this->created_at;
    }

  //retourne l'id du créateur
  public function get_creator_id(): int
  {
    return $this->creator;
  }

    //retourne le tricount par son id
    public static function get_by_id($id){
      $query = self::execute("SELECT * FROM tricounts WHERE id = :id", array("id"=>$id));
        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new Tricounts($data["id"],$data["title"],$data["description"],$data["created_at"],$data["creator"]);
        }
    }

  //retourne le tricount par son créateur
  public static function get_by_creator($creator)
  {
    $query = self::execute("SELECT * FROM tricounts WHERE creator = :creator", array("creator" => $creator));
    $data = $query->fetch();
    if ($query->rowCount() == 0) {
      return false;
    } else {
      return new Tricounts($data["id"], $data["title"], $data["description"], $data["created_at"], $data["creator"]);
    }
  }

  public function update()
  {
    if (!is_null($this->id)) {
      self::execute("UPDATE tricounts SET
          title=:title,
          description=:description,
          created_at=:created_at,
          creator=:creator
          WHERE id=:id ",
        array(
          "id" => $this->id,
          "title" => $this->title,
          "description" => $this->description,
          "created_at" => $this->created_at,
          "creator" => $this->creator,
        )
      );
    } else {
      self::execute("INSERT INTO
          tricounts (title,description,
          created_at,
          creator)
          VALUES(:title,
          :description,
          :created_at,
          :creator)",
        array(
          "title" => $this->title,
          "description" => $this->description,
          "created_at" => $this->created_at,
          "creator" => $this->creator
        )
      );
    }
    return $this;
  }

    public function delete ($id)
    {
      // Repartition_template::delete_by_tricount($id);
      // Operation::delete_by_tricount($id);
      // Participation::delete_by_tricount($id);
      $query=self::execute("DELETE from `tricounts` where id=:id", array("id"=>$id));
      if($query->rowCount()==0)
          return false;
      else
          return $query;
    }
    public static function by_user($user){
      $query = self::execute("SELECT t.* FROM `tricounts` t JOIN  subscriptions s ON t.id = s.tricount where t.creator=:user", array("user"=>$user));
        $data = $query->fetchAll();
        $tricount  = [];
        foreach ($data as $row) {
          $tricount[] = new Tricounts($row["id"],$row["title"],$row["description"],$row["created_at"],$row["creator"]);
        }
        return $tricount;
    }
    public static function list($creator){
      $query = self::execute("SELECT DISTINCT tricounts.*
                              FROM tricounts
                              LEFT JOIN subscriptions
                              ON tricounts.id = subscriptions.tricount
                              WHERE tricounts.creator =:creator
                              OR subscriptions.user =:creator",
                              array("creator"=>$creator));
      $data = $query->fetchAll();
      $tricount  = [];
      foreach ($data as $row) {
        $tricount[] = new Tricounts($row["id"],$row["title"],$row["description"],$row["created_at"],$row["creator"]);
      }
      return $tricount;
    }
    public static function one_of_list(){
      $query = self::execute("SELECT * FROM `tricounts`", array());
      if ($query->rowCount() == 0) {
        return false;
      } else {
        $data = $query->fetch();
        return new Tricounts($data["id"],$data["title"],$data["description"],$data["created_at"],$data["creator"]);
      }
    }
    public static function number_of_friends($tricountId){
      $query = self::execute("SELECT count(*)
                              FROM subscriptions s, tricounts t
                              where s.tricount = t.id
                              and t.id=:tricountId",
                              array("tricountId"=>$tricountId));
        $data = $query->fetch();
        return $data[0];
    }


}



?>
