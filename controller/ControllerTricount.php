<?php
require_once 'model/Repartition_templates.php';
require_once 'model/Repartition_template_items.php';
require_once 'model/User.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';

class ControllerTricount extends Controller{
  public function index() :void{
        if (isset($_GET["param1"])) {
            $this->redirect('list_tricounts');
        }
  }

}

?>
