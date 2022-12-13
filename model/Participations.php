<?php
require_once "framework/Model.php";

    class Participations extends Model{
        public int $tricount;
        public int $user;

        public function __construct($tricount, $user){
            $this->tricount = $tricount;
            $this->user = $user;
        }


        public static function get_by_tricount($tricount){
            $query = self::execute("SELECT * from subscriptions where tricount =:tricount", 
            array("tricount"=>$tricount));

        }
        
        public static function get_by_user($user){
            $query = self::execute("SELECT * from subscriptions where user =:user", 
            array("tricount"=>$user));
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
        public static function delete_by_tricount_id($id): bool{
            $query = self::execute("DELETE
                FROM subscriptions 
                where tricount =:id",
                array("tricount"=>$id));
            if($query->rowCount()==0)
                return false;
            else
                return true;
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




    }
 
?>