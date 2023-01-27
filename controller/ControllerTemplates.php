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
    
    /**
     *                      que doit faire templates?
     * recevoir l'id du tricount pour afficher le nom dans la vue       ($get param 1 )
     * récupérer les repartition_templates pour afficher le nom de la répartition
     * récupérer les items des templates et le nom des utilisateurs concerné
     */

    /**
     *              Que doit faire la vue templates
     * afficher le nom du tricount
     * faire un foreach des templates reçu
     * afficher le nom du template
     * faire un foreach pour récupérer les données du templates
     * faire appel a une fonction qui permet de récup le full name du user par son id
     * récupérer le poid lié au user 
     * récupérer la fonction qui fait la somme du poid du template 
     */

    public function templates()
    {
        $userlogged = $this->get_user_or_redirect();
        $user = User::get_by_id($userlogged->getUserId());
        $items = [];
        if (isset($_GET['param1']) && !is_numeric($_GET['param1'])) {
            $this->redirect('main', "error");
        }else{
            $tricount = Tricounts::get_by_id($_GET["param1"]);
            $templates = Repartition_templates::get_by_tricount($_GET["param1"]);
            if($templates !== null){
                foreach($templates as $template){
                    $items[] = $template->get_items();
                
                }
            }
        }
        
        (new View("templates"))->show(array("user"=>$user,
                                            "templates"=>$templates, 
                                            "tricount"=>$tricount, 
                                            "items"=>$items,));
    }

    public function edit_template(){
        $userlogged = $this->get_user_or_redirect();
        $user = User::get_by_id($userlogged->getUserId());
        $listUser = [];
        
        if($_GET['param1'] !==null && (isset($_GET['param2']) && $_GET['param2'] !== null)){
            $tricount = Tricounts::get_by_id($_GET["param1"]);
            $template = Repartition_templates::get_by_id($_GET['param2']);
            if($template === null){
                $this->redirect("templates","edit_template".$tricount->get_id());
            }
            $listUser = Participations::get_by_tricount($tricount->get_id());

            $listItems[] = Repartition_template_items::get_user_by_repartition($template->get_id());
        
            (new View("edit_template"))->show(array("user"=>$user, 
                                                    "tricount"=>$tricount,
                                                    "template"=>$template,
                                                    "listUser"=>$listUser));
        }else{     
            if($_GET['param1'] !== null ){
                $tricount = Tricounts::get_by_id($_GET["param1"]);
                $listUser = Participations::get_by_tricount($tricount->get_id());
                
            }
            
                (new View("edit_template"))->show(array("user"=>$user,
                                                        "tricount"=>$tricount,
                                                        "listUser"=>$listUser));
            
        }
        
    }

    public function editTemplate(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if(isset($_POST["template_title"]) && isset($_POST["c"]) && isset($_POST["w"])){
                // Récupère les valeurs des inputs
                $checkedUsers = $_POST["c"];
                $weights = $_POST["w"];
    
                // Utilise les valeurs pour ajouter à la base de données ou pour d'autres traitements
                $template = new Repartition_templates(null,$_POST["template_title"], $_POST["tricountId"] );
                $template->newTemplate($_POST["template_title"], $_POST["tricountId"]);

                
                
                if($template !== null){
                    for($i = 0; $i <= count($checkedUsers)+1; $i++) {                                       
                        if(isset($checkedUsers[$i]) && $checkedUsers[$i] !== null){
                           Repartition_template_items::addNewItems($checkedUsers[$i],
                            $template->id,
                            $weights[$i]); 
                        }
                        
                    }
                    $this->redirect("templates", "templates", $_POST["tricountId"]);    
                }
            }       
        }
    }
    public function delete_template(){

        $userlogged = $this->get_user_or_redirect();
        $user = User::get_by_id($userlogged->id);
        if (isset($_GET['param1']) && !is_numeric($_GET['param1'])) {
            $this->redirect('main', "error");
        }else{    
            $template = Repartition_templates::get_by_id($_GET['param1']);
            if($template === null){
                $this->redirect("user","profile");
            }
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

        (new View("delete_template"))->show(array("user"=>$user,
                                            "template"=>$template));
    }

}
?>