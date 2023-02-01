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
                
                   <?php
                   $total_usr = 0;
                   $tot = intval($total["sum(amount)"]);
                        foreach($users as $user):  
                            $total_usr = 0;
                            foreach($operations_of_tricount as $operation):
                                // print_r($operation); die();
                                if($user->is_in_operation($operation->get_id()) || $user->getUserInfo() === $operation->getInitiator())
                                    $total_usr+=Operation::total_by_user($user->get_user(),$operation->get_id());

                            endforeach;
                            //le total c'est faux mais mieux que rien
                            echo '<p>' . $user->getUserInfo() . '  ' . $tot-$total_usr . '</p>';
                        endforeach;
                       
                   ?>

                <br>
            </ul>
        </div>
    </div>

</body>
</html>