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
    $user = $this->get_user_or_redirect();
    $users = User::get_all();
    if(isset($_POST['userId'])){
      $selectedUser = $_POST['userId'];
      $this->redirect("exam1s2c","admin",$selectedUser);
    }
    (new View("admin"))->show(array("user" => $user,"users" => $users));
  }

  public function admin():void{

    $users = User::get_all();
    $user = $this->get_user_or_redirect();
    $tricounts ='';
    if (isset($_GET['param1']) && isset($_GET['param1'])!= null){
      $selectedUser = $_GET['param1'];
      $users = User::get_all();
      $tricounts = Tricounts::by_user($selectedUser);
    }
    (new View("admin"))->show(array("user" => $user,"users" => $users, "selectedUser" => $selectedUser, "tricounts" => $tricounts));
  }

  public function get_expenses_json(){
    if (isset($_GET['param1']) && isset($_GET['param1'])!= null && isset($_GET['param2']) && isset($_GET['param2'])!= null){
      $tricount = $_GET['param1'];
      $user = $_GET['param2'];
      $operations = Operation::getOperationByTricountAndInitiator($tricount,$user);
    }
    echo $operations;
  }


}

?>
