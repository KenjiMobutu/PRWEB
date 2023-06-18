<?php
require_once 'model/Repartition_templates.php';
require_once 'model/Repartition_template_items.php';
require_once 'model/User.php';
require_once 'model/Operation.php';
require_once 'model/Tricounts.php';
require_once 'model/Participations.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';

class ControllerExam1s2c extends Controller{
  public function index() :void{
    $this->admin();
  }

  public function admin(){
    $user = $this->get_user_or_redirect();
    $users = User::get_all();
    if(isset($_POST['userId'])){
      $selectedUser = $_POST['userId'];
      $this->redirect("exam1s2c","resultAdmin",$selectedUser);
    }

    (new View("admin"))->show(array("user" => $user, "users" => $users));
  }

  public function resultAdmin(){
    $user = $this->get_user_or_redirect();
    $users = User::get_all();
    if(isset($_GET['param1'])){
      $selectedUser = $_GET['param1'];
      $subscribedTricount = Tricounts::by_user($selectedUser);
    }

    (new View("admin"))->show(array("user" => $user, "users" => $users,"selectedUser" => $selectedUser,"subscribedTricount" => $subscribedTricount));
  }

  public function get_operations(){
    $operations = '';
    if(isset($_GET['param1']) && isset($_GET['param2'])){
      $tricountId = $_GET['param1'];
      $userId = $_GET['param2'];
      $operations = Operation::get_by_tricount_and_initiator($tricountId,$userId);
    }

    echo $operations;
  }

}

?>
