<?php

    class User extends Model{
        public int $id;
        public String $mail;
        public int $hashed_password;
        public String $full_name;
        public String $role;
        public String $iban;

        public function __construct($id,$mail,$hashed_password,$full_name,$role,$iban){
            $this->id = $id;
            $this->mail = $mail;
            $this->hashed_password = $hashed_password;
            $this->full_name = $full_name;
            $this->role = $role;        
            $this->iban = $iban;        
        }

        //retourne l'id de l'utilisateur
        public function get_id(): int{
            return $this->id;
        }

        public function isAdmin(): String{
            return $this->role=="admin";
        }


        public function delete ($id): void{
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
        
        public static function get_by_id($id){//récup l'user par son id
            $query = self::execute("SELECT * FROM  `user` where id=:id", array("id"=>$id));
            $data = $query->fetch()//un seul resultat max
            if($query->rowCount() == 0){
                return false;
            } else{
                return new User($data["id"],$data["mail"],$data["hashed_password"],$data["full_name"],$data["role"],$data["iban"]);
            }
        }
        public static function get_by_mail($mail){//récup l'user par son id
            $query = self::execute("SELECT * FROM  `user` where mail=:mail", array("mail"=>$mail));
            $data = $query->fetch()//un seul resultat max
            if($query->rowCount() == 0){
                return false;
            } else{
                return new User($data["id"],$data["mail"],$data["hashed_password"],$data["full_name"],$data["role"],$data["iban"]);
            }
        }

        public static function get_by_name($full_name){ //récup l'user par son full_name
            $query = self::execute("SELECT * FROM  `user` where full_name=:fullname", array("fullname"=>$full_name));
            $data = $query->fetch()//un seul resultat max
            if($query->rowCount() == 0){
                return false;
            } else{
                return new User($data["id"],$data["mail"],$data["hashed_password"],$data["full_name"],$data["role"],$data["iban"]);
            }
        }

        public static function get_by_iban($iban){//récup l'user par son iban
            $query = self::execute("SELECT * FROM  `user` where iban=:iban", array("iban"=>$iban));
            $data = $query->fetch()//un seul resultat max
            if($query->rowCount() == 0){
                return false;
            } else{
                return new User($data["id"],$data["mail"],$data["hashed_password"],$data["full_name"],$data["role"],$data["iban"]);
            }
        }

        public function update() {
            if(!is_null($this->id)){
                self::execute("UPDATE user SET 
                mail=:mail,
                hashed_password=:password,
                full_name=:full_name,
                Role=:role 
                WHERE id=:id ",
                            array("id"=>$this->id,
                            "mail"=>$this->mail,
                            "hashed_password"=>$this->password,
                             "full_name"=>$this->full_name,
                            "Role"=>$this->role, 
                            "iban"=>$this->iban));
            }else{
                self::execute("INSERT INTO
                 user(mail,hashed_password, 
                 full_name, 
                 role,
                 iban)
                VALUES(:mail,
                :hashed_password,
                :full_name,
                :role,
                :iban,)",
                array("mail"=>$this->mail,
                        "hashed_password"=>$this->hashed_password,
                        "full_name"=>$this->full_name,
                        "role"=>$this->role, 
                        "iban"=>$this->iban));
            }
            return $this;
        }
       


    }
?>