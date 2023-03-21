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
            $this->redirect("operation", 'expenses', $_GET['param1']);
        }
    }

    public function expenses()
    {
        $user = $this->get_user_or_redirect();
        $user = User::get_by_id($user->getUserId());
        // var_dump($_POST); die();
        if (isset($_GET['param1']) && !is_numeric($_GET['param1'])) {
            $this->redirect('main', "error");
        } else {
            //$userId = $user->getUserId();
            $checkTricount = Tricounts::exists($_GET['param1']);
            if (!is_null($checkTricount)) {
            }
            if ($checkTricount <= 0) {
                $this->redirect('main', "error", "exp2");
            }
            if ($user->is_in_tricount($_GET['param1'])) {
                $tricount = Tricounts::get_by_id($_GET['param1']);
                $amounts[] = Operation::get_operations_by_tricount($tricount->get_id());
                $totalExp = Tricounts::get_total_amount_by_tric_id($tricount->get_id());
                $participants = Tricounts::number_of_friends($tricount->get_id());
            } else {
                $this->redirect('main', "error", "nononono");
            }
        }
        (new View("expenses"))->show(
            array(
                "user" => $user,
                "tricount" => $tricount,
                "amounts" => $amounts,
                "totalExp" => $totalExp,
                "participants" => $participants
            )
        );
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
            $users = Participations::get_by_tricount($tricount->get_id());
            $operations_of_tricount = Operation::get_operations_by_tricount($tricount->get_id());
            if (is_null($operations_of_tricount)) {
                $this->redirect('operation', "index", $tricount->get_id());
            }
            //var_dump($tricount->get_id()); die();
            $weights = Repartitions::get_by_operation($tricount->get_id());
            $total = Tricounts::get_total_amount_by_tric_id($tricount->get_id());
        }
        (new View("tricount_balance"))->show(
            array(
                "total" => $total,
                "users" => $users,
                "operations_of_tricount" => $operations_of_tricount,
                "user" => $user,
                "tricount" => $tricount,
                "weights" => $weights
            )
        );
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

        (new View("detail_expense"))->show(
            array(
                "user" => $user,
                "operationUsers" => $operationUsers,
                "debt" => $debt,
                "operation_data" => $operation_data,
                "participants" => $participants,
                "tricount" => $tricount,
                "usr" => $usr
            )
        );

    }

    public function refreshBtnHandler($user)
    {
        $errors = [];

        // var_dump($_GET); echo"<----------- GET \n";
        // var_dump($_POST);die();

        if (isset($_POST["addrefreshBtn"]) && $_POST['rti'] !== 'option-default') {
            $requiredFields = ["title", "tricId", "amount", "operation_date", "initiator", "rti"];
            $allFieldsExist = true;
            foreach ($requiredFields as $field) {
                if (!array_key_exists($field, $_POST)) {
                    $allFieldsExist = false;
                    break;
                }
            }
            if ($allFieldsExist) {
                $action = $_GET['action'];
                $userId = $user->getUserId();
                $title = Tools::sanitize($_POST["title"]);
                $tricId = $_POST["tricId"];
                $tricount = Tricounts::get_by_id($tricId);
                $amount = Tools::sanitize(floatval($_POST["amount"]));
                $operation_date = $_POST["operation_date"];
                $initiator = $_POST["initiator"];
                $users = Participations::get_by_tricount($tricId);
                $init = User::get_by_id($initiator);
                $rti = Repartition_template_items::get_by_user_and_tricount($userId, $tricId);
                $template = Repartition_templates::get_by_id($_POST['rti']);
                
                isset($_GET['param1']) ? $repartitions = Repartitions::get_by_operation($_GET['param1']) : null;
                if(isset($_POST['repartitions'])){
                    $repartitions = Repartitions::get_by_operation($_POST['rti']);
                }
                //$repartitions = Repartitions::get_by_operation($_GET['param1'] ? $_GET['param1'] : null);
                $templateId = $template->get_id();
                
                // var_dump($templateId);
                // var_dump($templateId);
                if ($template === null) {
                    $this->redirect("operation", "expenses/" . $tricount->get_id());
                }
                isset($_GET['param1']) ? $operationId = $_GET['param1'] :  null;
                if(isset($_POST["operationId"]))
                    $operationId = $_POST["operationId"];
                $ListUsers = Participations::get_by_tricount($tricId);
                $listItems = Repartition_template_items::get_user_by_repartition($template->get_id());
                $operation = new Operation($title, $tricId, $amount, $operation_date, $initiator, $operation_date, $operationId);
                $errors = $operation->validateForEdit();
                //var_dump($repartitions);
                //var_dump($repartitions);

                if (count($errors) === 0) {
                    (new View("add_expense"))->show(
                        array(
                            "user" => $user,
                            "operation" => $operation,
                            "rti" => $rti,
                            "templateId" => $templateId,
                            "users" => $users,
                            "tricount" => $tricount,
                            "template" => $template,
                            "ListUsers" => $ListUsers,
                            "listItems" => $listItems,
                            "repartitions" =>$repartitions,
                            "errors" => $errors,
                            "action" => $action,
                            "operationId"=> $operationId
                        )
                    );

                } else {
                    // show errors
                    (new View("add_expense"))->show(
                        array(
                            "user" => $user,
                            "operation" => $operation,
                            "title" => $title,
                            "amount" => $amount,
                            "operation_date" => $operation_date,
                            "init" => $init,
                            "rti" => $rti,
                            "templateId" => $templateId,
                            "users" => $users,
                            "tricount" => $tricount,
                            "template" => $template,
                            "ListUsers" => $ListUsers,
                            "listItems" => $listItems,
                            "errors" => $errors,
                            "action" => $action
                        )
                    );
                    
                }
            }
            
        }
        
    }


    public function SaveWithTemplateExistant($user)
    {
        $action = $_GET['action'];
        $title = $_POST["title"];
        $tricountId = $_POST["tricId"];
        $amount = $_POST["amount"];
        $operation_date = $_POST["operation_date"];
        $initiator = $_POST["initiator"];
        $created_at = date('y-m-d h:i:s');
        $errors = [];

        //$operation = new Operation($title, $tricountId, $amount, $operation_date, $initiator, $created_at);
        if (!$title || !$tricountId || !$amount || !$operation_date || !$initiator) {
            // Handle missing fields error
            return;
        }

        $title = Tools::sanitize($title);
        $tricount = Tricounts::get_by_id($tricountId);
        $amount = Tools::sanitize(floatval($amount));
        $init = User::get_by_id($initiator);
        $users = Participations::get_by_tricount($tricountId);
        $rti = Repartition_template_items::get_by_user_and_tricount($initiator, $tricountId);
        $template = Repartition_templates::get_by_id($_POST['rti']);
        
        //$ListUsers = Participations::get_by_tricount($tricountId);

        if (!$tricount || !$init) {
            $this->redirect("main", "error", "save");
            return;
        }

        $operation = new Operation($title, $tricountId, $amount, $operation_date, $initiator, $created_at);
        $errors = $operation->validate();
        //$errors = $operation->validateTitle($title);
        $checkedUsers = $_POST["c"];
        $weights = $_POST["w"];
        if (empty($errors)) {
            $operation->insert();
            if ($template !== null) {
                Repartition_template_items::delete_by_repartition_template($template->get_id());
                for ($i = 0; $i <= count($checkedUsers) + 50; $i++) {
                    if (isset($checkedUsers[$i]) && $checkedUsers[$i] !== null) {
                        if ($weights[$i] === "" || $weights[$i] === "0") {
                            $weights[$i] = 1;
                        }
                        Repartition_template_items::addNewItems($checkedUsers[$i], $template->get_id(), $weights[$i]);
                        Operation::insertRepartition($operation->get_id(), $weights[$i], $checkedUsers[$i]);
                    }
                }
                $this->redirect("operation", "expenses", $_POST["tricId"]);
            }
            $this->redirect("operation", "expenses", $tricountId);
        } else
            (new View("add_expense"))->show(
                array(
                    "user" => $user,
                    "operation" => $operation,
                    "rti" => $rti,
                    "users" => $users,
                    "tricount" => $tricount,
                    "template" => $template,
                    "errors" => $errors,
                    "action" => $action
                )
            );
    }

    public function saveWithCustomTemplate()
    {
        $action = $_GET['action'];
        $title = $_POST["title"];
        $tricountId = $_POST["tricId"];
        $amount = $_POST["amount"];
        $operation_date = $_POST["operation_date"];
        $initiator = $_POST["initiator"];
        $template_name = $_POST["template_name"];
        $checkedUsers = $_POST["c"];
        $weights = $_POST["w"];
        $errors = [];
        if ($title && $tricountId && $amount && $operation_date && $initiator && $template_name && $checkedUsers && $weights) {
            $title = Tools::sanitize($_POST["title"]);
            $tricount = Tricounts::get_by_id($tricountId);
            $amount = Tools::sanitize(floatval($_POST["amount"]));
            $operation_date = $_POST["operation_date"];
            $initiator = $_POST["initiator"];
            $created_at = date('y-m-d h:i:s');
            $users = Participations::get_by_tricount($tricountId);
            $init = User::get_by_id($initiator);
            $template_name = Tools::sanitize($_POST["template_name"]);

            $operation = new Operation($title, $tricountId, $amount, $operation_date, $initiator, $created_at);
            $template = new Repartition_templates(null, $template_name, $_POST["tricId"]);

            $errors = $operation->validate();
            //$errors = $operation->validateTitle($title);
            if (empty($errors)) {
                $operation->insert();
                $template->newTemplate($template_name, $_POST["tricId"]);

                if ($template !== null) {
                    for ($i = 0; $i <= count($checkedUsers) + 50; $i++) {
                        if (isset($checkedUsers[$i]) && $checkedUsers[$i] !== null) {
                            if ($weights[$i] === "" || $weights[$i] === "0") {
                                $weights[$i] = 1;
                            }
                            Repartition_template_items::addNewItems($checkedUsers[$i], $template->get_id(), $weights[$i]);
                            Operation::insertRepartition($operation->get_id(), $weights[$i], $checkedUsers[$i]);
                        }
                    }
                    $this->redirect("operation", "expenses", $_POST["tricId"]);
                }
            } else
                (new View("add_expense"))->show(
                    array(
                        //print_r($tricount),
                        "title" => $title,
                        "amount" => $amount,
                        "operation_date" => $operation_date,
                        "init" => $init,
                        "users" => $users,
                        "tricount" => $tricount,
                        "template" => $template,
                        "errors" => $errors,
                        "action" => $action
                    )
                );
        }
    }


    public function add()
    {
        $user = $this->get_user_or_redirect();
        $user = User::get_by_id($user->getUserId());
        if (isset($_GET['param1']) && !is_numeric($_GET['param1'])) {
            $this->redirect('main', "error", "add");
        } else {
            $userId = $user->getUserId();
            $tricount = Tricounts::get_by_id($_GET['param1']);
            $users = Participations::get_by_tricount($_GET['param1']);
            $rti = Repartition_template_items::get_by_user_and_tricount($userId, $_GET['param1']);
            $action = $_GET['action'];
        }
        (new View("add_expense"))->show(
            array(
                "user" => $user,
                "tricount" => $tricount,
                "rti" => $rti,
                "users" => $users,
                "action" => $action
            )
        );

    }
    //TODO garder les weights en cas d'erreur ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    public function add_expense()
    {
        $user = $this->get_user_or_redirect();
        if (isset($_GET['param1']) && !is_numeric($_GET['param1'])) {

            $this->redirect('main', "error", "add2");
        }

        //print_r($_POST);
        //if i choose a template from the templates list
        if (isset($_POST["addrefreshBtn"])) {
            $this->refreshBtnHandler($user);
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST["save_template"])) {
            //print_r($_POST);
            $this->SaveWithTemplateExistant($user);
            //if i make a custom template => need save name checked
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["c"]) && isset($_POST["w"])) {
            $this->saveWithCustomTemplate();
        }
    }

    public function edit()
    {
        $user = $this->get_user_or_redirect();
        $user = User::get_by_id($user->getUserId());
        $checkOperation = Operation::exists($_GET['param1']);
        if (isset($_GET['param1']) && !is_numeric($_GET['param1'])) {
            $this->redirect('main', "error","aici1");
        }
        if ($checkOperation <= 0) {
            $this->redirect('main', "error","aici2");
        } else 
        {
            $action = $_GET['action'];
            $userId = $user->getUserId();
            $tricount = Tricounts::get_tricount_by_operation_id($_GET['param1']);
            $operationId = $_GET['param1'];
            $operation = Operation::getOperationByOperationId($operationId);
            $users = Participations::get_by_tricount($tricount->get_id());
            $repartitions = Repartitions::get_by_operation($operationId);
            $rti = Repartition_template_items::get_by_user_and_tricount($userId, $tricount->get_id());
            
        }
        
        (new View("add_expense"))->show(
            array(
                "user" => $user,
                "action" => $action,
                "operation" => $operation,
                "users" => $users,
                "rti" => $rti,
                "tricount" => $tricount,
                "repartitions" => $repartitions,
                "operationId" => $operationId
            )
            
        );

    }

    public function edit_expense()
    {
        //var_dump($_POST); die();
        $user = $this->get_user_or_redirect();
        $action = $_GET['action'];
        $operationId = $_POST['operationId'];
        var_dump($operationId);
        // die();
        if (isset($_GET['param1']) && !is_numeric($_GET['param1'])) {
            $this->redirect('main', 'error','aici3');
        }
        //$operationId = $_POST['operationId'];
        
        //$operation = Operation::getOperationByOperationId($operationId);
        // var_dump($operation);
        // if (!$operation) {
        //     $this->redirect('main', 'error','aici4');
        // }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        if (isset($_POST['save_template'])) {
            $save_template = $_POST['save_template'] === 'on';
        } else {
            $save_template = false;
        }

        if (isset($_POST["addrefreshBtn"])) {
            $this->refreshBtnHandler($user);
            
        }
        // var_dump($_POST); die();
        // if ($save_template && empty($_POST['name_template'])) {
        //     $this->redirect('main', 'error', 'title_cannot_be_empty');
        // }
        $title = Tools::sanitize($_POST['title']);
        // var_dump($_GET); die();
        // $tricount = Tricounts::get_by_id($_POST['tricId']);
        $operation = Operation::get_by_id($operationId);
        var_dump($operation); die();
        $tricountId = $operation->tricount;
        // $operationId = $operation->id;
        // var_dump($operation); die();
        
       
        $amount = Tools::sanitize(floatval($_POST['amount']));
        $operation_date = $_POST['operation_date'];
        $initiator = $_POST['initiator'];
        $created_at = date('y-m-d h:i:s');
            // var_dump($amount); die();
        $operation->setTitle($title);
        $operation->setTricount($tricountId);
        $operation->setAmount($amount);
        $operation->setOperation_date($operation_date);
        $operation->setInitiator($initiator);
        $operation->setCreated_at($created_at);
        $operation->setId($operationId);
        $operation = new Operation($title, $tricountId, $amount, $operation_date, $initiator, $created_at, $operationId);

        $errors = $operation->validateForEdit();

        

        if (isset($_POST['save_template']) && !empty($_POST['template_name'])) {
            $template_name = $_POST['template_name'];
            $checkedUsers = $_POST['c'];
            $weights = $_POST['w'];
        
            $template = new Repartition_templates(null, $template_name, $_POST['tricId']);
            $template->newTemplate($template_name, $_POST['tricId']);
        
            if ($template) {
                foreach ($checkedUsers as $userId) {
                    if ($userId === '') {
                        continue;
                    }
                    $weight = isset($weights[$userId]) && $weights[$userId] !== '' ? $weights[$userId] : 1;
        
                    Repartition_template_items::addNewItems($userId, $template->get_id(), $weight);
                    Operation::deleteRepartition($_GET['param1']);// TODO : a modif car c'est censé supp toutes les items pas les repartitions
                    Operation::insertRepartition($_GET['param1'], $userId, $weight);// TODO : a modif car c'est censé supp toutes les items pas les repartitions
                }
                $this->redirect('operation', 'expenses','aiciTemplate', $_POST['tricId']);
            }
        }
        // die('here');
        if (empty($errors)) {
            // var_dump($_POST); die();
            $operation->update();
            // $operation->insert();
            $this->redirect('operation', 'expenses', $_POST['tricId']);
        } else
            (new View("add_expense"))->show(
                array(

                    "title" => $title,
                    "amount" => $amount,
                    "operation_date" => $operation_date,
                    "initiator" => $initiator[0],
                    "tricount" => $tricountId,
                    "template" => $template,
                    "errors" => $errors,
                    "action" => $action
                )
            );
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
                $this->redirect("operation", "add_expense", $operationId);
            } else if ($_POST['submitted'] === "Delete") {
                $tricount = Tricounts::get_tricount_by_operation_id($operationId);
                $tricountId = $tricount->get_id();
                $operation = $operation->delete();
                $this->redirect("operation", "expenses", $tricountId);
            }
        }

        (new View("add_expense"))->show(array("user" => $user, "tricount" => $tricount));

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