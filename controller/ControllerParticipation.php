<?php
require_once 'model/Repartition_templates.php';
require_once 'model/Repartition_template_items.php';
require_once 'model/User.php';
require_once 'model/Operation.php';
require_once 'model/Tricounts.php';
require_once 'model/Participations.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';

class ControllerParticipation extends Controller{
  public function index(): void{
    $this->redirect('profile');
  }

  public function add(){
    $user = $this->get_user_or_redirect();
    $id = null;
    $sub = [];
    $users='';
    $subscriptions='';
    $tricount = '';

    if ((isset($_POST["names"]) && $_POST["names"]!="") && (isset($_GET["param1"]) && $_GET["param1"]!="")) {
      $idUser = $_POST["names"];
      $idTricount = $_GET['param1'];
      var_dump($idTricount);
      var_dump($idUser);
      $newSubscriber = new Participations($idTricount , $idUser );
      if($newSubscriber == NULL){
        $this->redirect("tricount","index");
      }
      $newSubscriber->add();
      $this->redirect("tricount","edit",$idTricount);
    }else {
      $this->redirect("tricount","index");
    }


  }

  public function delete(){
    $user = $this->get_user_or_redirect();
    if (isset($_POST["userId" ]) && is_numeric($_POST["userId" ]) && $_POST["userId" ] != null && (isset($_GET["param1"]) && $_GET["param1"]!="")) {
      $id = $_POST["userId" ];
      $idT = $_GET["param1"];
      $tricount = Tricounts::get_by_id($idT);
      var_dump($tricount);
      $result = Participations::delete_by_user_id_and_tricount($id,$tricount->get_id());

      if ($result) {
        $this->redirect('tricount', "edit",$tricount->get_id());
      } else {
        // affiche un message d'erreur
        echo "Une erreur s'est produite lors de la suppression de la participation.";
      }
    }
  }


}
