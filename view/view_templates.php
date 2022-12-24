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
            <?php foreach($templates as $rt) :?>
                    <tr>
                        <!-- on récupère juste le titre  -->
                        <th><?= $rt->get_title(); ?></th>
                    </tr>
                        <th class="info_templates">
                            <ul>
                            <!-- on doit récupérer les noms des user -->
                                <?php foreach($items as $participe): ?>

                                    <!-- <?php echo '<pre>'; print_r($participe["user"][0]); echo '</pre>';?> mieux que var_dump imo  -->
                                    <li>
                                            <?php foreach($participe as $row) : ?>
                                                <?php echo $row->get_user_info(); ?> 
                                                    <?php if($row->get_repartition_template() === $row->repartition_template): ?>
                                                        <?php echo "("; echo $row->get_weight_by_user($row->get_user(), $row->repartition_template);
                                                             echo "/"; 
                                                                echo $row->get_Sum_Weight();
                                                                    echo ")"; ?>
                                                        <?php endif;?>
                                                        <!-- <?php echo '<pre>'; print_r($row->get_repartition_template() ===$row->repartition_template) ; echo '</pre>' ; ?> -->

                                            <?php endforeach ; ?>
                                        
                                                                         
                                    </li>
                                <?php endforeach;?>
                            </ul>
                        </th>
                        
                    
                <?php endforeach; ?>
            </table>
        </div>
    </div>

</body>
</html>