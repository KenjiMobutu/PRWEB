<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link href="css/style.css" rel="stylesheet" type="text/css" />
        <link
            href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400&family=Sen:wght@400;700;800&display=swap"
            rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <title>edit profile</title>
</head>
<body>
<?php include 'menu.html' ?>
<div class="prf-body">
    <div class="title">
            <?= $user->getFullName() ?>
    </div>
    <div class="edit_profile">
        <div class="chprof-form-items">
            
        </div>
    </div>
</div>

</body>
</html>