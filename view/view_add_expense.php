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
            <?php if (isset($operation) || isset($operationId)): ?>
                <input type="hidden" id="operationId" name="operationId"
                    value="<?php if (isset($operation))
                        echo $operation->get_id();
                    else if (isset($operationId))
                        echo $operationId; ?>">
            <?php endif; ?>
            <input required class="addExp" placeholder="Title" type="text" id="title" value="<?php
            if (isset($info))
                echo $info[0];
            else if (isset($operation))
                echo $operation->getTitle();
            else
                echo ''; ?>" name="title">
            <input type="hidden" id="tricId" name="tricId" value="<?php echo $tricount->get_id() ?>">
            <br>
            <label for="operation_amount">Amount</label>
            <input required class="addExp" placeholder="Amount (EUR)" value="<?php
            if (isset($info))
                echo $info[1];
            else if (isset($operation))
                echo $operation->getAmount();
            else
                echo ''; ?>" type="number" id="amount" name="amount" oninput="calculateAmounts()">
            <br>
            <label for="operation_date">Date</label>
            <input class="addExp" type="date" id="operation_date" value="<?php
            if (isset($info))
                echo $info[2];
            else if (isset($operation))
                echo $operation->getOperationDate();
            else
                echo date('Y-m-d'); ?>" name="operation_date">
            <br>
            <label for="paid_by">Paid By</label>
            <select id="initiator" name="initiator">
                <?php
                if (isset($init)) {
                    echo "<option style='color: black;' selected value='{$init->getUserId()}'>{$init->getFullName()}</option>";
                } else if (isset($operation)) {
                    echo "<option style='color: black;' selected value='{$operation->getInitiatorId()}'>{$operation->getInitiator()}</option>";
                }
                ?>
                <?php foreach ($users as $user): ?>
                    <?php if (!isset($operation) || $user->getUserInfo() !== $operation->getInitiator()): ?>
                        <option style="color: black;" value="<?= $user->get_user() ?>"><?= $user->getUserInfo() ?></option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
            <br>
            <label for="repartition_template">Use repartition template (optional)</label>
            <button name="refreshBtn" id="refreshBtn">Refresh</button>
            <!--$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$ a template is lost after refresh + error $$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$-->
            <select id="repartitionTemplate" name="rti">
                <?php if (isset($template)) {
                    echo "<option style='color: black;' value='{$template->get_id()}'>{$template->get_title()}</option>";
                } else {
                    echo "<option style='color: black;' value='option-default'>No, I'll use custom repartition</option>";
                }
                ?>
                <?php foreach ($rti as $rt):
                    $title = $rt["title"];
                    $rtiId = $rt["id"] ?>
                    <?php if (isset($template)):
                        if ($title !== $template->get_title()): ?>
                            <option name="option_template" id="option_template" style="color: black;" value="<?php echo $rtiId ?>">
                                <?php echo $title ?></option>
                        <?php endif; ?>

                    <?php else: ?>
                        <option name="option_template" id="option_template" style="color: black;" value="<?php echo $rtiId ?>">
                            <?php echo $title ?></option>
                    <?php endif; ?>

                <?php endforeach; ?>
            </select>
            <label for="who">For whom? (select at least one)</label>
            <?php foreach ($users as $usr) {
                $repartitions_map = [];
                if (!empty($repartitions)) {
                    foreach ($repartitions as $repartition) {
                        $repartitions_map[$repartition->user] = $repartition;
                    }
                }
                ?>
                <div class="check-input">
                    <?php
                    $isChecked = isset($repartitions_map[$usr->get_user()]);
                    if (!empty($templateId) && isset($template) && $usr->is_in_Items($templateId, $usr->user)) {
                        $isChecked = true;
                    }
                    //$$$$$$$$$$$$$$$ Keep the checkbox checked in case of error but if weight is null, the user is still checkedin the next refresh :(
                    // if (isset($_POST['c']) && is_array($_POST['c']) && in_array($usr->get_user(), $_POST['c'])) {
                    //     
                    //     $isChecked = true;
                    // }
                    ?>
                    <input type="checkbox" name="c[<?= $usr->get_user() ?>]" value="<?= $usr->get_user() ?>"
                        id="<?php echo $usr->getUserInfo() ?>" <?php echo $isChecked ? "checked" : ""; ?>>
                    <span class="text-input" style="color: yellow; font-weight: bold;">
                        <?php echo $usr->getUserInfo() ?>
                    </span>
                    <fieldset>
                        <legend class="legend" style="color: yellow; font-weight: bold;">Weight</legend>
                        <input type="number" name="w[<?= $usr->get_user(); ?>]" id="userWeight" min="0" max="50" <?php
                          if (isset($template)) {
                              if ($usr->is_in_Items($template->get_id(), $usr->user)) {
                                  echo "value=" . $usr->get_weight_by_user($template->get_id());
                              }
                          } else {
                              echo "value=" . (isset($repartitions_map[$usr->get_user()]) ? $repartitions_map[$usr->get_user()]->weight : '');
                          }
                          ?>>
                    </fieldset>
                </div>

                <?php
            }
            ?>
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

            <input type="submit" value="<?php echo 'Submit'; ?>">
        </form>
        <?php
        if ($action === 'edit' || $action === 'edit_expense') {
            echo '<button class="delete-btn" style="background-color: blue; color: white;">';
            echo '<a href="/Operation/delete_confirm/' . $_GET['param1'] . '" style="text-decoration: none; color: white;">DELETE</a>';
            echo '</button>';
        }
        ?>
    </div>
</body>
<script>
    $(document).ready(function () {
        const repartitionTemplate = $('#repartitionTemplate');
        const refreshBtn = $('#refreshBtn');

        repartitionTemplate.on('change', function () {
            refreshBtn.hide();

            const selectedTemplateId = repartitionTemplate.val(); //GET TEPLATE ID
            console.log(selectedTemplateId);
            $.ajax({
                url: 'operation/get_template_service/' + selectedTemplateId,
                method: 'GET',
                dataType: 'json',
                success: function (templateData) {
                    updateInputsAndCheckboxes(templateData);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error('Error fetching template data:', textStatus, errorThrown);
                }
            });

            function updateInputsAndCheckboxes(templateData) {
                var check = $(`input[type="checkbox"]`);
                check.prop('checked', false);

                var numb = $(`input[type="number"]`);
                numb.val('');

                templateData.forEach(userTemplateData => {
                    const userCheckbox = $(`input[type="checkbox"][name="c[${userTemplateData.user}]"]`);
                    if (userCheckbox.length > 0) {
                        userCheckbox.prop('checked', true);
                        const userWeight = $(`input[type="number"][name="w[${userTemplateData.user}]"]`);
                        userWeight.val(userTemplateData.weight);
                    }
                });
            }

        });
    });
</script>

</html>