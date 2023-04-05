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

  public function add() : User|false{
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
      $this->redirect("tricount","edit",$idTricount);die();
    }else {
      $this->redirect("tricount","index");
    }
    return false;
  }
  public function add_service() : void {
    

    $tricount_id = $_GET["param1"] ?? null;
    $user_id = $_POST['names'] ?? null;

    if (!$tricount_id || !$user_id) {
        echo json_encode(['success' => false, 'message' => 'Missing parameters']);
        return;
    }

    $tricount = Tricounts::get_by_id($tricount_id);
    if (!$tricount) {
        echo json_encode(['success' => false, 'message' => 'Tricount not found']);
        return;
    }

    $participation = new Participations($tricount_id, $user_id);
    if (!$participation->add()) {
        echo json_encode(['success' => false, 'message' => 'Failed to add participation']);
        return;
    }

    echo json_encode(['success' => true]);
}
  public function delete_service() : void {
    $user = $this->delete();
    echo $user ? "true" : "false";
  }
  public function delete(): User|false{
    $user = $this->get_user_or_redirect();
    if (isset($_POST["userId" ]) && is_numeric($_POST["userId" ]) && $_POST["userId" ] != null && (isset($_GET["param1"]) && $_GET["param1"]!="")) {
      $id = $_POST["userId" ];
      $idT = $_GET["param1"];
      $tricount = Tricounts::get_by_id($idT);
      $result = Participations::delete_by_user_id_and_tricount($id,$tricount->get_id());
      if ($result) {
        $this->redirect('tricount', "edit",$tricount->get_id());
      } else {
        // affiche un message d'erreur
        echo "Une erreur s'est produite lors de la suppression de la participation.";
      }
    }
    return false;
  }

  public function get_visible_users_service() : void {

    if(isset($_GET["param1"]) && $_GET["param1"]!=""){
        var_dump($_GET["param1"]);
        $id = $_GET['param1'];
        $tricount = Tricounts::get_by_id($id);
        $users_json = $tricount->not_participate_as_json($id);
    }
    echo $users_json;
  }




}
