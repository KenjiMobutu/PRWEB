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
       if (isset($_GET['param1']) && !is_numeric($_GET['param1'])) {
           $this->redirect('main', "error");
       }
       $user = array_key_exists('param1', $_GET) && $loggedUser->isAdmin() ? 
           User::get_by_id($_GET['param1']) : $loggedUser;
       $errors = [];
       $success = array_key_exists('param2', $_GET) && $_GET['param2'] === 'ok' ? 
           "Your profile has been successfully updated." : "";


       if ($_SERVER['REQUEST_METHOD'] === 'POST') {

           if (
               array_key_exists("mail", $_POST)
               && array_key_exists("fullName", $_POST)
               && array_key_exists("title", $_POST)
           ) {
               $mail = $_POST["mail"];
               $fullName = $_POST["fullName"];
               $title = $_POST["iban"];
           } else {
               $this->redirect('main', "error");
           }

           $user->setMail($mail);
           $user->setFullName($fullName);
           $user->setIban($title);


           $errors = $user->validate();

           if (empty($errors)) {
               $user->update();
               $this->redirect("profile", "edit_profile", $user->getUserId(), "ok");
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