<!doctype html>
<html lang="en">

<head>
    <base href="<?= $web_root ?>" />
    <meta charset="UTF-8">
    <title><?= $user->getFullName() ?>'s Tricount!</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="css/style.css" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400&family=Sen:wght@400;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
</head>

<body>
<?php include 'menu.html' ?>
    <div class="title"><?= $user->getFullName() ?>'s Tricount!</div>
        <div class="profile">
            <div class="name">
                <p>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Vitae facilis blanditiis, eius odit consequuntur omnis.</p>
                <h1>PROFILEEEEEEEEEEEEEE</h1>
                <p>fullname : </p><?php echo $user->getFullName(); ?>
                <p>role : </p><?php echo $user->getRole(); ?>
                <p>iban : </p><?php echo $user->getUserIban(); ?>
                <p>user id : </p><?php echo $user->getUserId(); ?> 
                <!-- LE ID S'AFFICHE QUE SI ON LOGOUT ET RE LOGIN pas besoin de lui la mais pour savoir-->

            </div>

    </div>
    
</body>

</html>