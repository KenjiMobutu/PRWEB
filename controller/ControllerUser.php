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
        if ($user->getUserId() === 1) {
            $user->setRole("admin");
        }
        (new View("profile"))->show(array("user" => $user));//show may throw Exception
    }
    public function handle_can_be_delete_request() {
        // Get the JSON payload from the POST request
        $inputJSON = file_get_contents('php://input');
        $input = json_decode($inputJSON, true);

        // Extract userId and tricountId from the JSON payload
        $userId = $input['userId'];
        $tricountId = $input['tricountId'];
        $creator = $input['creator'];
        var_dump($creator);
        // Pass the extracted data to the can_be_delete() function
        $deletable = $this->can_be_delete($userId, $tricountId,$creator);

        // Return the result as JSON
        echo json_encode([$userId => $deletable]);
    }

    public function can_be_delete($userId, $tricountId, $creator) {
        // Retrieve the tricount object
        //$tricount = Tricounts::get_by_id($tricountId);

        // Get the creator's user ID
        //$creatorUserId = $tricount->get_creator_id();

        // If the user is the creator, return false (not deletable)
        if ($userId == $creator) {
            return false;
        }

        // Otherwise, check if the user is deletable
        $user = User::get_by_id($userId);
        return  $user->can_be_delete($tricountId);
    }

}
