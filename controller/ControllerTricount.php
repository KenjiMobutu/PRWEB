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
      $id = null;
      $errors = [];
      $title = '';
      $description = '';
      $tricount = '';
      $created_at = date('Y-m-d H:i:s');
      if ( (isset($_POST["title"]) && $_POST["title"]!==" ")&&(isset($_POST["description"])|| $_POST["description"]===" ")){
        $title = Tools::sanitize($_POST["title"]);
        $errors = Tricounts::validate_title($title);
        $description = Tools::sanitize($_POST["description"]);
        $creator = $user->getUserId();
          $tricount = new Tricounts($id, $title, $description, $created_at, $creator);
          $tricountBool = Tricounts::get_by_title($tricount->get_title());
          if($tricountBool === true){
            $errors[]  = "This tricount already exist";
          }
          if( strlen($description) < 3 && !empty($description)){
            $errors[] = "Description to short min. 3 characters";
          }
          if (count($errors) === 0) {
            $tricount->addTricount();
            $idT = $tricount->get_id();
            $newSubscriber = new Participations($idT, $tricount->get_creator_id());
            $newSubscriber->add();
            $this->redirect("tricount", "result", $idT);
          }
      }
      (new View("add_tricount"))->show(array("user" => $user,"tricount" =>$tricount, "errors"=>$errors, "description"=>$description));
    } else {
      $this->redirect("user","profile");
    }
  }
  public function result() {
    if (!empty($_GET["param1"])) {
      $user = $this->get_user_or_redirect();
        // load tricount corresponding to param
        $id = $_GET["param1"];
        $tricount = Tricounts::get_by_id($id);
        // display results with last created tricount
        $this->redirect("tricount","index");
    }
  }

  public function edit(){
    $user = $this->get_user_or_redirect();
    $id = null;
    $sub = [];
    $errors = [];
    if (isset($_GET['param1']) || isset($_POST['param1'])) {
      $id = isset($_POST['param1']) ? $_POST['param1'] : $_GET['param1'];
      $tricount = Tricounts::get_by_id($id);
      $title = $tricount->get_title();
      $tricountTitle = Tricounts::get_by_title($tricount->get_title());
          if($tricountTitle === true){
            $errors[]  = "This title already exist";
          }
      $subscriptions = Participations::by_tricount($tricount->get_id());
      $users = User::not_participate($tricount->get_id());
      foreach($subscriptions as $s){
        $sub[] = User::get_by_id($s->user);
      }
    }else {
      $this->redirect("tricount","index");
    }

    (new View("edit_tricount"))->show(array("user" => $user,"tricount" => $tricount,"subscriptions" =>$subscriptions, "sub" => $sub,"users" => $users, "errors"=>$errors));
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
    $user = $this->get_user_or_redirect();
    $errors = [];
    if (!is_null($user)) {
      if (isset($_GET['param1']) && is_numeric($_GET['param1']) && $_GET['param1'] != null
          && isset($_POST["title"]) && !empty($_POST["title"])
          && isset($_POST["description"])|| ($_POST["description"]=="")){

        $id = $_GET['param1'];
        $title = Tools::sanitize($_POST["title"]);
        $errors = Tricounts::validate_title($title);
        $description = Tools::sanitize($_POST["description"]);
        $tricount = Tricounts::get_by_id($id);
        $subscriptions = Participations::by_tricount($tricount->get_id());
        $users = User::not_participate($tricount->get_id());
        $tricount2 = Tricounts::get_by_title($_POST["title"]);
        $tricountTitle = $tricount2->get_title();
        if (strcasecmp($title, $tricountTitle) == 0){
            $errors[]  = "This title already exist";
        }
        foreach($subscriptions as $s){
          $sub[] = User::get_by_id($s->user);
        }
        if (count($errors) == 0) {
          $tricount->updateTricount($title,$description);
          $idT = $tricount->get_id();
          $this->redirect("tricount", "result", $idT);
        }
        else {
          // Handle error for invalid tricount id
          (new View("edit_tricount"))->show(array("user" => $user,"tricount" => $tricount,"subscriptions" =>$subscriptions, "sub" => $sub,"users" => $users, "errors"=>$errors));
        }
      }
    } else {
      $this->redirect("user","profile");
    }
  }


}

?>
