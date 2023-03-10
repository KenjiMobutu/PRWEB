<?php
require_once "framework/Model.php";
//seul l'iban peut être null

class User extends Model
{
    private ?int $id;
    private string $mail;
    private string $hashed_password;
    private string $full_name;
    private string $role;
    private ?string $iban;

    protected const ROLE_ADMIN = 'admin';
    protected const ROLE_USER = 'user';

    protected const ROLES = [
        self::ROLE_ADMIN,
        self::ROLE_USER
    ];

    public function __construct(?int $id, string $mail, string $hashed_password, string $full_name, ?string $role, ?string $iban)
    {
        $this->id = $id;
        $this->mail = $mail;
        $this->hashed_password = $hashed_password;
        $this->full_name = $full_name;
        $this->role = self::ROLE_USER;
        $this->iban = $iban;
    }

    public function get_tricounts() : array{
        return Tricounts::get_tricount_by_user_id($this->getUserId());
    }

    public static function getUsers()
    {
        $result = [];
        $query = self::execute("SELECT * FROM  `users`", array());
        $data = $query->fetchAll();

        foreach ($data as $row) {
            $result[] = new User($row["id"], $row["mail"], $row["hashed_password"], $row["full_name"], $row["role"], $row["iban"]);
        }
        return $result;

    }

    //retourne l'id de l'utilisateur
    public function getUserId()
    {
        return $this->id;
    }

    public function getFullName(): string
    {
        return $this->full_name;
    }

    public function getUserIban(): string|null
    {
        return $this->iban;
    }

    public function setUserIban(string $iban): void
    {
        $this->iban = $iban;
    }

    public function getPassword(): string
    {
        return $this->hashed_password;
    }

    public function setPassword(string $hashed_password): void
    {
        $this->hashed_password = $hashed_password;
    }


    // public function delete($id)
    // {
    //     if(Repartition_template_items::delete_by_user_id($id)){
    //         if(Repartition::delete_by_user_id($id)){
    //             if(Operation::delete_by_user_id($id)){
    //                 if(Participation::delete_by_user_id($id)){
    //                     if(Tricount::delete_by_user_id($id)){
    //                         $query = self::execute("DELETE from `user` where id=:id", array("id" => $id));
    //                         if ($query->rowCount() == 0)
    //                             return false;
    //                         else
    //                             return $query;
    //                     }else{
    //                         echo "problème avec la fonction delete_by_user_id du modele tricount";
    //                     }
    //                 }else
    //                 echo "problème avec la fonction delete_by_user_id du modele Participation";
    //             }else
    //             echo "problème avec la fonction delete_by_user_id du modele operation";
    //         }
    //         echo "problème avec la fonction delete_by_user_id du modele repartition";
    //     }else
    //     echo "problème avec la fonction delete_by_user_id du modele Repartition_template_items";
    // }


    public function is_in_operation($operationId){
        $query = self::execute("SELECT user FROM repartitions WHERE operation = :id ",
                                array("id"=>$operationId));
        if($query->rowCount()==0){
            return false;
        }
        return $query;
    }

    public static function get_user_id_by_name($full_name)
    {
        $query = self::execute("SELECT id FROM  `users` where full_name=':fullname'", array("fullname" => $full_name));
        $data = $query->fetch(); //un seul resultat max
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return $data;
        }
    }

    public static function get_by_name($full_name)
    { //récup l'user par son full_name
        $query = self::execute("SELECT * FROM  `users` where full_name=:fullname", array("fullname" => $full_name));
        $data = $query->fetch(); //un seul resultat max
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new User($data["id"], $data["mail"], $data["hashed_password"], $data["full_name"], $data["role"], $data["iban"]);
        }
    }

    public function getIban(): string|null
    {
        return $this->iban;
    }

    public function setFullName(string $fullname): string
    {
        return $this->full_name = $fullname;
    }


    public function setIban(string $iban): void
    {
        $this->iban = $iban;
    }

    public function update_password()
    {
        if (self::get_by_id($this->id) != null) {
            self::execute("UPDATE users SET
                hashed_password=:hashed_password WHERE id=:id ",
                array(
                    "hashed_password" => $this->hashed_password,
                    "id" => $this->id
                )
            );
        }
        return $this;
    }



    public function setRole(string $role): void
    {
        $this->role = $role;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getMail(): string
    {
        return $this->mail;
    }
    public function setMail(string $mail): string
    {
        return $this->mail = $mail;
    }

    public function isAdmin(): string
    {
        return $this->role == "admin";
    }

    public static function get_by_id($id)
    { //récup l'user par son id
        $query = self::execute("SELECT * FROM  `users` where id=:id", array("id" => $id));
        $data = $query->fetch(); //un seul resultat max
        if ($query->rowCount() == 0) {
            return null;
        } else {
            return new User($data["id"], $data["mail"], $data["hashed_password"], $data["full_name"], $data["role"], $data["iban"]);
        }
    }
    public static function get_all()
    { //récup tous les users
        $query = self::execute("SELECT * FROM  `users` ", array());
        $data = $query->fetchAll();
        $results = [];
        foreach ($data as $row) {
            $results[] = new User($row["id"], $row["mail"], $row["hashed_password"], $row["full_name"], $row["role"], $row["iban"]);
        }
        return $results;
    }
    public static function not_participate($tricountId)
    { //récup tous les users
        $query = self::execute("SELECT *
            FROM users
            WHERE id
            NOT IN (SELECT user FROM subscriptions WHERE tricount =:tricountId)", array("tricountId" => $tricountId));
        $data = $query->fetchAll();
        $results = [];
        foreach ($data as $row) {
            $results[] = new User($row["id"], $row["mail"], $row["hashed_password"], $row["full_name"], $row["role"], $row["iban"]);
        }
        return $results;
    }
    public static function get_by_mail($mail)
    { //récup l'user par son mail
        $query = self::execute("SELECT * FROM  `users` where mail=:mail", array("mail" => $mail));
        $data = $query->fetch(); //un seul resultat max
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new User($data["id"], $data["mail"], $data["hashed_password"], $data["full_name"], $data["role"], $data["iban"]);
        }
    }

    public static function get_user_by_name($full_name)
    { //récup l'user par son full_name
        $query = self::execute("SELECT * FROM  `users` where full_name=:fullname", array("fullname" => $full_name));
        $data = $query->fetch(); //un seul resultat max
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new User($data["id"], $data["mail"], $data["hashed_password"], $data["full_name"], $data["role"], $data["iban"]);
        }
    }

    public static function get_by_iban($iban)
    { //récup l'user par son iban
        $query = self::execute("SELECT * FROM  `users` where iban=:iban", array("iban" => $iban));
        $data = $query->fetch(); //un seul resultat max
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new User($data["id"], $data["mail"], $data["hashed_password"], $data["full_name"], $data["role"], $data["iban"]);
        }
    }

    public function update_profile($full_name, $mail, $iban)
    {
        if (self::get_by_id($this->id) != null) {
            self::execute("UPDATE users
                    SET full_name=:full_name,
                    mail=:mail,
                    iban=:iban
                    where id=:id",
                array(
                    "id" => $this->id,
                    "full_name" => $full_name,
                    "mail" => $mail,
                    "iban" => $iban
                )
            );
        }
        return $this;
    }


    public function update()
    {
        if (self::get_by_id($this->id) != null) {
            self::execute("UPDATE users SET
                mail=:mail,
                hashed_password=:hashed_password,
                full_name=:full_name,
                role=:role,
                iban=:iban,
                WHERE id=:id ",
                array(
                    "mail" => $this->mail,
                    "hashed_password" => $this->hashed_password,
                    "full_name" => $this->full_name,
                    "role" => $this->role,
                    "iban" => $this->iban,
                    "id" => $this->id
                )
            );
        } else {
            self::execute("INSERT INTO
                 `users`(mail,
                 hashed_password,
                 full_name,
                 role,
                 iban)
                VALUES(:mail,
                    :hashed_password,
                    :full_name,
                    :role,
                    :iban)",
                array(
                    "mail" => $this->mail,
                    "hashed_password" => $this->hashed_password,
                    "full_name" => $this->full_name,
                    "role" => $this->role,
                    "iban" => $this->iban
                )
            );
        }
        return $this;
    }

    //VALIDATIONS
    public static function validate_login($mail, $hashed_password): array
    {
        $errors = [];
        $user = User::get_by_mail($mail);
        if ($user) {
            if (!self::check_password($hashed_password, $user->hashed_password)) {
                $errors[] = "Wrong password. Please try again.";
            }
        } else {
            $errors[] = "Can't find a user with the mail : '$mail'. Please sign up.";
        }
        return $errors;
    }

    public function validate(): array
    {
        $errors = [];
        if (isset($this->mail) && self::validateEmail($this->mail)) {
            $user = self::get_by_mail($this->mail);
            if (!is_null($user) && self::validate_unicity($this->mail)) {
                $errors[] = "This email is already used.";
            }

        }

        if (!(isset($this->full_name) && strlen($this->full_name) >= 3)) {
            $errors[] = "Full Name must be at least 3 characters.";
        }

        return $errors;
    }

    public static function validateFullName($full_name): bool
    {
        if (strlen($full_name) <= 3) {
            return false;
        }
        return true;
    }


    public static function validateEmail($email): bool
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        }
        return false;
    }

    private static function validate_password($password)
    {
        $errors = [];
        if (strlen($password) < 8 || strlen($password) > 16) {
            $errors[] = "Password length must be between 8 and 16.";
        }
        if (!((preg_match("/[A-Z]/", $password)) && preg_match("/\d/", $password) && preg_match("/['\";:,.\/?!\\-]/", $password))) {
            $errors[] = "Password must contain one uppercase letter, one number and one punctuation mark.";
        }
        return $errors;
    }

    public static function validate_passwords($password, $password_confirm)
    {
        $errors = User::validate_password($password);
        if ($password != $password_confirm) {
            $errors[] = "You have to enter twice the same password.";
        }
        return $errors;
    }

    // public static function validate_iban($iban):bool{
    //     $pattern = '^[a-zA-Z]+[0-9]+(\s+([0-9]+\s+)+)[0-9]+$';
    //     str_replace(' ','',$iban);
    //     //si il n'est pas vide
    //     if(!is_null($iban)){
    //         if (!preg_match($pattern, $iban)) {
    //             return false;
    //         }
    //         return true;
    //     }
    //     return true;
    // }


    public static function validate_unicity($email): array
    {
        $errors = [];
        $user = self::get_by_mail($email);
        if ($user) {
            $errors[] = "This email is already used.";
        }
        return $errors;
    }

    //indique si un mot de passe correspond à son hash
    public static function check_password(string $clear_password, string $hash): bool
    {
        return $hash === Tools::my_hash($clear_password);
    }

    public function list_by_user()
    {
        $query = self::execute("SELECT * FROM `tricounts` t JOIN  subscriptions s ON t.id = s.tricount where user=:user", array("user" => $this->id));
        $data = $query->fetchAll();
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new Tricounts($data["ID"], $data["title "], $data["description"], $data["created_at"], $data["creator"]);
        }
    }

    public function participates_in_tricount(): bool
    {
        $query = self::execute("SELECT *
                                FROM repartition_template_items
                                WHERE user = :user
                                LIMIT 1;
                                ", array("user" =>$this->getUserId()));
        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return true;
        }

    }
    
    public function can_be_delete($tricount): bool
    {
        $query = self::execute("SELECT count(*)
        FROM subscriptions
        WHERE tricount = :tricount
        AND user = :user
        AND user NOT IN (
        SELECT initiator
        FROM operations
        WHERE tricount = :tricount
        )
        AND user NOT IN (
        SELECT user
        FROM repartitions
        JOIN operations
        ON repartitions.operation = operations.id
        WHERE tricount = :tricount
        );", array("tricount"=>$tricount,"user" =>$this->getUserId()));
        if ($query->fetchColumn() == 0) {
            return false;
        } else {
            return true;
        }

    }
    
    public function is_in_tricount($idTricount){
        $query = self::execute("SELECT * from subscriptions s where s.user = :user and s.tricount =:id  ",array("user"=>$this->id,"id"=>$idTricount));
        $data = $query->fetch();
        if($query->rowCount()== 0)
            return false;
        return $data;
    }
    public function is_creator($idTricount){
        $query = self::execute("SELECT * FROM tricounts t where t.creator =:user and t.id=:id ",array("user"=>$this->id,"id"=>$idTricount));
        $data = $query->fetch();
        if($query->rowCount()== 0)
            return false;
        return $data;
    }
    public function is_in_items($idTemplate){
        $query = self::execute("SELECT * FROM repartition_template_items rti where rti.user =:user and rti.repartition_template=:id ",array("user"=>$this->id,"id"=>$idTemplate));
        $data = $query->fetch();
        if($query->rowCount()== 0)
            return false;
        return $data;
    }
}
?>
