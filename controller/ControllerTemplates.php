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
        $user = $this->get_user_or_redirect();
        $user = User::get_by_id($user->id);
        $templates = Repartition_template_items::get_by_user($user->id);
        if (isset($_GET['param1']) && !is_numeric($_GET['param1'])) {
            $this->redirect('main', "error");
        }else{
            $tricount = Tricounts::get_by_id($_GET['param1']);
            $participant = Participations::get_by_tricount_and_creator($tricount->id);
        }
        (new View("templates"))->show(array("user"=>$user,"templates"=>$templates, "tricount"=>$tricount, "participant"=>$participant ));
    }
}
?>