<!doctype html>
<html lang="en">

    <head>
        <base href="<?= $web_root ?>" />
        <meta charset="UTF-8">
        <title>
            <?= $user->getFullName() ?>'s Tricount!
        </title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <link rel="icon" type="image/png" href="assets/img/favicon.png" sizes="32x32">
        <link rel="apple-touch-icon" sizes="180x180" href="assets/img/icon/192x192.png">
        <link rel="stylesheet" href="css/style.css">
        <!-- CSS only -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    </head>

    <body>
        <div class="appHeader">
            <div class="left">
                <a href="#" class="headerButton goBack">
                    <ion-icon name="chevron-back-outline"></ion-icon>
                </a>
            </div>
            <div class="pageTitle">
                Your tricount
            </div>
            <div class="right">
                <a href="tricount/add" class="headerButton goBack" >
                    <ion-icon name="add-outline"></ion-icon>
                </a>
            </div>
        </div>
        <div id="listTricount">
                <div class="section mt-2">
                    <!-- tricount block -->
                <?php foreach ($tricounts_list as $tl):  ?>
                    <div class="card-block mb-2">
                        <form action="tricount/edit/<?= $tl->id?>" method="POST">
                        <button class="button-card" >
                        <div class="card-main">
                            <div class="balance">
                                <span class="label"><?=$tl->title ?></span>
                                <h1 class="title"><?= $tl->description  == null ? "No description" : $tl->description ?></h1>
                            </div>
                            <div class="in">
                                <div class="card-number">
                                    <span class="label"><?php echo $tl->number_of_friends($tl->id) == 0 ? "you're alone!" :"with ". $tl->number_of_friends($tl->id)." friends" ?> </span>
                                </div>
                            </div>
                            <input type='text' name="id" id="id" value="<?= $tl->id ?>" hidden >
                        </div>
                        </button>
                        </form>
                    </div>
                <?php endforeach; ?>
                </div>
        </div>
    <!-- * tricount block -->

        <!-- JavaScript Bundle with Popper -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
        <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
        <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
        <!-- Splide -->
        <script src="css/src/splide/splide.min.js"></script>
        <!-- Base Js File -->
        <script src="css/src/js/base.js"></script>
    </body>

</html>
