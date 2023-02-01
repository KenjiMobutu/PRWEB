<?php
require_once "framework/Model.php";

    class Participations extends Model{
        public int $tricount;
        public int $user;

        public function __construct($tricount, $user){
            $this->tricount = $tricount;
            $this->user = $user;
        }

        public function get_tricount(){
            return $this->tricount;
        }

        public function get_user(){
            return $this->user;
        }


        public function is_in_operation($operationId){
            $query = self::execute("SELECT user FROM repartitions WHERE operation = :id ",
                                    array("id"=>$operationId));
            if($query->rowCount()==0){
                return false;
            }
            return $query;
        }


        public static function get_by_tricount($tricount){
            $query = self::execute("SELECT s.*, t.creator from subscriptions s, tricounts t
                                            where s.tricount = t.id
                                            and s.tricount =:tricount
                                            and t.id = :tricount",
            array("tricount"=>$tricount));
            $participant = [];
            $data = $query->fetchAll();
            if($query->rowCount() == 0)
                return null;
            foreach($data as $row)
                $participant[] = new Participations($row["tricount"], $row["user"]);
            return $participant;
        }

        public function getUserInfo(){
            $query = self::execute("SELECT u.full_name
                                    from `users` u, subscriptions s where u.id= s.user
                                    and s.user = :id",array("id"=>$this->user));
            $data = $query->fetch();
            if($query->rowCount() == 0)
                return null;
            return $data["full_name"];
        }

        public static function get_by_tricount_and_creator($tricount){
            $query = self::execute("SELECT DISTINCT u.full_name
                        from subscriptions s, tricounts t, users u, repartition_template_items rti
                        where s.tricount =:tricount
                        and s.user = u.id
                        and u.id = rti.user
                        or u.id = t.creator;",
                            array("tricount"=>$tricount));
            $data = $query->fetchAll();
            if($query->rowCount() == 0)
                return null;
            return $data;
        }
        public static function get_by_user($user){
            $query = self::execute("SELECT * from subscriptions where user =:user",
            array("user"=>$user));
        }
        public static function delete_by_user_id_and_tricount($id,$tricount): bool{
            $query = self::execute("DELETE
                from subscriptions
                where user=:user
                And tricount=:tricount",
                array("user"=>$id, "tricount"=>$tricount));
            if($query->rowCount()==0)
                return false;
            else
                return true;
        }


        public static function delete_by_user_id($id): bool{
            $query = self::execute("DELETE
                from subscriptions
                where user=:id",
                array("user"=>$id));
            if($query->rowCount()==0)
                return false;
            else
                return true;
        }
        public static function delete_by_tricount_id($id){
            $query = self::execute("DELETE
                FROM subscriptions
                where tricount =:id",
                array("id"=>$id));
            if($query->rowCount()==0)
                return false;
            else
                return true;
        }
        public function add(){
            self::execute("INSERT INTO `subscriptions`(`tricount`, `user`) VALUES (:tricount,:user)",
                    array("tricount"=>$this->tricount,"user"=>$this->user));
        }
        public function update(){
            if(self::get_by_tricount($this->tricount) != null){
                self::execute("UPDATE subscriptions
                SET
                tricount=:tricount,
                user=:user
                where tricount=:tricount",
                array("tricount"=>$this->tricount,
                "user"=>$this->user));
            }else{
                self::execute("INSERT INTO
                subscriptions (tricount,
                user)
                VALUES(:tricount,
                :user)",
                array("tricount"=>$this->tricount,
                      "user"=>$this->user));
            }
            return $this;
        }
        public static function by_tricount($tricount){
            $query = self::execute("SELECT s.*
                                  FROM subscriptions s, tricounts t
                                  where s.tricount = t.id
                                  And s.tricount = :tricount
                                  ", array("tricount"=>$tricount));
            $data = $query->fetchAll();
            $subscription  = [];
            foreach ($data as $row) {
              $subscription[] = new Participations($row["tricount"],$row["user"]);
            }
            return $subscription;
        }


    /**SELECT * 
        from repartition_template_items rti 
        join repartition_templates rt on rt.id = rti.repartition_template
        join tricounts t on rt.tricount = t.id
        join subscriptions s on t.id = s.tricount
        ORDER by rti.repartition_template*/

    public function is_in_Items($templateID){
        $query = self::execute("SELECT DISTINCT rti.* 
                from repartition_template_items rti, subscriptions o 
                where o.tricount =:tricount
                and rti.repartition_template = :repartition_template 
                and rti.user = :user",
                array("tricount"=>$this->tricount,
                        "user"=>$this->user,
                        "repartition_template"=>$templateID));
        if($query->rowCount()==0){
            return false;
        }
        return $query;

    }
    public function get_weight_by_user($repartition_template): int
    {
      $query = self::execute("SELECT *
                              FROM  `repartition_template_items`
                              where user=:user
                              and repartition_template=:repartition_template ",
                              array("user" => $this->user,"repartition_template"=>$repartition_template));
      $data = $query->fetch(); //un seul resultat max
      if ($query->rowCount() == 0) {
        return null;
      } else
        return ($data["weight"]);
    }

    }

?>
