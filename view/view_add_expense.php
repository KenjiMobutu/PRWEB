<!DOCTYPE html>
<html>

<?php
if (isset($_GET['action']) && $_GET['action'] === 'add') { ?>

        <head>
            <title>Add Expense</title>
            <base href="<?= $web_root ?>" />
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link href="css/style.css" rel="stylesheet" type="text/css" />
            <link href="css/add-exp.css" rel="stylesheet" type="text/css" />
        </head>

        <body>
            <?php include 'menu.html' ?>
            <div class="add-exp">
                <p>
                    <?php echo $tricount->get_title(); ?> > New expense
                </p>
                <form action="operation/add_expense" method="post">
                    <div class="errors">
                        <ul>
                            <?php if (!empty($errors))
                                foreach ($errors as $error): ?>
                                            <li>
                                                <?= $error ?>
                                            </li>
                                    <?php endforeach; ?>
                        </ul>
                    </div>
                    <input class="addExp" placeholder="Title" type="text" id="title" name="title" required <?php
                    if (isset($_POST["title"]))
                        echo 'value=' . $operation->getTitle() ?>>
                            <br>
                            <input type="hidden" id="tricId" name="tricId" value="<?php echo $tricount->get_id() ?>">
                    <input class="addExp" placeholder="Amount (EUR)" type="number" id="amount" name="amount" required <?php
                    if (isset($_POST["amount"]))
                        echo 'value=' . $operation->getAmount() ?>>
                            <br>
                            <label for="operation_date">Date</label>
                            <input class="addExp" type="date" id="operation_date" name="operation_date"
                                value="<?php echo date('Y-m-d'); ?>" required <?php
                                   if (isset($_POST["operation_date"]))
                                       echo 'value=' . $operation->getOperationDate() ?>>
                            <br>
                            <label for="paid_by">Paid By</label>
                            <select id="initiator" name="initiator">
                        <?php if (isset($_POST["initiator"])):
                                       echo '<option value=' . $operation->getInitiatorId() . ">" . $operation->getInitiator() . "</option>";
                                   endif; ?>

                        <?php foreach ($users as $urss): ?>
                                <option style="color: black;" value="<?php echo $urss->get_user() ?>"><?php echo $urss->getUserInfo() ?>
                                </option>
                        <?php endforeach; ?>
                    </select>
                    <br>
                    <label for="repartition_template">Use repartition template (optional)</label>
                    <button name="refreshBtn" id="refreshBtn">Refresh</button>
                    <select id="rti" name="rti">
                        <?php if (isset($_POST["rti"]) && $template !== null):
                            echo '<option style="color: black;" value=' . $template->get_id() . ">" . $template->get_title() . "</option>";
                        else: ?>
                                <option value="option-default" style="color: black;">No, I'll use custom repartition</option>
                        <?php endif; ?>
                        <?php foreach ($rti as $rt):
                            $title = $rt["title"];
                            $templateId = $rt["id"];
                            ?>
                                <option name="option_template" style="color: black;" value="<?php echo $templateId ?>">
                                    <?php echo $title ?>
                                </option>
                        <?php endforeach; ?>
                    </select>
                    <label for="who">For whom? (select at least one)</label>
                    <?php
                    if (isset($_POST["refreshBtn"])) {
                        foreach ($ListUsers as $usr) {
                            ?>
                                    <div class="checks">
                                        <div class="check-input">
                                            <input type="checkbox" name="c[<?= $usr->get_user(); ?>]" value="<?php echo $usr->get_user() ?>"
                                                id="userIdTemp" <?php if (isset($template)) {
                                                    if ($usr->is_in_Items($template->get_id())) {
                                                        echo "checked = 'checked'";
                                                    }
                                                }
                                                ; ?>>
                                            <span class="text-input" style="color: yellow; font-weight: bold;">
                                                <?php echo $usr->getUserInfo() ?>
                                            </span>

                                            <fieldset>
                                                <legend class="legend" style="color: yellow; font-weight: bold;">Weight</legend>
                                                <input type="number" name="w[<?= $usr->get_user(); ?>]" id="userWeight" min="0" max="50" <?php if (isset($template)) {
                                                      if ($usr->is_in_Items($template->get_id())) {
                                                          echo "value=" . $usr->get_weight_by_user($template->get_id());
                                                      };
                                                  } else
                                                      echo "value=0"; ?>>
                                            </fieldset>

                                        </div>
                                    </div>
                                    <?php
                        }
                        echo '<input type="submit" value="Submit">';
                    } else {
                        foreach ($users as $usr) {
                            ?>
                                    <div class="check-input">
                                        <input type="checkbox" name="c[<?= $usr->get_user(); ?>]" value="<?php echo $usr->get_user() ?>"
                                            id="userIdTemp">
                                        <span class="text-input" style="color: yellow; font-weight: bold;">
                                            <?php echo $usr->getUserInfo() ?>
                                        </span>

                                        <fieldset>
                                            <legend class="legend" style="color: yellow; font-weight: bold;">Weight</legend>
                                            <input type="number" name="w[<?= $usr->get_user(); ?>]" id="userWeight" value="1" min="0" max="50">
                                        </fieldset>
                                    </div>
                                    <?php
                        } ?>
                            <p>Add a new repartition template</p>
                            <div class="save-template">
                                <input type="checkbox" name="save_template" id="save"><span
                                    style="color: yellow; font-weight: bold;">Save this template</span>
                                <fieldset>
                                    <legend style="color: yellow; font-weight: bold;">Name</legend>
                                    <input type="text" name="template_name" id="savename" placeholder="Name">
                                </fieldset>
                            </div>
                            <input type="submit" value="Submit">
                            <?php
                    }
                    ?>
                </form>
            </div>
        </body>
<?php } else if (isset($_GET['action']) && $_GET['action'] === 'edit') { ?>

                <head>
                    <title>Edit Expense</title>
                    <base href="<?= $web_root ?>" />
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <link href="css/style.css" rel="stylesheet" type="text/css" />
                    <link href="css/add-exp.css" rel="stylesheet" type="text/css" />
                </head>

                <body>
            <?php include 'menu.html' ?>
                    <div class="add-exp">
                        <p>
                    <?php echo $tricount->get_title(); ?> > Edit expense
                        </p>
                        <form action="operation/edit_expense/<?php echo $operation_data->get_id() ?>" method="post">
                            <div class="errors">
                                <ul>
                            <?php if (!empty($errors))
                                foreach ($errors as $error): ?>
                                                    <li>
                                                <?= $error ?>
                                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <input type="hidden" id="operationId" name="operationId" value="<?php echo $operation_data->get_id() ?>">
                            <input class="addExp" placeholder="Title" type="text" id="title"
                                value="<?php echo $operation_data->getTitle() ?>" name="title">
                            <input type="hidden" id="tricId" name="tricId" value="<?php echo $tricount->get_id() ?>">
                            <br>
                            <label for="operation_amount">Total Amount</label>
                            <input class="addExp" placeholder="Amount (EUR)" value="<?php echo $operation_data->getAmount() ?>"
                                type="number" id="amount" name="amount">
                            <br>
                            <label for="operation_date">Date</label>
                            <input class="addExp" type="date" id="operation_date"
                                value="<?php echo $operation_data->getOperationDate() ?>" name="operation_date">
                            <br>

                            <label for="paid_by">Paid By</label>
                            <select id="initiator" name="initiator">
                                <option style="color: black;" selected value="<?php echo $usr ?>"><?php echo $usr ?></option>
                        <?php foreach ($users as $urss): ?>
                                <?php if ($urss->getUserInfo() !== $usr): ?>
                                                <option style="color: black;" value="<?php echo $urss->getUserInfo() ?>"><?php echo $urss->getUserInfo() ?></option>
                                <?php endif; ?>
                        <?php endforeach; ?>
                            </select>
                            <br>
                            <label for="repartition_template">Use repartition template (optional)</label>
                            <button name="refreshBtn" id="refreshBtn">Refresh</button>
                            <select id="rti" name="rti">
                                <option style="color: black;" value="option-default">No,I'll use custom repartition</option>
                        <?php foreach ($rti as $rt):
                            $title = $rt["title"];
                            $templateId = $rt["id"] ?>
                                        <option name="option_template" style="color: black;" value="<?php echo $templateId ?>"><?php echo $title ?></option>
                        <?php endforeach; ?>
                            </select>
                            <label for="who">For whom? (select at least one)</label>
                    <?php foreach ($users as $usr): ?>
                                    <div class="checks">
                                        <input type="checkbox" name="c[<?= $usr->get_user() ?>]" value="<?= $usr->get_user(); ?>"
                                            id="<?php echo $usr->getUserInfo() ?>" <?php if ($usr->is_in_tricount($tricount->get_id())) {
                                                echo "checked";
                                            } ?>>
                                        <span style="color: yellow; font-weight: bold;">
                                    <?php echo $usr->getUserInfo() ?>
                                        </span>

                                            <legend>Weight</legend>
                                            <input type="number" name="w[<?= $usr->get_user() ?>]" id="<?php echo $usr->get_user() ?>" value="1"
                                                min="0" max="50">

                                            <legend>Amount</legend>

                                            <input type="number" value="<?php echo $usr->get_dette($operation_data->get_id(), $usr)?>" >

                                    </div>
                    <?php endforeach; ?>

                            <p>Add a new repartition template</p>
                            <div class="save-template">
                                <input type="checkbox" name="save_template" id="save"> <span
                                    style="color: yellow; font-weight: bold;">Save this template</span>
                                <fieldset>
                                    <legend>Name</legend>
                                    <input type="text" name="name_template" id="savename" placeholder="Name">
                                </fieldset>
                            </div>

                            <input type="submit" value="Submit">
                        </form>
                        <button class="delete-btn" style="background-color: blue; color: white;">
                            <a href="/prwb_2223_c03/Operation/delete_confirm/<?php echo $operation_data->get_id() ?>"
                                style="text-decoration: none; color: white;">DELETE</a>
                        </button>
                    </div>
                </body>
<?php } ?>

</html>
