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
                $templateID = $_GET['param2'];
                if( is_null($template) || empty(Repartition_templates::template_exist_in_tricount($template->get_id(),$tricount->get_id()))){
                    //Si l'utilisateur modifie l'url
                    $this->redirect("user","profile");
                }
                if($template === null){
                    $this->redirect("templates","edit_template".$tricount->get_id());
                }
                $listUser = Participations::get_by_tricount($tricount->get_id());

                $listItems = Repartition_template_items::get_user_by_repartition($template->get_id());            
                (new View("edit_template"))->show(array("user"=>$user, 
                                                        "tricount"=>$tricount,
                                                        "template"=>$template,
                                                        "listUser"=>$listUser,
                                                        "listItems"=>$listItems,
                                                        "templateID"=>$templateID));
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
            $this->redirect("user","profile");
        }
    }


    public function editTemplate() {
        $userlogged = $this->get_user_or_redirect();
        $user = User::get_by_id($userlogged->getUserId());
        $errors = [];
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tricount = Tricounts::get_by_id($_POST["tricountId"]);
            $listUser = Participations::get_by_tricount($_POST["tricountId"]);
        
            $templateId = $_POST["templateID"];
            $templateTitle = Tools::sanitize($_POST["template_title"]);
            $checkedUsers = isset($_POST["checkedUser"]) ? $_POST["checkedUser"] : [];
            $weights = $_POST["weight"];
        
            $errors = $this->validate($checkedUsers, $templateTitle, $tricount->get_id(), $templateId);
            if (empty($errors) && $this->updateOrCreateTemplate($templateId, $templateTitle, $tricount->get_id(), $this->combine_array($checkedUsers, $weights))) {
                $this->redirect("templates","templates", $tricount->get_id());
            }
        
            // render the view
            (new View("edit_template"))->show(array(
                "user" => $user,
                "tricount" => $tricount,
                "template_title" => $templateTitle,
                "listUser" => $listUser,
                "checkedUser" => $checkedUsers,
                "combined_array" => $this->combine_array($checkedUsers, $weights),
                "weights" => $weights,
                "errors" => $errors,
                "templateID" => $templateId
            ));
        }else{
            $this->redirect("user", "profile", $user->getUserId());
        }
    }
    
    
    private function updateOrCreateTemplate($templateID, $template_title, $tricountId, $combined_array) : bool{
        $errors = [];
        $result = false;
        if(empty($errors)){
            if($templateID !== ""){
                $template = Repartition_templates::get_by_id($templateID);
                if($template_title !== $template->get_title()){
                    $template->update_title($template_title);
                }
                if(!is_null($template)){
                    Repartition_template_items::delete_by_repartition_template($template->get_id());
                    foreach($combined_array as $user_id => $weight) {                        
                        if($weight ==="" )
                            $weight = 1;
                        Repartition_template_items::addNewItems($user_id, $template->id, $weight); 
                    };
                }
            }
            else{
                $template = new Repartition_templates(null, $template_title, $tricountId);
                $template->newTemplate($template_title, $tricountId);
                if($template !== null){
                    foreach($combined_array as $user_id => $weight) {
                        Repartition_template_items::addNewItems($user_id, $template->get_id(), $weight); 
                    }
                }
            }
            $result = true;
        }
        return $result;
    }
    private function combine_array($ids, $weight) : array{
        $combined_array = array();
        foreach ($ids as $i => $id) {
            if (isset($weight[$i])) {
                $combined_array[$id] = $weight[$i];
            } else {
                $combined_array[$id] = null; 
            }
        }
        return $combined_array;
    }
    private function validate($checkedUsers, $template_title, $tricount, $templateId) : array{
        $errors = [];
        // si le tableau est vide
        if(empty($checkedUsers)){
            $errors[] = "You must check at least 1 user ";
        }
        
        // si le title est incorrect
        if(!Repartition_templates::validatetitle($template_title)){
            $errors[] = "Title is not long enough. It must be 3 characters minimum.";
        }
        if($templateId !== ""){
            $currentRepartition = Repartition_templates::get_by_id($templateId);
            if($currentRepartition->get_title() !== $template_title){
                if(Repartition_templates::title_already_exist_in_tricount($template_title, $tricount)){
                    $errors[] = "this title already exist for this tricount";
                }
            }
        }else{
            if(Repartition_templates::title_already_exist_in_tricount($template_title, $tricount)){
                $errors[] = "this title already exist for this tricount";
            }
        }
        return $errors;
    }
    

    public function delete_template(){
        $userlogged = $this->get_user_or_redirect();
        $user = User::get_by_id($userlogged->getUserId());
        if($user->is_in_tricount($_GET['param1'] || $user->is_in_items($_GET['param1'])  )){
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