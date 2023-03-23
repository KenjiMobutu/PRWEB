<!DOCTYPE html>
<html>

<head>
    <title>
        <?php echo isset($operation) ? 'Edit Expense' : 'Add Expense'; ?>
    </title>
    <base href="<?= $web_root ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/style.css" rel="stylesheet" type="text/css" />
    <link href="css/add-exp.css" rel="stylesheet" type="text/css" />
    <script src="lib/jquery-3.6.3.min.js" type="text/javascript"></script>
    <script src="lib/script.js"></script>
</head>

<body>
    <?php include 'menu.html' ?>
    <div class="add-exp">
        <p>
            <?php echo $tricount->get_title(); ?> >
            <?php echo isset($operation) ? 'Edit expense' : 'Add expense'; ?>
        </p>
        <form
            action="<?php echo isset($operation) ? "operation/edit_expense/{$operation->get_id()}" : 'operation/add_expense'; ?>"
            method="post">
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
            <?php if (isset($operation)): ?>
                <input type="hidden" id="operationId" name="operationId" value="<?php echo $operation->get_id() ?>">
            <?php endif; ?>
            <input class="addExp" placeholder="Title" type="text" id="title"
                value="<?php echo isset($operation) ? $operation->getTitle() : ''; ?>" name="title">
            <input type="hidden" id="tricId" name="tricId" value="<?php echo $tricount->get_id() ?>">
            <br>
            <label for="operation_amount">Total Amount</label>
            <input class="addExp" placeholder="Amount (EUR)"
                value="<?php echo isset($operation) ? $operation->getAmount() : ''; ?>" type="number" id="amount"
                name="amount" oninput="calculateAmounts()">
            <br>
            <label for="operation_date">Date</label>
            <input class="addExp" type="date" id="operation_date"
                value="<?php echo isset($operation) ? $operation->getOperationDate() : date('Y-m-d'); ?>"
                name="operation_date">

            <br>

            <label for="paid_by">Paid By</label>
            <select id="initiator" name="initiator">
                <?php if (isset($operation)): ?>
                    <option style="color: black;" selected value="<?php echo $operation->getInitiator(); ?>"><?php echo $operation->getInitiator(); ?></option>
                <?php endif; ?>
                <?php foreach ($users as $urss): ?>
                    <?php if (!isset($operation) || $urss->getUserInfo() !== $operation->getInitiator()): ?>
                        <option style="color: black;" value="<?php echo $urss->getUserInfo() ?>"><?php echo $urss->getUserInfo() ?></option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
            <br>
            <label for="repartition_template">Use repartition template (optional)</label>
            <button name="refreshBtn" id="refreshBtn">Refresh</button>
            <select id="rti" name="rti">


                <option style="color: black;" value="option-default">No, I'll use custom repartition</option>
                <?php foreach ($rti as $rt):
                    $title = $rt["title"];
                    $templateId = $rt["id"] ?>
                    <option name="option_template" style="color: black;" value="<?php echo $templateId ?>"><?php echo $title ?></option>
                <?php endforeach; ?>
            </select>
            <label for="who">For whom? (select at least one)</label>
            <?php
            if (isset($_POST["addrefreshBtn"])) {
                foreach ($ListUsers as $usr) {
                    ?>
                    <div class="checks">
                        <div class="check-input">
                            <?php
                            $isChecked = isset($template) && $usr->is_in_Items($templateId, $usr->user);
                            ?>

                            <input type="checkbox" name="c[<?= $usr->get_user(); ?>]" value="<?= $usr->get_user() ?>"
                                id="userIdTemp" <?= $isChecked ? "checked" : ""; ?>>

                            <span class="text-input" style="color: yellow; font-weight: bold;">
                                <?php echo $usr->getUserInfo() ?>
                            </span>

                            <fieldset>
                                <legend class="legend" style="color: yellow; font-weight: bold;">Weight</legend>
                                <input type="number" name="w[<?= $usr->get_user(); ?>]" id="userWeight" min="0" max="50" <?php if (isset($template)) {
                                      if ($usr->is_in_Items($template->get_id())) {
                                          echo "value=" . $usr->get_weight_by_user($template->get_id());
                                      }

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
                }
            } ?>
            <!-- <?php foreach ($users as $usr): ?>
                <div class="checks">
                    <input type="checkbox" name="c[<?= $usr->get_user() ?>]" value="<?= $usr->get_user(); ?>"
                        id="<?php echo $usr->getUserInfo() ?>" <?php if (isset($operation) && $usr->is_in_tricount($tricount->get_id())) {
                               echo "checked";
                           } ?>>
                    <span style="color: yellow; font-weight: bold;">
                        <?php echo $usr->getUserInfo() ?>
                    </span>

                    <legend>Weight</legend>
                    <input type="number" name="w[<?= $usr->get_user() ?>]" id="<?= $usr->get_user() ?>_weight" value="1"
                        min="0" max="50">
                    <?php if (isset($operation)): ?>
                        <input type="number" id="<?= $usr->get_user() ?>_amount"
                            value="<?php echo $usr->get_dette($operation->get_id(), $usr) ?>" hidden>
                    <?php endif; ?>

                    <legend>Amount</legend>
                    <?php if (isset($operation)): ?>
                        <input type="number" id="<?= $usr->get_user() ?>_dette"
                            value="<?php echo $usr->get_dette($operation->get_id(), $usr) ?>">
                    <?php endif; ?>
                </div>
            <?php endforeach; ?> -->

            <p>Add a new repartition template</p>
            <div class="save-template">
                <input type="checkbox" name="save_template" id="save"> <span
                    style="color: yellow; font-weight: bold;">Save this
                    template</span>
                <fieldset>
                    <legend>Name</legend>
                    <input type="text" name="template_name" id="savename" placeholder="Name">
                </fieldset>
            </div>

            <input type="submit" value="<?php echo isset($operation) ? 'Update' : 'Submit'; ?>">
        </form>
        <?php
        if ($action === 'edit' || $action === 'edit_expense') {
            echo '<button class="delete-btn" style="background-color: blue; color: white;">';
            echo '<a href="/prwb_2223_c03/Operation/delete_confirm/' . $operation->get_id() . '" style="text-decoration: none; color: white;">DELETE</a>';
            echo '</button>';
        }
        ?>
    </div>
</body>

</html>