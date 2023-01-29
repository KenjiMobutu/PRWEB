<?php
require_once 'model/Repartition_templates.php';
require_once 'model/Repartition_template_items.php';
require_once 'model/User.php';
require_once 'model/Operation.php';
require_once 'model/Tricounts.php';
require_once 'model/Participations.php';
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
    $tricounts_list = Tricounts::list($user->getUserId());
    (new View("list_tricounts"))->show(array("loggedUser" => $loggedUser, "user" => $user, "tricounts_list"=>$tricounts_list));
  }

  public function add(){
    $user = $this->get_user_or_redirect();
    if (!is_null($user)) {
      $id = NULL;
      $errors = [];
      $title = '';
      $description = '';
      $tricount = '';
      $created_at = date('Y-m-d H:i:s');
      if ((isset($_POST["title"]) && $_POST["title"]!="")&&(isset($_POST["description"])&& $_POST["description"]!="")){
        $title = $_POST["title"];
        $description = $_POST["description"];
        $creator = $user->getUserId();
        $tricount = new Tricounts($id,$title,$description,$created_at,$creator);
        if (count($errors) == 0) {
          $tricount->update();
          $this->redirect("tricount", "index");
        }
      }

      (new View("add_tricount"))->show(array("user" => $user,"tricount" =>$tricount));
    } else {
      $this->redirect("user","profile");
    }
  }

  public function edit(){
    $user = $this->get_user_or_redirect();
    $id = null;
    $sub = [];
    if (isset($_GET['param1']) || isset($_POST['param1'])) {
      $id = isset($_POST['param1']) ? $_POST['param1'] : $_GET['param1'];
      $tricount = Tricounts::get_by_id($id);
      $subscriptions = Participations::by_tricount($tricount->get_id());
      $users = User::not_participate($tricount->get_id());
      foreach($subscriptions as $s){
        $sub[] = User::get_by_id($s->user);
      }
    }else {
      $this->redirect("tricount","index");
    }

    (new View("edit_tricount"))->show(array("user" => $user,"tricount" => $tricount,"subscriptions" =>$subscriptions, "sub" => $sub,"users" => $users));
  }

  public function delete(){
    $user = $this->get_user_or_redirect();
    if (isset($_GET['param1']) && is_numeric($_GET['param1']) && $_GET['param1'] != null ) {
      $id = $_GET['param1'];
      $tricount = Tricounts::get_by_id($id);
      if($tricount->get_creator_id() === $user->getUserId()){
        //$tricount->delete($tricount->get_id());
        (new View("delete_tricount"))->show(array("user" => $user,"tricount" => $tricount));
      }else {
        $this->redirect('main', "error");
      }
      //var_dump($tricount);
    }
  }
  public function delete_confirm(){
    $user = $this->get_user_or_redirect();
    if (isset($_GET['param1']) && is_numeric($_GET['param1']) && $_GET['param1'] != null ) {
      $id = $_GET['param1'];
      $tricount = Tricounts::get_by_id($id);
      if($tricount->get_creator_id() === $user->getUserId()){
        $tricount->delete($tricount->get_id());
        $this->redirect('tricount', "index");
      }else {
        $this->redirect('main', "error");
      }
      //var_dump($tricount);
    }
  }
  public function update(){

  }

}

?>
