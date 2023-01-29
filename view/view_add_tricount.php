<!doctype html>
<html lang="en">
    <head>
        <base href="<?= $web_root ?>" />
        <meta charset="UTF-8">
        <title>
            <?= $user->getFullName() ?>'s Tricount!
        </title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
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
    <form id="addTricount" action="tricount/add" method="post">
        <div class="appHeader">
            <div class="left">
                <a href="#" class="headerButton goBack">
                <i class="bi bi-arrow-left"></i>
                </a>
            </div>
            <div class="pageTitle">
                Add new Tricount
            </div>
            <div class="right">
                <button type="submit" value="add" class="addTricount_btn">
                    <i class="bi bi-save"></i>
                </button>
            </div>
        </div>

    <!-- * tricount block -->

    <div class="section mt-2 mb-2">
            <div class="card">
                <div class="card-body">
                        <div class="form-group boxed">
                            <div class="input-wrapper">
                                <label class="label" for="text4b">Title</label>
                                <input type="text" name="title" class="form-control" id="text4b" placeholder="Your Title here!">
                            </div>
                        </div>

                        <div class="form-group boxed">
                            <div class="input-wrapper">
                                <label class="label" for="textarea4b">Description (optional)</label>
                                <textarea id="textarea4b" name="description" rows="2" class="form-control" placeholder="Your description here!"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>



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
