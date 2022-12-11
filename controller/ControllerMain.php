<?php

require_once 'model/User.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';

class ControllerMain extends Controller
{

    //si l'utilisateur est connecté, redirige vers son profil.
    //sinon, produit la vue d'accueil.
    public function index() :void
    {
        if ($this->user_logged()) {
            $this->redirect("user","profile");
        } else {
            (new View("index"))->show();
        }
    }


    public function error()
    {
        /** @var User $loggedUser */
        $loggedUser = $this->get_user_or_redirect();

        $error = "YOU CANNOT ACCESS THIS RESOURCE!";
        (new View("error"))->show(["loggedUser" => $loggedUser, "error" => $error]);
    }


    public function login()
    {
        $mail = '';
        $password = '';
        $errors = [];
        if (isset($_POST['mail']) && isset($_POST['password'])) { //note : pourraient contenir des chaînes vides
            $mail = $_POST['mail'];
            $password = $_POST['password'];

            $errors = User::validate_login($mail, $password);
            if (empty($errors)) {
                $this->log_user(User::get_user_by_mail($mail));
            }
        }
        (new View("login"))->show(array("mail" => $mail, "password" => $password, "errors" => $errors));
    }

    //gestion de l'inscription d'un utilisateur
    public function signup()
    {
        $mail = '';
        $full_name = '';
        $iban='';
        $password = '';
        $password_confirm = '';
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (
                array_key_exists('mail', $_POST) &&
                array_key_exists('password', $_POST) &&
                array_key_exists('password_confirm', $_POST) &&
                array_key_exists('full_name', $_POST) &&
                array_key_exists('iban', $_POST)
                
            ) {

                $mail = $_POST['mail'];
                $password = $_POST['password'];
                $password_confirm = $_POST['password_confirm'];
                $full_name = $_POST['full_name'];
                $iban = $_POST['iban'];
                
                $newUser = new User($id = null,$mail,Tools::my_hash($password), $full_name,$role='user', $iban);
                
                $errors = User::validate_passwords($password, $password_confirm);
                $errors = array_merge($errors, $newUser->validate());
                if (empty($errors)) {
                    $newUser->update();
                    $this->log_user($newUser, "profile");
                }
            } else {
                $errors[] ="All information are needed to complete your registration.";
            }
        }

        (new View("signup"))->show([
            'mail' => $mail,
            'password' => $password,
            'password_confirm' => $password_confirm,
            'full_name' => $full_name,
            'iban' => $iban,
            'errors' => $errors
        ]);
    }
}
