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
       }

       $user = array_key_exists('param1', $_GET) && $loggedUser->isAdmin() ? 
           User::get_by_id($_GET['param1']) : $loggedUser;
       $success = array_key_exists('param2', $_GET) && $_GET['param2'] === 'ok' ? 
           "Your profile has been successfully updated." : "";


       if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if(isset($_POST["mail"]) || isset($_POST["fullName"]) || isset($_POST["iban"])){
                if(isset($_POST["mail"])){
                    if(!User::validateEmail($_POST["mail"])){
                        $errors[] = "Wrong mail";
                    }
                }
                if(isset($_POST["fullName"])){
                    if(!User::validateFullName($_POST["fullName"])){
                        $errors[] = "Bad name. Too short.";
                    }
                }
                // if(isset($_POST["iban"])){
                //     if(User::validate_iban($_POST["iban"])){
                //         $errors[] = "Bad Iban";
                //     }
                // }
                $updatedUser = new User($user->id,
                            $_POST["mail"],
                            $user->hashed_password,
                            $_POST["fullName"], 
                            $user->role,
                            $_POST["iban"]);
                    //var_dump($updatedUser); die(); 
            }
            if(empty($errors)){
                $updatedUser->update_profile($_POST["fullName"],$_POST["mail"],  $_POST["iban"]);
                $this->redirect("user","profile",$updatedUser->id,"ok");
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