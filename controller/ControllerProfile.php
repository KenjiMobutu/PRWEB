<?php

require_once 'model/User.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';


class ControllerProfile extends Controller
{

    // static int $first_time = 0;

    public function index(): void
    {
        $this->profile();
    }

    //profil de l'utilisateur connecté ou donné

    /**
     * @throws Exception
     */
    public function profile() {
        $user = $this->get_user_or_redirect();
        
        (new View("profile"))->show(array("user" => $user));//show may throw Exception
    }

    public function change_password()
    {
        $user = $this->get_user_or_redirect();
        if (isset($_GET['param1']) && !is_numeric($_GET['param1'])) {
            $this->redirect('main', "error");
        }
        if(isset($_GET['param1']) && $_GET['param1'] !== $user->getUserId()){
            $this->redirect('main', 'error');
        }
        $errors = [];
        $success = array_key_exists('param2', $_GET) && $_GET['param2'] === 'ok' ? 
            "Your password has been successfully changed." : '';


        // If the form has been submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate the entered passwords
            if (
                array_key_exists('newPassword', $_POST)
                && array_key_exists('confirmPassword', $_POST)
            ) {

                $errors = User::validate_passwords($_POST["newPassword"], $_POST["confirmPassword"]);

                // If the connected user is updated, also verify the current password
                if (array_key_exists('currentPassword', $_POST)) {
                    if (!User::check_password($_POST["currentPassword"], $user->getPassword())) {
                        $errors[] = "The current password is not correct.";
                    }
                }
                // If passwords are valid, update user
                if (empty($errors)) {
                    $user->setPassword(Tools::my_hash($_POST["newPassword"]));
                    $user->update_password();
                    $this->redirect("profile", "change_password", $user->getUserId(), "ok");
                }
            } else {
                $this->redirect('main', "error");
            }
        }

        (new View("change_password"))->show([
            "user" => $user,
            "errors" => $errors,
            "success" => $success
        ]);
    }
}