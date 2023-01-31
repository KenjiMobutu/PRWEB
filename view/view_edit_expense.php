<!DOCTYPE html>
<html>
<head>
	<title>Add Expense</title>
    <base href="<?= $web_root ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php include 'menu.html' ?>
<style>
    .add-exp{
    margin:25px;
    }
    .checks {
        display: flex;
        align-items: center;
        padding: 10px;
        background-color: #cccccc;
        border: 1px solid black;
        margin-bottom: 10px;
    }
    .checks input[type='checkbox'] {
        margin-right: 10px;
        height: 25px;
    }

    .checks span {
        border-right: 1px solid #000;
        padding-right: 10px;
        margin-right: 10px;
        height: 25px;
        align-items: center;
        display: flex;
    }
    fieldset {
        margin-left: auto;
        display: flex;
        align-items: center;
    }

    legend {
        font-size: 14px;
        font-weight: bold;
        margin-left: 10px;
    }

    .save-template {
        display: flex;
        align-items: center;
        padding: 10px;
        background-color: #cccccc;
        border: 1px solid black;
        margin-bottom: 10px;
    }
    .save-template input[type='checkbox'], .save-template input[type='text'] {
        height: 15px;
    }
    legend {
        font-size: 14px;
        font-weight: bold;
        margin-left: 10px;
        height: 25px;
        line-height: 25px;
    }

    input[class="addExp"],
    select {
    width: 100%;
    padding: 12px 20px;
    margin: 8px 0;
    display: inline-block;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
    }

    .delete-btn{
        width: 100%;
    padding: 12px 20px;
    margin: 8px 0;
    display: inline-block;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
    }
</style>

<div class="add-exp">
<p><?php echo $tricount->get_title();?> > Edit expense</p>
    <form action="operation/edit_expense/<?php echo $operation_data->id ?>" method="post">
    <input type="hidden" id="operationId" name="operationId" value="<?php echo $operation_data->id?>">
    <input class="addExp" placeholder="Title" type="text" id="title" value="<?php echo $operation_data->title ?>" name="title">
            <input type="hidden" id="tricId" name="tricId" value="<?php echo $tricount->get_id()?>">
            <br>
            <input class="addExp" placeholder="Amount (EUR)" value="<?php echo $operation_data->amount ?>" type="number" id="amount" name="amount">
            <br>
            <label  for="operation_date">Date</label>
            <input class="addExp" type="date" id="operation_date" value="<?php echo $operation_data->operation_date?>" name="operation_date">
            <br>

            <label for="paid_by">Paid By</label>
            <select id="initiator" name="initiator">
            <option selected value="<?php echo $usr ?>"><?php echo $usr ?></option>
            <?php foreach($users as $urss): ?>
                <option value="<?php echo $urss->getFullName()?>"><?php echo $urss->getFullName()?></option>
                <?php endforeach;?>
            </select>
            <br>
            <label for="repartition_template">Use repartition template (optional)</label>
            <select id="rti" name="rti">
            <option value="option-default">No,I'll use custom repartition</option>
            <?php foreach($rti as $rt):  $title = $rt["title"];?>
                <option value="<?php echo $title?>"><?php echo $title?></option>
            <?php endforeach;?>
            </select>
            <label for="who">For whom? (select at least one)</label>
                <?php foreach($users as $usr): ?>
                    <div class="checks">
                        <input type="checkbox" name="<?php echo $usr->getFullName() ?>" id="<?php echo $usr->getUserId() ?>" <?php if ($usr->participates_in_tricount()) {echo "checked";} ?>>
                            <span style="color: yellow; font-weight: bold;"><?php echo $usr->getFullName() ?></span>
                        <fieldset>
                            <legend>Weight</legend>
                            <input type="number" name="user_weight" id="<?php echo $usr->getUserId() ?>" value="1" min="0" max="50">
                        </fieldset>
                    </div>
                <?php endforeach;?>
                <p>Add a new repartition template</p>
                <div class="save-template">
                <input type="checkbox" name="save_template" id="save" > <span style="color: yellow; font-weight: bold;">Save this template</span>
                    <fieldset>
                        <legend>Name</legend>
                        <input type="text" name="name_template" id="savename" placeholder="Name">
                    </fieldset>
                </div>

            <input type="submit" value="Submit">
        </form>

        <!-- Operation/delete/<?php echo $operation_data->id ?> -->

        <button class="delete-btn" style="background-color: blue; color: white;">
            <a href="/prwb_2223_c03/Operation/delete_confirm/<?php echo $operation_data->id ?>" style="text-decoration: none; color: white;">DELETE</a>
</button>
</div>

</body>
</html>
