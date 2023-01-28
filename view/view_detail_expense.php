<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <title>Exepenses </title>
    <base href="<?= $web_root ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/style.css" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400&family=Sen:wght@400;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link rel="stylesheet" href="path/to/font-awesome/css/font-awesome.min.css">
</head>


<body>
    <?php include 'menu.html' ?>
    <div class="cont">
    <p><?php echo $tricount->get_title();?> > <?php echo $operation_data->title ?>  <button class="edit-btn">
            <a href="Operation/edit/<?php echo $operation_data->id ?>" style="text-decoration: none; color: black;">Edit</a>
        </button>
        <?php
            // echo '<pre>';
            // print_r($operation_data);
            // echo '</pre>';
            // die();
            ?>
        <div class="view_expenses">   
        <h2><?php echo $operation_data->amount ?></h2>
        <p>Paid by <?php echo $usr ?></p><p><?php echo $operation_data->operation_date ?></p>
        <p>For <?php echo $participants["0"] ?> participants, including me</p>    
        <?php echo $operation_data->title ?>
        </div>
    </div>
    

</body>
</html>