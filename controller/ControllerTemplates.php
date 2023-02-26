<?php 
require_once 'model/User.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';
require_once 'model/Repartition_templates.php';
require_once 'model/Repartition_template_items.php';
require_once 'model/tricounts.php';
require_once 'model/participations.php';




class ControllerTemplates extends Controller
{
    public function index() : void
    {
        $this->templates();
    }
    
    public function templates()
    {
        $userlogged = $this->get_user_or_redirect();
        $user = User::get_by_id($userlogged->getUserId());
        if (isset($_GET['param1']) && !is_numeric($_GET['param1'])) {
            $this->redirect('main', "error");
        }

        if($user->is_in_tricount($_GET['param1']) || $user->is_creator($_GET['param1'])){
            $items = [];
            $tricount = Tricounts::get_by_id($_GET["param1"]);
            $templates = Repartition_templates::get_by_tricount($_GET["param1"]);
            if($templates !== null){
                foreach($templates as $template){
                    $items[] = $template->get_items();
                
                }
            }
            (new View("templates"))->show(array("user"=>$user,
                                                "templates"=>$templates, 
                                                "tricount"=>$tricount, 
                                                "items"=>$items,));
        }
        else
            $this->redirect('main', "error");        
    }

    public function edit_template(){
        $userlogged = $this->get_user_or_redirect();
        $user = User::get_by_id($userlogged->getUserId());
        if($user->is_in_tricount($_GET['param1']) || $user->is_creator($_GET['param1'])){  
            if($_GET['param1'] !==null && (isset($_GET['param2']) && $_GET['param2'] !== null)){
                $tricount = Tricounts::get_by_id($_GET["param1"]);
                $template = Repartition_templates::get_by_id($_GET['param2']);
                if(empty(Repartition_templates::template_exist_in_tricount($template->get_id(),$tricount->get_id()))){
                    //dans le cas si l'utilisateur modifie l'url
                    $this->redirect("user","profile");
                }
                $listUser = [];
                
                if($template === null){
                    $this->redirect("templates","edit_template".$tricount->get_id());
                }
                $listUser = Participations::get_by_tricount($tricount->get_id());

                $listItems = Repartition_template_items::get_user_by_repartition($template->get_id());            
                (new View("edit_template"))->show(array("user"=>$user, 
                                                        "tricount"=>$tricount,
                                                        "template"=>$template,
                                                        "listUser"=>$listUser,
                                                        "listItems"=>$listItems));
            }else{
                if($_GET['param1'] !== null ){
                    $tricount = Tricounts::get_by_id($_GET["param1"]);
                    $listUser = Participations::get_by_tricount($tricount->get_id());
                }                
                (new View("edit_template"))->show(array("user"=>$user,
                                                        "tricount"=>$tricount,
                                                        "listUser"=>$listUser));
            }
        }else{
            $this->redirect("main","error");
        }     
        
    }

    public function editTemplate(){
        $userlogged = $this->get_user_or_redirect();
        $user = User::get_by_id($userlogged->getUserId());
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if(empty($_POST["c"])){
                $this->redirect("templates","templates",$_POST["tricountId"]);
            }
            if($_POST["templateID"] !== "" && isset($_POST["template_title"]) && isset($_POST["c"]) && isset($_POST["w"])){
                $checkedUsers = $_POST["c"];
                $weights = $_POST["w"];
                $template_title = Tools::sanitize($_POST["template_title"]);
                $template = Repartition_templates::get_by_id($_POST["templateID"]);
                if($_POST["template_title"] !== $template->get_title()){
                    $template->update_title($template_title);
                }
                $listUser = Participations::get_by_tricount($_POST["tricountId"]);

                // foreach($checkedUsers as $c){
                //     foreach ($listUser as $u){
                //      echo "<pre>";
                //     print_r($c);print_r($u);
                //     echo "</pre>";
                    
                //     }
                // }
               
                // echo "\n";
                // foreach($weights as $i) {
                //     echo $i . "-----";
                // }   
                //die();
                if(!is_null($template)){
                    Repartition_template_items::delete_by_repartition_template($template->get_id());
                    for($i = 0; $i <= count($weights)+50; $i++) {
                        if((isset($checkedUsers[$i]) && $checkedUsers[$i] !=="") && (isset($weights[$i]) && $weights[$i] !=="")){

                            if($weights[$i] ==="" )
                                $weights[$i] = 0;
                            Repartition_template_items::addNewItems($checkedUsers[$i],
                            $template->id,
                            $weights[$i]); 
                        }
                    };
                }
                $this->redirect("templates", "templates",$_POST["tricountId"]);                
            }else if(isset($_POST["template_title"]) && isset($_POST["c"]) && isset($_POST["w"]) && $_POST["templateID"] === ""){
                // Récupère les valeurs des inputs
                $checkedUsers = $_POST["c"];
                $weights = $_POST["w"];
                // Utilise les valeurs pour ajouter à la base de données
                $template_title = Tools::sanitize($_POST["template_title"]);
                $template = new Repartition_templates(null,$_POST["template_title"], $_POST["tricountId"] );
                $template->newTemplate($template_title, $_POST["tricountId"]);
                if($template !== null){
                    for($i = 0; $i <= count($checkedUsers)+50; $i++) {
                        if((isset($checkedUsers[$i]) && $checkedUsers[$i] !== null) && isset($weights[$i]) && $weights[$i] !== null ){
                        Repartition_template_items::addNewItems($checkedUsers[$i],
                            $template->get_id(),
                            $weights[$i]); 
                        }
                    }
                    $this->redirect("templates", "templates",$_POST["tricountId"]);
                }
            }else
                $this->redirect("main","error");
        }else
            $this->redirect("main","error");
    }



    public function delete_template(){
        $userlogged = $this->get_user_or_redirect();
        $user = User::get_by_id($userlogged->getUserId());

        if($user->is_in_tricount($_GET['param1'] || $user->is_in_items($_GET['param1'])  )){
            $userlogged = $this->get_user_or_redirect();
            $user = User::get_by_id($userlogged->getUserId());
            if (isset($_GET['param1']) && !is_numeric($_GET['param1'])) {
                $this->redirect('main', "error");
            }else{    
                $template = Repartition_templates::get_by_id($_GET['param1']);
                if(is_null($template)){
                    $this->redirect("user","profile");
                }
                if(isset($_POST['submitted'])){
                    if($_POST['submitted'] === "Cancel"){
                        $this->redirect("templates","templates",$template->get_tricount()); // recuperer l'id du tricount lié au template
                    }else if ($_POST['submitted'] === "Delete"){
                        $tricount = $template->get_tricount();
                        $template = $template->delete_by_id();
                        $this->redirect("templates","templates", $tricount);
                    }
                }
            }
            (new View("delete_template"))->show(array("user"=>$user,
                                                "template"=>$template));
        }else{
            $this->redirect("user","profile");
        }
    }

}
?>