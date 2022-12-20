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
        $user = User::get_by_id($userlogged->id);
        if (isset($_GET['param1']) && !is_numeric($_GET['param1'])) {
            $this->redirect('main', "error");
        }else{
            $tricount = Tricounts::get_by_id($_GET["param1"]);
            $templates = Repartition_templates::get_by_tricount($_GET["param1"]);
            foreach($templates as $template){
                $items[] = $template->get_items();
                
            }
        }
        (new View("templates"))->show(array("user"=>$user,
                                            "templates"=>$templates, 
                                            "tricount"=>$tricount, 
                                            "items"=>$items));
    }


}
?>