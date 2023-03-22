<!DOCTYPE html>
<head>
    <base href="<?= $web_root ?>" />
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link href="css/style.css" rel="stylesheet" type="text/css" />
        <link
            href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400&family=Sen:wght@400;700;800&display=swap"
            rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
        <script src="lib/jquery-3.6.3.min.js" type="text/javascript"></script>

        <title>Edit Template</title>
</head>
<body>
<script>
       $(function() {
            let title = $("#template_title");
            let errTitle = $("#errTitle");
            let btn = $("#btnSubmit");
            let checkedUser = $(".check");
            let errItems = $("#errItems");
            let errorTitleShow = false;

            title.on("input", function() {
                if (title.val().length < 3) {
                    errTitle.html("Title must have at least 3 characters.");
                    errTitle.css({"color": "red"});
                    errorTitleShow = true;
                    btn.prop("disabled", true);
                } else {
                    errTitle.html("");
                    errorTitleShow = false;
                    btn.prop("disabled", false);
                }
            });

            if(checkedUser.is(":checked") == false){
                errItems.html("You must check at least one user");
                    errItems.css({"color": "red"});
                    btn.prop("disabled", true);
            }else{
                errItems.html("");
                btn.prop("disabled", false);
            }
            checkedUser.on("change", function() {
                console.log(checkedUser.is(":checked"));
                if ($(".check:checked").length === 0) {
                    errItems.html("You must check at least one user");
                    errItems.css({"color": "red"});
                    btn.prop("disabled", true);
                } else {
                    errItems.html("");
                    btn.prop("disabled", false);
                }
            });
        });

</script>

<?php include 'menu.html' ?>
    <form action="templates/editTemplate" method="post" id="edit_template_form">
        <div class="edit_template_container">
        <?php if (count($listUser) > 1) : ?>

            <p class="edit_template_p">Title :</p>
            <input type="text" name="template_title" id="template_title" 
                    value="<?php 
                    if(isset($template))
                        echo $template->get_title();
                    if(isset($template_title))
                        echo $template_title;
                    ?>" required>
            <span class="errTitle" id="errTitle"></span>
            <p class="edit_template_p">
            Template items :
            </p><br>
            <!-- pour récupérer l'id du tricount & template si reçu dans le submit du form -->
            <input type="text" name="tricountId" value="<?php echo $tricount->get_id(); ?>" hidden>
            <input type="text" name="templateID" value="<?php if(isset($templateID)){ echo $templateID;}  ?>" hidden>
            <span class="errItems" id="errItems"></span>

            <?php foreach($listUser as $listusr): ?>
            <!-- mettre c[User->id] ça fera un tableau avec des données -->              <!-- check si c'est un edit t'emplate et récupère les items liés-->
                <div class="edit_template_items">
                <!-- // par défaut on check si l'utilisateur est dans les items si c'est un update -->
                    <?php if (!isset($checkedUser)): ?> 
                        <input  type="checkbox" class="check" name="checkedUser[<?= $listusr->get_user(); ?>]" value="<?= $listusr->get_user(); ?>" 
                                                                                                                    <?php if(isset($template)){
                                                                                                                        if($listusr->is_in_Items($template->get_id())) {
                                                                                                                            echo "checked = 'checked'" ;
                                                                                                                        }
                                                                                                                    };
                                                                                                                    ?> >
                    <?php else : ?> 
                        <!-- // sinon  s'il y a un message d'erreur on reçoit un array différent -->
                    <input  type="checkbox" class="check" name="checkedUser[<?= $listusr->get_user(); ?>]" value="<?= $listusr->get_user(); ?>" 
                            <?php if(in_array($listusr->get_user(), array_keys($checkedUser) ))
                                echo "checked= 'checked'";
                            ?> >
                            
                    
                    <?php endif;?>
                    <input  type="text" name="user"  value="<?php echo $listusr->getUserInfo(); ?>"  disabled="disabled">
                    <fieldset>
                        <legend>Weight</legend>
                        <input  type="number" name="weight[<?= $listusr->get_user() ; ?>]"min="0" placeholder="0"  
                            <?php  if (isset($template) && $listusr->is_in_Items($template->get_id())) { // si c'est un update et si l'utilisateur est dans les items
                                        $weight = $listusr->get_weight_by_user($template->get_id());
                                        echo "value=".($weight ?? 1);
                                    } else if (isset($combined_array[$listusr->get_user()])) {          // dans le cas s'il y a un message d'erreur alors on reçoit un array différent
                                        $weight = $combined_array[$listusr->get_user()];
                                        echo "value=".($weight ?? 1);
                                    }
                                ?> value="1">
                    </fieldset>
                </div>
                <br><br>
            <?php endforeach ; ?>

            <?php if (!empty($errors)): ?>
                <div class='errors'>
                    <p>Please correct the following error(s) :</p>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                        <li>
                            <?= $error ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <input type="submit" value="Save_template" id="btnSubmit">
        <?php else : ?>
                <p>You're alone. Don't be shy -> <a href="tricount/edit/<?php echo $tricount->get_id(); ?>"> ADD FRIENDS</a> ☻</p>
        <?php endif;?>
            
        <?php if(isset($templateID)){
            echo "<a href='templates/delete_template/$templateID'"; echo " id='delete_template'>DELETE</a>";
        }?>

        </div>

    </form>
    
</body>
</html>