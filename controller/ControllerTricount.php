<?php
require_once 'model/Repartition_templates.php';
require_once 'model/Repartition_template_items.php';
require_once 'model/User.php';
require_once 'model/Tricounts.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';

class ControllerTricount extends Controller{
  public function index() :void{
    $this->tricount_list();
  }
  public function tricount_list(){
    $loggedUser = $this->get_user_or_redirect();
    if (isset($_GET['param1']) && !is_numeric($_GET['param1'])) {
      $this->redirect('main', "error");
    }
    $user= array_key_exists('param1', $_GET) && $loggedUser->isAdmin() ? User::get_by_id($_GET['param1']) : $loggedUser;
    if (is_null($user)) {
      $user = $loggedUser;
    }
    $tricounts_list = Tricounts::list();
    //var_dump($tricounts_list);
    //$user_tricounts_list = $tricounts_list->by_user($loggedUser->id);
    //var_dump($user_tricounts_list);
    (new View("list_tricounts"))->show(array("loggedUser" => $loggedUser, "user" => $user, "tricounts_list"=>$tricounts_list));

  }

  public function add(){
    $user = $this->get_user_or_redirect();
    if (!is_null($user)) {

      (new View("add_tricount"))->show(array("user" => $user));
    } else {
      $this->redirect("user","profile");
    }
  }

}

?>
