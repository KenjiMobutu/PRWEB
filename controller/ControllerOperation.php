<?php
require_once 'model/User.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';
require_once 'model/tricounts.php';
require_once 'model/participations.php';
require_once 'model/repartitions.php';

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

    public function detail_expense(){
        $user = $this->get_user_or_redirect();
        $user = User::get_by_id($user->getUserId());
        if (isset($_GET['param1']) && !is_numeric($_GET['param1'])) {
            $this->redirect('main', "error");
        }else{
        $userId = $user->getUserId();
        $operationId = $_GET['param1'];
        // $t=Operation::get_tricount_by_operation_id($operationId);
        $tricount = Tricounts::get_tricount_by_operation_id($operationId);
        $participants = Operation::getNumberParticipantsByOperationId($operationId);
        $operation_data=Operation::getOperationByOperationId($operationId);
        $usr = $operation_data->getInitiator();
            // echo '<pre>';
            // var_dump($usr);
            // echo '</pre>';
            // die();
        }

        (new View("detail_expense"))->show(array("user"=>$user, "operation_data"=>$operation_data, "participants" => $participants,"tricount"=>$tricount, "usr" => $usr ));

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
                    $initiator = $_POST["initiator"];
                    $created_at = date('y-m-d h:i:s');

                  
                    // echo '<pre>';
                    // print_r($_POST["initiator"]);
                    // echo '</pre>';
                    // die();
                    

                    if($user){
                        $operation = new Operation($title,$tricount,$amount,$operation_date,$initiator,$created_at);
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

    public function edit(){
        $user = $this->get_user_or_redirect();
        $user = User::get_by_id($user->getUserId());
        if (isset($_GET['param1']) && !is_numeric($_GET['param1'])) {
            $this->redirect('main', "error");
        }else{
        $userId = $user->getUserId();
        $tricount = Tricounts::get_tricount_by_operation_id($_GET['param1']);
        // $tricountID = $tricount->id;
        $operationId = $_GET['param1'];
        $operation_data=Operation::getOperationByOperationId($operationId);
        $usr = $operation_data->getInitiator();
        $users = User::getUsers();
        $rti = Repartition_template_items::get_by_user($userId);
        // echo '<pre>';
        // print_r($rti);
        // echo '</pre>';
        // die();
        
        }

        (new View("edit_expense"))->show(array("user"=>$user, "operation_data"=>$operation_data, "users"=>$users,"rti"=>$rti, "tricount"=>$tricount, "usr" => $usr ));

    }

    public function edit_expense(){
        $user = $this->get_user_or_redirect();
        $errors     = [];
        if (isset($_GET['param1']) && !is_numeric($_GET['param1'])) {
            $this->redirect('main', "error");
        }else{
            $userId = $user->getUserId();

            if ($_SERVER['REQUEST_METHOD'] === 'POST'){

                // echo '<pre>';
                //     print_r($_POST);
                //     echo '</pre>';
                //     die();
                if(
                    array_key_exists("operationId",$_POST) &&
                    array_key_exists("title",$_POST) &&
                    array_key_exists("tricId",$_POST) &&
                    array_key_exists("amount",$_POST) &&
                    array_key_exists("operation_date",$_POST) &&
                    array_key_exists("initiator",$_POST)
                ){

                    $operation = Operation::getOperationByOperationId($_POST["operationId"]);
                    
                    if($operation !== null){

                        $title=$_POST["title"];
                        $tricount = $_POST["tricId"];
                        $amount = floatval($_POST["amount"]);
                        $operation_date = $_POST["operation_date"];
                        $init = User::get_by_name($_POST["initiator"]);
                        $initiator = $init->getUserId();
                        $created_at = date('y-m-d h:i:s');

                        if($title){
                            $operation->setTitle($title);
                        }
                        if($tricount){
                            $operation->setTricount($tricount);
                        }
                        if($amount){
                            $operation->setAmount($amount);
                        }
                        if($operation_date){
                            $operation->setOperation_date($operation_date);
                        }
                        if($initiator){
                            $operation->setInitiator($initiator);
                        }
                        if($created_at){
                            $operation->setCreated_at($created_at);
                        }
                    }
                  
                    // echo '<pre>';
                    // print_r($operation);
                    // echo '</pre>';
                    // die();
                  
                    $errors=$operation->validate();

                    if(empty($errors)){
                        $operation->update();
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
