<?php
require_once 'model/User.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';
require_once 'model/tricounts.php';
require_once 'model/participations.php';
require_once 'model/repartitions.php';
require_once 'model/Repartition_templates.php';
require_once 'model/Repartition_template_items.php';

class ControllerOperation extends Controller
{

    public function index(): void
    {
        if (isset($_GET["param1"])) {
            $this->redirect('expenses');
        }
    }

    public function expenses()
    {
        $user = $this->get_user_or_redirect();
        $user = User::get_by_id($user->getUserId());
        if (isset($_GET['param1']) && !is_numeric($_GET['param1'])) {
            $this->redirect('main', "error");
        } else {
            $userId = $user->getUserId();
            $checkTricount = Tricounts::exists($_GET['param1']);
            if ($checkTricount <= 0) {
                $this->redirect('main', "error");
            }
            $tricount = Tricounts::get_by_id($_GET['param1']);
            $tricountID = $tricount->get_id();
            
            $tricountParticipants = Operation::getUsersFromTricount($tricountID);
            // if(!in_array($userId,$tricountParticipants)){ //TODO y a que boris qui peut les voir idk
            //     $this->redirect('main', "error");    //si l'user ne participe pas dans un tric il peux pas voir les operations
            // }
            $operations_of_tricount = Operation::get_operations_by_tricount($tricountID);
            $participants = Tricounts::number_of_friends($tricountID);
            $amounts[] = Operation::get_operations_by_tricount($tricountID);
            $nbOperations = Operation::getNbOfOperations($tricountID);
            $totalExp = Tricounts::get_total_amount_by_tric_id($tricountID);
            $mytot = Tricounts::get_my_total($userId);
        }
        (new View("expenses"))->show(array("operations_of_tricount" => $operations_of_tricount, "user" => $user, "tricount" => $tricount, "amounts" => $amounts, "totalExp" => $totalExp, "mytot" => $mytot, "participants" => $participants, "nbOperations" => $nbOperations));
    }

    public function balance()
    {
        $user = $this->get_user_or_redirect();
        $user = User::get_by_id($user->getUserId());
        if (isset($_GET['param1']) && !is_numeric($_GET['param1'])) {
            $this->redirect('main', "error");
        }
        $checkTricount = Tricounts::exists($_GET['param1']);
        if ($checkTricount <= 0) {
            $this->redirect('main', "error");
        } else {

            $tricount = Tricounts::get_by_id($_GET['param1']);
            $tricountID = $tricount->get_id();
            $users = Participations::get_by_tricount($tricountID);
            $operations_of_tricount = Operation::get_operations_by_tricount($tricountID);
            $weights = Repartitions::get_user_and_weight_by_operation_id($tricount->get_id());
            $total = Tricounts::get_total_amount_by_tric_id($tricountID);
        }
        (new View("tricount_balance"))->show(array("total" => $total, "users" => $users, "operations_of_tricount" => $operations_of_tricount, "user" => $user, "tricount" => $tricount, "weights" => $weights));
    }

    public function detail_expense()
    {
        $user = $this->get_user_or_redirect();
        $user = User::get_by_id($user->getUserId());
        if (isset($_GET['param1']) && !is_numeric($_GET['param1'])) {
            $this->redirect('main', "error");
        }
        $checkId = Operation::exists($_GET['param1']); //check si l'operation existe dans le tricount
        if (empty($checkId)) {
            $this->redirect('main', "error");
        } else {
            $userId = $user->getUserId();
            $operationId = $_GET['param1'];
            $tricount = Tricounts::get_tricount_by_operation_id($operationId);
            $operationUsers = Operation::get_users_from_operation($operationId);
            $debt = Operation::get_dette_by_operation($operationId, $userId);
            $participants = Operation::getNumberParticipantsByOperationId($operationId);
            $operation_data = Operation::getOperationByOperationId($operationId);
            $usr = $operation_data->getInitiator();
        }

        (new View("detail_expense"))->show(array("user" => $user, "operationUsers" => $operationUsers, "debt" => $debt, "operation_data" => $operation_data, "participants" => $participants, "tricount" => $tricount, "usr" => $usr));

    }

    public function add()
    {
        $user = $this->get_user_or_redirect();
        $user = User::get_by_id($user->getUserId());
        if (isset($_GET['param1']) && !is_numeric($_GET['param1'])) {
            $this->redirect('main', "error");
        } else {
            $userId = $user->getUserId();
            $tricount = Tricounts::get_by_id($_GET['param1']);
            $users = Participations::get_by_tricount($_GET['param1']);
            $rti = Repartition_template_items::get_by_user($userId);
        }

        (new View("add_expense"))->show(array("user" => $user, "tricount" => $tricount, "rti" => $rti, "users" => $users));

    }

    public function add_expense()
    {
        $user = $this->get_user_or_redirect();
        if (isset($_GET['param1']) && !is_numeric($_GET['param1'])) {
            $this->redirect('main', "error");
        }

        if (isset($_POST["refreshBtn"])) {
            if (
                array_key_exists("title",
                    $_POST) &&
                array_key_exists("tricId",
                    $_POST) &&
                array_key_exists("amount",
                    $_POST) &&
                array_key_exists("operation_date",
                    $_POST) &&
                array_key_exists("initiator",
                    $_POST) &&
                array_key_exists("rti",
                    $_POST)
            ) {
                $userId = $user->getUserId();
                $title = $_POST["title"];
                $tricId = $_POST["tricId"];
                $tricount = Tricounts::get_by_id($tricId);
                $amount = floatval($_POST["amount"]);
                $operation_date = $_POST["operation_date"];
                $initiator = $_POST["initiator"];
                $users = User::getUsers();
                $init = User::get_by_id($initiator);
                $rti = Repartition_template_items::get_by_user($userId);
                $template = Repartition_templates::get_by_id($_POST['rti']);
                // var_dump($template); die();
                if ($template === null) {
                    $this->redirect("operation", "expenses/" . $tricount->get_id());
                }
                $ListUsers = Participations::get_by_tricount($tricId);
                $listItems = Repartition_template_items::get_user_by_repartition($template->get_id());
            }
            (new View("add_expense"))->show(
                array(
                    "user" => $user,
                    "title" => $title,
                    "amount" => $amount,
                    "operation_date" => $operation_date,
                    "init" => $init,
                    "rti" => $rti,
                    "users" => $users,
                    "tricount" => $tricount,
                    "template" => $template,
                    "ListUsers" => $ListUsers,
                    "listItems" => $listItems
                )
            );
        } else {
            $userId = $user->getUserId();
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST["save_template"])) {
                if (
                    array_key_exists("title",
                        $_POST) &&
                    array_key_exists("tricId",
                        $_POST) &&
                    array_key_exists("amount",
                        $_POST) &&
                    array_key_exists("operation_date",
                        $_POST) &&
                    array_key_exists("initiator",
                        $_POST)
                ) {

                    $title = $_POST["title"];
                    $tricount = $_POST["tricId"];
                    $amount = floatval($_POST["amount"]);
                    $operation_date = $_POST["operation_date"];
                    $initiator = $_POST["initiator"];
                    $created_at = date('y-m-d h:i:s');

                    if ($user) {
                        $operation = new Operation($title, $tricount, $amount, $operation_date, $initiator, $created_at);
                    }

                    $errors = $operation->validate();

                    if (empty($errors)) {
                        $operation->insert();
                        $this->redirect("operation", "expenses", $_POST["tricId"]);
                    } else {
                        echo "<b>Validation Failed:<b> <br>";
                        foreach ($errors as $error) {
                            echo $error . "<br>";
                        }
                    }
                }
            } else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["c"]) && isset($_POST["w"])) {
                if (
                    array_key_exists("title",
                        $_POST) &&
                    array_key_exists("tricId",
                        $_POST) &&
                    array_key_exists("amount",
                        $_POST) &&
                    array_key_exists("operation_date",
                        $_POST) &&
                    array_key_exists("initiator",
                        $_POST)
                ) {

                    $title = $_POST["title"];
                    $tricount = $_POST["tricId"];
                    $amount = floatval($_POST["amount"]);
                    $operation_date = $_POST["operation_date"];
                    $initiator = $_POST["initiator"];
                    $created_at = date('y-m-d h:i:s');
                    $checkedUsers = $_POST["c"];
                    $weights = $_POST["w"];

                    if ($user) {
                        $operation = new Operation($title, $tricount, $amount, $operation_date, $initiator, $created_at);
                        $template = new Repartition_templates(null, $_POST["template_name"], $_POST["tricId"]);
                    }

                    $errors = $operation->validate();

                    if (empty($errors)) {
                        $operation->insert();
                        $template->newTemplate($_POST["template_name"], $_POST["tricId"]);
                        if ($template !== null) {
                            for ($i = 0; $i <= count($checkedUsers) + 1; $i++) {
                                if (isset($checkedUsers[$i]) && $checkedUsers[$i] !== null) {
                                    if ($weights[$i] === "" || $weights[$i] === "0")
                                        $weights[$i] = 1;
                                    Repartition_template_items::addNewItems(
                                        $checkedUsers[$i],
                                        $template->id,
                                        $weights[$i]
                                    );
                                    Operation::insertRepartition($operation->get_id(), $weights[$i], $checkedUsers[$i]);
                                } //alberti goat

                            }
                            $this->redirect("operation", "expenses", $_POST["tricId"]);
                        }


                    } else {
                        echo "<b>Validation Failed:<b> <br>";
                        foreach ($errors as $error) {
                            echo $error . "<br>";
                        }
                    }
                }
            }
        }
    }

    public function edit()
    {
        $user = $this->get_user_or_redirect();
        $user = User::get_by_id($user->getUserId());
        $checkOperation = Operation::exists($_GET['param1']);
        if (isset($_GET['param1']) && !is_numeric($_GET['param1'])) {
            $this->redirect('main', "error");
        }
        if ($checkOperation <= 0) {
            $this->redirect('main', "error");
        } else {
            $userId = $user->getUserId();
            $tricount = Tricounts::get_tricount_by_operation_id($_GET['param1']);
            $operationId = $_GET['param1'];
            $operation_data = Operation::getOperationByOperationId($operationId);
            $usr = $operation_data->getInitiator();
            $users = User::getUsers();
            $rti = Repartition_template_items::get_by_user($userId);
            // echo '<pre>';
            // print_r($rti);
            // echo '</pre>';
            // die();

        }

        (new View("edit_expense"))->show(array("user" => $user, "operation_data" => $operation_data, "users" => $users, "rti" => $rti, "tricount" => $tricount, "usr" => $usr));

    }

    public function edit_expense()
    {
        $user = $this->get_user_or_redirect();
        $errors = [];
        if (isset($_GET['param1']) && !is_numeric($_GET['param1'])) {
            $this->redirect('main', "error");
        } else {
            $userId = $user->getUserId();

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (
                    array_key_exists("operationId",
                        $_POST) &&
                    array_key_exists("title",
                        $_POST) &&
                    array_key_exists("tricId",
                        $_POST) &&
                    array_key_exists("amount",
                        $_POST) &&
                    array_key_exists("operation_date",
                        $_POST) &&
                    array_key_exists("initiator",
                        $_POST)
                ) {

                    $operation = Operation::getOperationByOperationId($_POST["operationId"]);

                    if ($operation !== null) {

                        $title = $_POST["title"];
                        $tricount = $_POST["tricId"];
                        $amount = floatval($_POST["amount"]);
                        $operation_date = $_POST["operation_date"];
                        $init = User::get_by_name($_POST["initiator"]);
                        $initiator = $init->getUserId();
                        $created_at = date('y-m-d h:i:s');

                        if ($title) {
                            $operation->setTitle($title);
                        }
                        if ($tricount) {
                            $operation->setTricount($tricount);
                        }
                        if ($amount) {
                            $operation->setAmount($amount);
                        }
                        if ($operation_date) {
                            $operation->setOperation_date($operation_date);
                        }
                        if ($initiator) {
                            $operation->setInitiator($initiator);
                        }
                        if ($created_at) {
                            $operation->setCreated_at($created_at);
                        }
                    }
                    $errors = $operation->validate();

                    if (empty($errors)) {
                        $operation->update();
                        $this->redirect("operation", "expenses", $_POST["tricId"]);
                    } else {
                        echo "<b>Validation Failed:<b> <br>";
                        foreach ($errors as $error) {
                            echo $error . "<br>";
                        }
                    }
                }
            }
        }
    }

    public function delete_confirm()
    {
        $user = $this->get_user_or_redirect();
        $errors = [];
        $operationId = $_GET['param1'];
        $checkOperation = Operation::exists($_GET['param1']);
        if ($checkOperation <= 0) {
            $this->redirect('main', "error");
        }
        $operation_data = Operation::getOperationByOperationId($operationId);
        if (isset($_GET['param1']) && !is_numeric($_GET['param1'])) {
            $this->redirect('main', "error");
        } else {
            $userId = $user->getUserId();
        }
        if (isset($_GET["param1"])) {

            (new View("delete_operation"))->show(array("user" => $user, "operationId" => $operationId, "operation_data" => $operation_data));
        }
    }

    public function delete_operation()
    {
        $user = $this->get_user_or_redirect();
        $errors = [];
        $operationId = $_GET['param1'];
        if (isset($_GET['param1']) && !is_numeric($_GET['param1'])) {
            $this->redirect('main', "error");
        } else {
            $userId = $user->getUserId();
            $operation = Operation::getOperationByOperationId($operationId);
            if ($operation === null) {
                $this->redirect("main", "error");
            }
        }
        if (isset($_POST['submitted'])) {
            if ($_POST['submitted'] === "Cancel") {
                $this->redirect("operation", "edit_expense", $operationId);
            } else if ($_POST['submitted'] === "Delete") {
                $tricount = Tricounts::get_tricount_by_operation_id($operationId);
                $tricountId = $tricount->get_id();
                $operation = $operation->delete();
                $this->redirect("operation", "expenses", $tricountId);
            }
        }

        (new View("edit_expense"))->show(array("user" => $user, "tricount" => $tricount));

    }


    public function next_expense()
    {
        if (isset($_POST["tricount_id"]) && isset($_POST["operation"])) {
            $idTricount = $_POST["tricount_id"];
            $tricount = Tricounts::get_by_id($idTricount);
            $idOperation = $_POST["operation"];
            $operation = Operation::get_by_id($idOperation);
            if ($_POST["submit"] === "Next")
                $nextOperation = $operation->get_next_operation_by_tricount($idOperation, $tricount->get_id());
            else if ($_POST["submit"] === "Previous") {
                $nextOperation = $operation->get_previous_operation_by_tricount($idOperation, $tricount->get_id());
            }
            if ($nextOperation) {
                $this->redirect("operation", "detail_expense", $nextOperation->get_id());
            } else {
                $this->redirect("operation", "detail_expense", $_POST["operation"]);
            }
        }
    }
// /**      idée de fonction si on doit mettre une fonction a part pour previous_experience
//  * public function previous_experience(){
//     if(isset($_POST["tricount_id"])&& isset($_POST["operation"]) ){
//         $idTricount = $_POST["tricount_id"];
//         $tricount = Tricounts::get_by_id($idTricount);
//         $idOperation = $_POST["operation"];
//         $operation = Operation::get_by_id($idOperation);


//         $prevOperation = $operation->get_prev_operation_by_tricount($idOperation,$tricount->get_id());
//         if($prevOperation){
//             $this->redirect("operation", "detail_expense", $prevOperation->get_id());
//         }
//         else{
//             $this->redirect("operation", "detail_expense", $_POST["operation"]);
//         }
//     }
// }
// */
}

?>