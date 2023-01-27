<?php 
require_once 'model/User.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';
require_once 'model/tricounts.php';
require_once 'model/participations.php';
require_once 'model/repartitions.php';
require_once 'model/operation.php';
require_once 'model/Repartition_templates.php';
require_once 'model/Repartition_template_items.php';

class ControllerOperation extends Controller{

    public function index(): void
    {
        if(isset($_GET["param1"])){
            $this->redirect('expenses');
        }
    }
    
    public function expenses(){
        $user = $this->get_user_or_redirect();
        $user = User::get_by_id($user->getUserId());
        if (isset($_GET['param1']) && !is_numeric($_GET['param1'])) {
            $this->redirect('main', "error");
        }else{
        $userId = $user->getUserId();
        $tricount = Tricounts::get_by_id($_GET['param1']);

        $tricountID = $tricount->get_id();
        $amounts[] = Operation::get_operations_by_tricount($tricountID);
        $totalExp = Tricounts::get_total_amount_by_tric_id($tricountID);
        $mytot = Tricounts::get_my_total($userId);
            // echo '<pre>';
            // print_r($amounts);
            // echo '</pre>';
            // die();
        }
        (new View("expenses"))->show(array("user"=>$user, "tricount"=>$tricount, "amounts"=>$amounts,"totalExp"=>$totalExp,"mytot"=>$mytot ));
    }

    public function balance(){
        $user = $this->get_user_or_redirect();
        $user = User::get_by_id($user->getUserId());
        if (isset($_GET['param1']) && !is_numeric($_GET['param1'])) {
            $this->redirect('main', "error");
        }else{
        
        $operation = Operation::getOperationId($_GET['param1']);
        
        $tricount = Tricounts::get_by_id($_GET['param1']);
        // echo '<pre>';
        //     print_r($tricount->get_id());
        //     echo '</pre>';
        //     die();
        $tricountID = $tricount->get_id();
        $weights = Repartitions::get_user_and_weight_by_operation_id($tricount->get_id());
        $total = Tricounts::get_total_amount_by_tric_id($tricountID);
        // $debt = ;
            // echo '<pre>';
            // print_r($total);
            // echo '</pre>';
            // die();
        }
        (new View("tricount_balance"))->show(array("user"=>$user, "tricount"=>$tricount, "weights"=>$weights));
    }

    public function add(){
        $user = $this->get_user_or_redirect();
        $user = User::get_by_id($user->getUserId());
        if (isset($_GET['param1']) && !is_numeric($_GET['param1'])) {
            $this->redirect('main', "error");
        }else{
        $userId = $user->getUserId();
        $users = User::getUsers();

        $rti = Repartition_template_items::get_by_user($userId);
            // echo '<pre>';
            // print_r($user);
            // echo '</pre>';
            // die();
        $tricount = Tricounts::get_by_id($_GET['param1']);
        }
        
        (new View("add_expense"))->show(array("user"=>$user, "tricount"=>$tricount, "rti"=>$rti,"users"=>$users ));
        
    }

    public function add_expense(){
        $user = $this->get_user_or_redirect();
        if (isset($_GET['param1']) && !is_numeric($_GET['param1'])) {
            $this->redirect('main', "error");
        }else{
            $userId = $user->getUserId();

            if ($_SERVER['REQUEST_METHOD'] === 'POST'){
                if(
                    array_key_exists("title",$_POST) &&
                    array_key_exists("tricId",$_POST) &&
                    array_key_exists("amount",$_POST) &&
                    array_key_exists("operation_date",$_POST) &&
                    array_key_exists("initiator",$_POST)
                ){
                    
                    $title=$_POST["title"];
                    $tricount = $_POST["tricId"];
                    $amount = floatval($_POST["amount"]);
                    $operation_date = $_POST["operation_date"];
                    $initiator = User::get_by_id($userId);
                    $created_at = date('d-m-y h:i:s');

                    if($user){
                        $operation = new Operation($title,$tricount,$amount,$operation_date,$initiator->getUserId(),$created_at);
                    }
                    
                    $errors=$operation->validate();

                    if(empty($errors)){
                        $operation->insert();
                        $this->redirect("operation", "expenses", $_POST["tricId"]);
                    }else{
                        echo "<b>Validation Failed:<b> <br>";
                        foreach($errors as $error) {
                            echo $error . "<br>";
                        }
                    }
                 } 
                }
            }
    }


}

?>