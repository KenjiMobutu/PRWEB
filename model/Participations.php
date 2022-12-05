<?php
    class Participations extends Model{
        public int $tricount;
        public int $user;

        public function __construct($tricount, $user){
            $this->tricount = $tricount;
            $this->user = $user;
        }


        public static get_user_by_tricount($tricount){
            $query = self::execute("SELECT * from subscriptions where tricount =:tricount", 
            array("tricount"=>$tricount));
        }
        public static get_user_by_user($user){
            $query = self::execute("SELECT * from subscriptions where user =:user", 
            array("tricount"=>$user));
        }
        


        public static delete_by_user_id($id){

        }


    }
 
?>