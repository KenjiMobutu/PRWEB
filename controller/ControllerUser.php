<?php

require_once 'framework/View.php';
require_once 'framework/Controller.php';
require_once 'model/User.php';

class ControllerUser extends Controller
{

    //page d'accueil.
    public function index(): void
    {
        if (isset($_GET["param1"])) {
            $this->redirect('profile');
        }
    }

    public function logout(): void
    {
        Controller::logout();
    }

    public function profile()
    {
        $user = $this->get_user_or_redirect();
        $user = User::get_by_id($user->getUserId());
        (new View("profile"))->show(array("user" => $user));//show may throw Exception
    }
    public function can_be_delete($userId, $tricountId){
        if(isset($_POST["tricountId"])){
            $tricountId = $_POST["tricountId"];
            $user = User::get_by_id($userId);
            $deletable = $user->beDeletable($tricountId);
        echo json_encode($deletable);
        }

    }

}
