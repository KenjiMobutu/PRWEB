<?php
require_once "framework/Model.php";

class User extends Model
{
    public ?int $id;
    public string $mail;
    public string $hashed_password;
    public string $full_name;
    public string $role;
    public ?string $iban;

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

    public function getPassword(): string
    {
        return $this->hashed_password;
    }

    public function setPassword(string $hashed_password): void
    {
        $this->hashed_password = $hashed_password;
    }



    public function isAdmin(): string
    {
        return $this->role == "admin";
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
    public static function get_by_mail($mail)
    { //récup l'user par son id
        $query = self::execute("SELECT * FROM  `users` where mail=:mail", array("mail" => $mail));
        $data = $query->fetch(); //un seul resultat max
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new User($data["id"], $data["mail"], $data["hashed_password"], $data["full_name"], $data["role"], $data["iban"]);
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

    public function update()
    {
        if (self::get_by_id($this->id) != null) {
            self::execute("UPDATE users SET 
                mail=:mail,
                hashed_password=:hashed_password,
                full_name=:full_name,
                Role=:role,
                iban=:iban,
                WHERE id=:id ",
                array(
                    "id" => $this->id,
                    "mail" => $this->mail,
                    "hashed_password" => $this->hashed_password,
                    "full_name" => $this->full_name,
                    "Role" => $this->role,
                    "iban" => $this->iban
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

}
?>