<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <title>Balance </title>
    <base href="<?= $web_root ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/style.css" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400&family=Sen:wght@400;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
</head>
<body>
    <?php include 'menu.html' ?>
    <div class="view_balance">
             
        <p><?php echo $tricount->get_title();?> > Balance</p>
        
        <div class="balance_container">
             <ul>
            <!-- on doit récupérer les noms des user et leur solde / dette --> -->
           
                <?php foreach($weights as $weight): ?>
                    <li>
                    <?php
                    $id = $weight["user"];
                    $users = User::get_by_id($id);
                   
                    if($weight["user"] == $tricount->get_creator_id()){
                        echo "<b>user  ". $users->getFullName() . " avec une  weight de " .  $weight["weight"] . "</b>";
                    } 
                    else
                    echo "user  ".  $users->getFullName() . " avec une  weight de " .  $weight["weight"];
                    
                    ?>
                    </li>
                    
                <?php endforeach;?>
                <br>
                <p>Total is : <?php echo $total["0"] ?></p>
            </ul>
        </div>
    </div>

</body>
</html>