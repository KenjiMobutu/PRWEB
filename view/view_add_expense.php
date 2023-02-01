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
</style>

<div class="add-exp">
<p><?php echo $tricount->get_title();?> > New expense</p>
    <form action="operation/add_expense" method="post">
            <input class="addExp" placeholder="Title" type="text" id="title" name="title" <?php if(isset($_POST["title"])) echo 'value=' . $title ?>>
            <br>
            <input type="hidden" id="tricId" name="tricId" value="<?php echo $tricount->get_id()?>">
            <input class="addExp" placeholder="Amount (EUR)" type="number" id="amount" name="amount" required <?php if(isset($_POST["amount"])) echo 'value=' . $amount ?>>
            <br>
            <label  for="operation_date">Date</label>
            <input class="addExp" type="date" id="operation_date" name="operation_date" required <?php if(isset($_POST["operation_date"])) echo 'value=' . $operation_date ?>>
            <br>
            <label for="paid_by">Paid By</label>
            <select id="initiator" name="initiator">
            <?php if(isset($_POST["initiator"])): echo '<option value=' . $init->getUserId() . ">" . $init->getFullName() . "</option>"; endif; ?>
            
            <?php foreach($users as $urss): ?>
                <option value="<?php echo $urss->getUserId()?>"><?php echo $urss->getFullName()?></option>
                <?php endforeach;?>
            </select>
            <br>
            <label for="repartition_template">Use repartition template (optional)</label>
            <button name="refreshBtn" id="refreshBtn">Refresh</button>
            <select id="rti" name="rti">
            <?php if(isset($_POST["rti"])): echo '<option value=' . $template->get_id() . ">" . $template->get_title() . "</option>"; endif; ?>
            <option value="option-default">No,I'll use custom repartition</option>
            <?php foreach($rti as $rt):  $title = $rt["title"]; $templateId=$rt["id"]?>
                <option name="option_template" value="<?php echo $templateId?>"><?php echo $title?></option>
                <?php endforeach;?>
            <label for="who">For whom? (select at least one)</label>
            <?php 
                if(isset($_POST["refreshBtn"])) {
                    foreach($ListUsers as $usr) { 
                ?>
                <div class="checks">
                    <input type="checkbox" name="c[<?= $usr->get_user(); ?>]" value="<?php echo $usr->get_user() ?>" id="userIdTemp" <?php if(isset($template)){
                                                                                                            if($usr->is_in_Items($template->get_id())) {
                                                                                                                echo "checked = 'checked'" ;} };?> >
                        <span style="color: yellow; font-weight: bold;"><?php echo $usr->getUserInfo() ?></span>
                    <fieldset>
                        <legend style="color: yellow; font-weight: bold;">Weight</legend>
                        <input type="number" name="w[<?= $usr->get_user(); ?>]" id="userWeight" min="0" max="50" <?php if(isset($template)){
                                                                                if($usr->is_in_Items($template->get_id())) {
                                                                                    echo "value=".$usr->get_weight_by_user($template->get_id());}; }else echo "value=0";?>>
                    </fieldset>
                </div>
                <?php 
                    }
                } else {
                    foreach($users as $usr) { 
                ?>
                            <div class="checks">
                    <input type="checkbox" name="c[<?= $usr->getUserId(); ?>]" value="<?php echo $usr->getUserId() ?>" id="userIdTemp">
                        <span style="color: yellow; font-weight: bold;"><?php echo $usr->getFullName() ?></span>
                    <fieldset>
                        <legend style="color: yellow; font-weight: bold;">Weight</legend>
                        <input type="number" name="w[<?= $usr->getUserId(); ?>]" id="userWeight" value="1" min="0" max="50">
                    </fieldset>
                </div>
                <?php 
                    }
                }
            ?>
                <p>Add a new repartition template</p>
                <div class="save-template">
                <input type="checkbox" name="save_template" id="save" ><span style="color: yellow; font-weight: bold;">Save this template</span>
                    <fieldset>
                        <legend style="color: yellow; font-weight: bold;">Name</legend>
                        <input type="text" name="template_name" id="savename" placeholder="Name">    
                    </fieldset>
                </div>
    
            <input type="submit" value="Submit">
        </form>
</div>
	
</body>
</html>
