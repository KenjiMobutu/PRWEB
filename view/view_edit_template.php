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
        <title>Edit Template</title>
</head>
<body>
<?php include 'menu.html' ?>
    <form action="templates/editTemplate" method="post" id="edit_template_form">
        <div class="edit_template_container">
        <?php if (count($listUser) > 1) : ?>

            <p class="edit_template_p">Title :</p>
            <input type="text" name="template_title" id="template_title" 
            value="<?php 
            if(isset($template))
                echo $template->get_title();
            ?>" required>
            <p class="edit_template_p">
            Template items :
            </p><br>
            <!-- pour récupérer l'id du tricount & template si reçu dans le submit du form -->
            <input type="text" name="tricountId" value="<?php echo $tricount->get_id(); ?>" hidden>
            <input type="text" name="templateID" value="<?php if(isset($_GET["param2"])){ echo $_GET["param2"];}  ?>" hidden>
            
            <?php foreach($listUser as $listusr): ?>
            <!-- mettre c[User->id] ça fera un tableau avec des données -->              <!-- check si c'est un edit t'emplate et récupère les items liés-->
            <div class="edit_template_items">
                <input  type="checkbox" name="c[<?= $listusr->get_user(); ?>]" value="<?= $listusr->get_user(); ?>" <?php if(isset($template)){
                                                                                                                if($listusr->is_in_Items($template->get_id())) {
                                                                                                                    echo "checked = 'checked'" ;} };?> >
                <input  type="text" name="user"  value="<?php echo $listusr->getUserInfo(); ?>"  disabled="disabled">
                <fieldset>
                    <legend>Weight</legend>
                    <input  type="number" name="w[<?= $listusr->get_user() ; ?>]"min="0" placeholder="0"  <?php if(isset($template)){if($listusr->is_in_Items($template->get_id())){echo "value=".$listusr->get_weight_by_user($template->get_id());}; }?> value="1">
                </fieldset>
            </div>
            <br><br>
            <?php if(isset($_POST['errors'])): echo $error; endif; ?>


        <?php endforeach ; ?>
            <input type="submit" value="Save_template">
        <?php else : ?>
                <p>You're alone. Don't be shy -> <a href="tricount/edit/<?php echo $tricount->get_id(); ?>"> ADD FRIENDS</a> ☻</p>
        <?php endif;?>
            
            <?php if(isset($_GET["param2"])){
                echo "<a href='templates/delete_template/$_GET[param2]'"; echo " id='delete_template'>DELETE</a>";
            }?>

        </div>

    </form>
    
</body>
</html>