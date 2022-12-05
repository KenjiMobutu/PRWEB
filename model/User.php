//mail
hashed_password
full_name
role (user, admin)
iban

id

<?php

    class User extends Model{
        public int $id;
        public int $hashed_password;
        public String $full_name;
        public String $role;
        public String $iban;

        public function __construct($id,$hashed_password,$full_name,$role,$iban){
            $this->id = $id;
            $this->hashed_password = $hashed_password;
            $this->full_name = $full_name;
            $this->role = $role;        
            $this->iban = $iban;        
        }

        public function isAdmin(){
            return $this->role=="admin";
        }

        public function delete ($id){
            Repartition_template_items::delete_by_user_id($id);
            Repartition::delete_by_user_id($id);
            Operation::delete_by_user_id($id);
            Participation::delete_by_user_id($id);
            Tricount::delete_by_user_id($id);
            $query=self::execute("DELETE from `user` where id=:id", array("id"=>$id));
            if($query->rowCount()==0)
                return false;
            else
                return $query;
        }
        
        public static function get_by_id($id){
            $query = self::execute("SELECT * FROM  `user` where id=:id", array("id"=>$id));
            $data = $query->fetch()//un seul resultat max
            if($query->rowCount() == 0){
                return false;
            } else{
                return new User($data["id"],$data["hashed_password"],$data["full_name"],$data["role"],$data["iban"]);
            }
        }

        public static function get_by_name($full_name){
            $query = self::execute("SELECT * FROM  `user` where full_name=:fullname", array("fullname"=>$full_name));
            $data = $query->fetch()//un seul resultat max
            if($query->rowCount() == 0){
                return false;
            } else{
                return new User($data["id"],$data["hashed_password"],$data["full_name"],$data["role"],$data["iban"]);
            }
        }

        public static function get_by_iban($iban){
            $query = self::execute("SELECT * FROM  `user` where iban=:iban", array("iban"=>$iban));
            $data = $query->fetch()//un seul resultat max
            if($query->rowCount() == 0){
                return false;
            } else{
                return new User($data["id"],$data["hashed_password"],$data["full_name"],$data["role"],$data["iban"]);
            }
        }

       


    }
?>