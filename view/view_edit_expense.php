<!DOCTYPE html>
<html>

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
            <?php echo $tricount->get_title(); ?> > Edit expense
        </p>
        <form action="operation/edit_expense/<?php echo $operation_data->get_id() ?>" method="post">
            <input type="hidden" id="operationId" name="operationId" value="<?php echo $operation_data->get_id() ?>">
            <input class="addExp" placeholder="Title" type="text" id="title"
                value="<?php echo $operation_data->getTitle() ?>" name="title">
            <input type="hidden" id="tricId" name="tricId" value="<?php echo $tricount->get_id() ?>">
            <br>
            <input class="addExp" placeholder="Amount (EUR)" value="<?php echo $operation_data->getAmount() ?>" type="number"
                id="amount" name="amount">
            <br>
            <label for="operation_date">Date</label>
            <input class="addExp" type="date" id="operation_date" value="<?php echo $operation_data->getOperationDate() ?>"
                name="operation_date">
            <br>

            <label for="paid_by">Paid By</label>
            <select id="initiator" name="initiator">
                <option selected value="<?php echo $usr ?>"><?php echo $usr ?></option>
                <?php foreach ($users as $urss): ?>
                    <option value="<?php echo $urss->getFullName() ?>"><?php echo $urss->getFullName() ?></option>
                <?php endforeach; ?>
            </select>
            <br>
            <label for="repartition_template">Use repartition template (optional)</label>
            <select id="rti" name="rti">
                <option value="option-default">No,I'll use custom repartition</option>
                <?php foreach ($rti as $rt):
                    $title = $rt["title"]; ?>
                    <option value="<?php echo $title ?>"><?php echo $title ?></option>
                <?php endforeach; ?>
            </select>
            <label for="who">For whom? (select at least one)</label>
            <?php foreach ($users as $usr): ?>
                <div class="checks">
                    <input type="checkbox" name="<?php echo $usr->getFullName() ?>" id="<?php echo $usr->getUserId() ?>"
                        <?php if ($usr->participates_in_tricount()) {
                            echo "checked";
                        } ?>>
                    <span style="color: yellow; font-weight: bold;">
                        <?php echo $usr->getFullName() ?>
                    </span>
                    <fieldset>
                        <legend>Weight</legend>
                        <input type="number" name="user_weight" id="<?php echo $usr->getUserId() ?>" value="1" min="0"
                            max="50">
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