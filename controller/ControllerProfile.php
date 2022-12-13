<?php

require_once 'model/User.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';


class ControllerProfile extends Controller
{

    // static int $first_time = 0;

    public function index() :void
    {
        $this->profile();
    }

    //profil de l'utilisateur connecté ou donné

    /**
     * @throws Exception
     */
    public function profile()
    {
        $user = $this->get_user_or_redirect();
        if (isset($_GET['param1']) && !is_numeric($_GET['param1'])) {
            $this->redirect('main', "error");
        }
        $user= array_key_exists('param1', $_GET);

        (new View("profile"))->show(array("user"=> $user)); //show may throw Exception
   }
        
    

   public function edit_profile()
   {
       /** @var User $loggedUser */
       $loggedUser = $this->get_user_or_redirect();
       $errors = [];

       if (isset($_GET['param1']) && !is_numeric($_GET['param1'])) {
           $this->redirect('main', "error");
       }

       $user = array_key_exists('param1', $_GET) && $loggedUser->isAdmin() ? 
           User::get_by_id($_GET['param1']) : $loggedUser;
       $success = array_key_exists('param2', $_GET) && $_GET['param2'] === 'ok' ? 
           "Your profile has been successfully updated." : "";


       if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if(array_key_exists("fullName", $_POST)){
                if(User::validateFullName($_POST["fullName"])){
                    $errors[] = "Your name is incorrect";
                }
            }

            if ( array_key_exists("mail", $_POST) ) {
                if(!User::validateEmail($_POST["mail"]))
                    $errors[] = "wrong mail";
            }
            

            if(array_key_exists("iban", $_POST) &&(!is_null($_POST["iban"]))){
                if(User::validate_iban($_POST["iban"])){
                    $errors[] = "The current iban is incorrect";
                }
            }
            

            if (empty($errors)) {
                $user->update_profile();
                $this->redirect("user", "profile", $user->getUserId(), "ok");
            }
       }


       (new View("edit_profile"))->show([
           "user" => $user,
           "errors" => $errors,
           "success" => $success,
           "loggedUser" => $loggedUser
       ]);
   }
}