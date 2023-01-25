<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <title>Templates </title>
    <base href="<?= $web_root ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/style.css" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400&family=Sen:wght@400;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
</head>
<body>
    <?php include 'menu.html' ?>
    <div class="view_templates">
        <p><?php echo $tricount->get_title();?> > templates</p>

        <div class="templates_container">
            <table class="tab_templates">
                <?php if($templates !== null) : ?>
                    <?php foreach($templates as $rt) :?>
                            <tr>
                                <th><?= $rt->get_title(); ?></th>
                            </tr>
                            <tr>
                                <th class="info_templates">
                                    <ul>
                                        <?php foreach($items as $participe): ?>

                                            <!-- <?php echo '<pre>'; print_r($templates); echo '</pre>';?>  -->                    
                                                    <?php foreach($participe as $row) : ?>
                                                        <?php if($row->get_repartition_template() === $rt->get_id()): ?>
                                                           <li> <?php echo $row->get_user_info();?> 
                                                                    <?php echo "("; echo $row->get_weight_by_user($row->get_user(), $row->repartition_template);
                                                                        echo "/"; 
                                                                            echo $row->get_Sum_Weight();
                                                                                echo ")"; ?></li>
                                                        
                                                        <?php endif;?>

                                                    <?php endforeach ; ?>
                                                
                                                                                
                                        <?php endforeach;?>
                                    </ul>
                                </th>       
                                </tr>                 
                            
                        <?php endforeach; ?>
                        <?php else : ?>
                            <p>no template for now.</p>
                    <?php endif;?>
            </table>
        </div>
    </div>

</body>
</html>