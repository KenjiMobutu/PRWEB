<!DOCTYPE html>
<html>

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
                    <fieldset>
                        <legend>Weight</legend>
                        <input type="number" name="w[<?= $usr->get_user() ?>]" id="<?php echo $usr->get_user() ?>" value="1"
                            min="0" max="50">
                    </fieldset>
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

        <!-- Operation/delete/<?php echo $operation_data->get_id() ?> -->

        <button class="delete-btn" style="background-color: blue; color: white;">
            <a href="/prwb_2223_c03/Operation/delete_confirm/<?php echo $operation_data->get_id() ?>"
                style="text-decoration: none; color: white;">DELETE</a>
        </button>
    </div>

</body>

</html>