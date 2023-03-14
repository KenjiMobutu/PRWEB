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
        if (isset($_GET['param1']) && !is_numeric($_GET['param1'])) {
            $this->redirect('main', "error");
        } else {
            //$userId = $user->getUserId();
            $checkTricount = Tricounts::exists($_GET['param1']);
            if (!is_null($checkTricount)) {
            }
            if ($checkTricount <= 0) {
                $this->redirect('main', "error");
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
            $weights = Repartitions::get_user_and_weight_by_operation_id($tricount->get_id());
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
            $usr = $operation_data->getInitiatorId();
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
        //var_dump($_POST);
        
        if (isset($_POST["addrefreshBtn"]) && $_POST['rti'] !== 'option-default') {
            $requiredFields = ["title", "tricId", "amount", "operation_date", "initiator", "rti"];
            $allFieldsExist = true;
            foreach ($requiredFields as $field) {
                if (!array_key_exists($field, $_POST)) {
                    $allFieldsExist = false;
                    break;
                }
            }
            //var_dump($allFieldsExist);
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

                if ($template === null) {
                    $this->redirect("operation", "expenses/" . $tricount->get_id());
                }
                $ListUsers = Participations::get_by_tricount($tricId);
                $listItems = Repartition_template_items::get_user_by_repartition($template->get_id());
                $operation = new Operation($title, $tricId, $amount, $operation_date, $initiator, $operation_date);
                $errors = $operation->validate();
                var_dump($errors);
                if (count($errors) === 0) {
                    (new View("add_expense"))->show(
                        array(
                            "user" => $user,
                            "operation" => $operation,
                            "rti" => $rti,
                            "users" => $users,
                            "tricount" => $tricount,
                            "template" => $template,
                            "ListUsers" => $ListUsers,
                            "listItems" => $listItems,
                            "errors" => $errors,
                            "action"=>$action
                        )
                    );
                    echo 'test';
                } else {
                    // show errors
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
                            "listItems" => $listItems,
                            "errors" => $errors
                        )
                    );
                }
            }
            //var_dump($_POST); die();
            //$this->redirect("main", "error");
        }
        // var_dump($_POST );
        // die();
    }


    public function SaveWithTemplateExistant($user)
    {
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
            $this->redirect("main", "error");
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
                    "errors" => $errors
                )
            );
    }

    public function saveWithCustomTemplate()
    {
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
                        "errors" => $errors
                    )
                );
        }
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
            $rti = Repartition_template_items::get_by_user_and_tricount($userId, $_GET['param1']);
            $action = $_GET['action'];
        }
        (new View("add_expense"))->show(array("user" => $user, "tricount" => $tricount, "rti" => $rti, "users" => $users, "action"=>$action));

    }
    //TODO garder les weights en cas d'erreur ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    public function add_expense()
    {
        $user = $this->get_user_or_redirect();
        if (isset($_GET['param1']) && !is_numeric($_GET['param1'])) {

            $this->redirect('main', "error");
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
            $this->redirect('main', "error");
        }
        if ($checkOperation <= 0) {
            $this->redirect('main', "error");
        } else {
            $action = $_GET['action'];
            $userId = $user->getUserId();
            $tricount = Tricounts::get_tricount_by_operation_id($_GET['param1']);
            $operationId = $_GET['param1'];
            $operation_data = Operation::getOperationByOperationId($operationId);
            $usr = $operation_data->getInitiator();
            $users = Participations::get_by_tricount($tricount->get_id());

            // $users = User::getUsers();
            $rti = Repartition_template_items::get_by_user_and_tricount($userId, $_GET['param1']);
        }

        (new View("add_expense"))->show(array("user" => $user, "action" => $action, "operation_data" => $operation_data, "users" => $users, "rti" => $rti, "tricount" => $tricount, "usr" => $usr));

    }

    //A FIXER
    // public function edit_expense()
    // {
    //     if (isset($_GET['param1']) && !is_numeric($_GET['param1'])) {
    //         $this->redirect('main', 'error');
    //     }

    //     $operation = Operation::getOperationByOperationId($_POST['operationId']);
    //     if (!$operation) {
    //         $this->redirect('main', 'error');
    //     }

    //     if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    //         return;
    //     }

    //     if (isset($_POST['save_template'])) {
    //         $save_template = $_POST['save_template'] === 'on';
    //     } else {
    //         $save_template = false;
    //     }

    //     if ($save_template && empty($_POST['template_name'])) {
    //         $this->redirect('main', 'error', 'title_cannot_be_empty');
    //     }

    //     $title = Tools::sanitize($_POST['title']);
    //     $tricount = $_POST['tricId'];
    //     $amount = Tools::sanitize(floatval($_POST['amount']));
    //     $operation_date = $_POST['operation_date'];
    //     $init = User::get_by_name($_POST['initiator']);
    //     $initiator = $init->getUserId();
    //     $created_at = date('y-m-d h:i:s');

    //     $operation->setTitle($title);
    //     $operation->setTricount($tricount);
    //     $operation->setAmount($amount);
    //     $operation->setOperation_date($operation_date);
    //     $operation->setInitiator($initiator);
    //     $operation->setCreated_at($created_at);

    //     $errors = $operation->validate();

    //     if (!$save_template && empty($_POST['template_name'])) {
    //         $operation->update();
    //         $this->redirect('operation', 'expenses', $_POST['tricId']);
    //     }

    //     if ($save_template && !empty($_POST['template_name'])) {
    //         $template_name = $_POST['template_name'];
    //         $checkedUsers = $_POST['c'];
    //         $weights = $_POST['w'];
    //         $template = new Repartition_templates(null, $template_name, $_POST['tricId']);
    //         $template->newTemplate($template_name, $_POST['tricId']);

    //         if ($template) {
    //             for ($i = 0; $i < count($checkedUsers); $i++) {
    //                 if (!isset($checkedUsers[$i])) {
    //                     continue;
    //                 }
    //                 //the continue statement is used in an if block to check if the $checkedUsers[$i]
    //                 //is not set. If it's not set, the continue statement will skip the current
    //                 //iteration of the loop and move on to the next iteration without executing
    //                 //the remaining code inside the loop block.
    //                 if ($weights[$i] === '' || $weights[$i] === '0') {
    //                     $weights[$i] = 1;
    //                 }

    //                 Repartition_template_items::addNewItems(
    //                     $checkedUsers[$i],
    //                     $template->get_id() ?: '',
    //                     $weights[$i]
    //                 );

    //                 Operation::deleteRepartition($operation->get_id());
    //                 Operation::insertRepartition($operation->get_id(), $weights[$i], $checkedUsers[$i]);
    //             }

    //             $this->redirect('operation', 'expenses', $_POST['tricId']);
    //         }
    //     }

    //     if (empty($errors)) {
    //         $operation->update();
    //         $this->redirect('operation', 'expenses', $_POST['tricId']);
    //     } else
    //         (new View("edit_expense"))->show(
    //             array(

    //                 "title" => $title,
    //                 "amount" => $amount,
    //                 "operation_date" => $operation_date,
    //                 "init" => $init,
    //                 "tricount" => $tricount,
    //                 "template" => $template,
    //                 "errors" => $errors
    //             )
    //         );
    // }


    public function editWithoutTemplate($user)
    {
       
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
        $rti = Repartition_template_items::get_by_user_and_tricount($initiator, $_GET['param1']);
        $template = Repartition_templates::get_by_id($_POST['rti']);
        $ListUsers = Participations::get_by_tricount($tricountId);
        // A CHECKER $^^$^$$^$ $listItems = Repartition_template_items::get_user_by_repartition($template->get_id());

        if (!$tricount || !$init) {
            $this->redirect("main", "error");
            return;
        }

        $operation = new Operation($title, $tricountId, $amount, $operation_date, $initiator, $created_at);
        $errors = $operation->validate();

        if (empty($errors)) {
            $operation->insert();
            $this->redirect("operation", "expenses", $tricountId);
        } else
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
                    //"listItems" => $listItems, A CHECKER ^ùµùµ$^$^µ^$µù$^µ
                    "errors" => $errors
                )
            );
    }

    public function editWithCustomTemplate()
    {
        
        $title = $_POST["title"];
        $tricount = $_POST["tricId"];
        $amount = $_POST["amount"];
        $operation_date = $_POST["operation_date"];
        $initiator = $_POST["initiator"];
        $template_name = $_POST["name_template"];
        $checkedUsers = $_POST["c"];
        $weights = $_POST["w"];

        if ($title && $tricount && $amount && $operation_date && $initiator && $template_name && $checkedUsers && $weights) {
            $title = Tools::sanitize($_POST["title"]);
            $tricount = $_POST["tricId"];
            $amount = Tools::sanitize(floatval($_POST["amount"]));
            $operation_date = $_POST["operation_date"];
            $initiator = $_POST["initiator"];
            $created_at = date('y-m-d h:i:s');
            $template_name = Tools::sanitize($_POST["name_template"]);

            $operation = new Operation($title, $tricount, $amount, $operation_date, $initiator, $created_at);
            $template = new Repartition_templates(null, $template_name, $_POST["tricId"]);

            $errors = $operation->validate();

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

            }
        }
    }

    public function edit_expense()
    {
        $user = $this->get_user_or_redirect();
        $errors = [];

        if (isset($_GET['param1']) && !is_numeric($_GET['param1'])) {
            $this->redirect('main', "error");
        }

        if (isset($_POST["refreshBtn"])) {
            $this->refreshBtnHandler($user);
            //TODO NOT SURE WE NEED THIS ONE ????????????????????????
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST["save_template"])) {
            //print_r($_POST);
            $this->editWithoutTemplate($user);
            //if i make a custom template => need save name checked
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["c"]) && isset($_POST["w"])) {
            $this->editWithCustomTemplate()->validate();
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