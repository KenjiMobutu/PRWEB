<?php

require_once 'framework/View.php';
require_once 'framework/Controller.php';
require_once 'model/User.php';

class ControllerUser extends Controller
{

    //page d'accueil. 
    public function index() :void
    {
        if (isset($_GET["param1"])) {
            $this->redirect('profile');
        }
    }

    public function logout() :void
    {
        Controller::logout();
    }

    /**
     * @throws Exception
     */
    public function profile()
    {
        $loggedUser = $this->get_user_or_redirect();
        if (isset($_GET['param1']) && !is_numeric($_GET['param1'])) {
            $this->redirect('main', "error");
        }
        $user= array_key_exists('param1', $_GET) && $loggedUser->isAdmin() ?
            User::get_user_by_id($_GET['param1']) : $loggedUser;

        if (is_null($user)) {
            $user = $loggedUser;
        }
        (new View("profile"))->show(array("loggedUser" => $loggedUser, "user" => $user)); //show may throw Exception
    }

}