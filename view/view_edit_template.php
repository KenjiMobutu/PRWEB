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
    <style>
        form {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background-color: #2c3e50;
            color: white;
            font-family: Arial, sans-serif;
        }

        p {
            margin-right: 10px;
        }

         
        input[type="text"] {
            padding: 10px;
            margin: 10px;
            border-radius: 5px;
            border: none;
            background-color: #34495e;
            color: white;

        }
        input[type="number"] {
            flex: 1;
            padding: 10px;
            margin: 1px;
            border-radius: 15px;
            border: none;
            background-color: #34495e;
            color: white;
        }
       

        input[type="submit"] {
            margin: 20px;
            padding: 10px 20px;
            border-radius: 5px;
            border: none;
            background-color: #16a085;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #1abc9c;
        }
        @media screen and (min-width: 10px) and (max-width: 480px) {
            input[type="text"], input[type="number"], input[type="checkbox"] {
                width: 25%;
            }
            input[type="submit"] {
                width: 100%;
            }
        }

    </style>
<?php include 'menu.html' ?>


<h1></h1>
    <form action="templates/editTemplate" method="post">
        <p>Title :</p>
        <input type="text" name="template_title" id="template_title" 
        value="<?php 
        if(isset($template))
            echo $template->get_title();
        ?>" required>
        <p>

        <!-- <?php //echo '<pre>'; print_r($listItems); echo '</pre>';?> -->
        Template items :
        </p><br>


<!-- pour récupérer l'id du tricount & template si reçu dans le submit du form -->
        <input type="text" name="tricountId" value="<?php echo $tricount->get_id(); ?>" hidden>
        <input type="text" name="templateID" value="<?php if(isset($_GET["param2"])){ echo $_GET["param2"];}  ?>" hidden>
        <?php foreach($listUser as $listusr): ?>

           <!-- mettre c[User->id] ça fera un tableau avec des données -->              <!-- check si c'est un edit t'emplate et récupère les items liés-->
            <input type="checkbox" name="c[<?= $listusr->user; ?>]" value="<?= $listusr->user; ?>" <?php if(isset($template)){
                                                                                                            if($listusr->is_in_Items($template->get_id())) {
                                                                                                                echo "checked = 'checked'" ;} };?> >
            <input type="text" name="user" value="<?php $listusr->user;?>" placeholder="<?php echo $listusr->getUserInfo(); ?>"  disabled="disabled">
            <fieldset>
                <legend>Weight</legend>
                <input type="number" name="w[<?= $listusr->user; ?>]"min="0"  <?php if(isset($template)){
                                                                                    if($listusr->is_in_Items($template->get_id())) {
                                                                                        echo "value=".$listusr->get_weight_by_user($template->get_id());}; }else echo "value=1";?>>
            </fieldset>
                            <?php // mettre w[User->id] ça fera un tableau avec des données?>
            <br><br>
            <?php // récupérer les donnée grace a l'id user.?>

        <?php endforeach ; ?>
        <input type="submit" value="Save_template">
    </form>

    
</body>
</html>