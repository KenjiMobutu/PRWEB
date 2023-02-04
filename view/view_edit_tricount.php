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
    <meta name="viewport"
        content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="icon" type="image/png" href="assets/img/favicon.png" sizes="32x32">
    <link rel="apple-touch-icon" sizes="180x180" href="assets/img/icon/192x192.png">
    <link rel="stylesheet" href="css/style.css">
    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>

<body>
    <div class="appHeader">
        <div class="left">
            <a href="#" class="headerButton goBack">
                <i class="bi bi-arrow-left"></i>
            </a>
        </div>
        <div class="pageTitle">
            <?= $tricount->get_title() ?> <i class="bi bi-caret-right-fill" style="font-size: 1em;"></i> Edit
        </div>
        <!-- Formulaire de mise à jour du Tricount -->
        <form id="updateTricount" action="tricount/update/<?= $tricount->get_id() ?>" method="post">
            <div class="right">
                <button type="submit" value="add" class="addTricount_btn">
                    <i class="bi bi-save"></i>
                </button>
            </div>

    </div>

    <!-- Bloc de modification du Tricount -->
    <div class="edit-tricount">
        <div class="edit-settingsTitle">
            <h1>Settings</h1>
        </div>
        <div class="edit-settingsInput">
            <h2>Title :</h2>
            <input type="text" name="title" value='<?= $tricount->get_title() ?>'>
            <h2>Description (optional) :</h2>
            <input type="text" name="description"
                value='<?= $tricount->get_description() == null ? "No description" : $tricount->get_description() ?>'>

                <?php if (count($errors) != 0): ?>
                            <div class='errors'>
                                <br><br><p>Please correct the following error(s) :</p>
                                <ul>
                                    <?php foreach ($errors as $error): ?>
                                        <li><?= $error ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
        </div>
</form>
        <!-- Souscriptions au Tricount -->
        <div class="edit-settingsTitle">
            <h1>Subscriptions</h1>
        </div>
        <div class="edit-subscriberInput">
            <!-- Boucle sur les souscriptions -->
            <?php foreach ($sub as $s): ?>
                <li>
                    <div class="infos_tricount_edit">
                        <!-- Nom de l'utilisateur -->
                        <div class="name_tricount_edit">
                            <!-- Indication que l'utilisateur est le créateur -->
                            <input type="text" name="name" value="<?=($s->getUserId() == $tricount->get_creator_id() ? $s->getFullName()." (créateur)" : $s->getFullName())?>"  disabled/>
                        <!-- Bouton de suppression (si autorisé) -->
                        <div class="trash_edit_tricount">
                            <?php if ($s->can_be_delete($tricount->get_id()) && $s->getUserId() != $tricount->get_creator_id()): ?>
                                <form action="participation/delete/<?=  $tricount->get_id() ?>" method="POST">
                                    <input name="userId" value="<?= $s->getUserId() ?>" hidden />
                                    <button type="submit" style="background-color:transparent;">
                                        <i type="submit"class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </div>
            <!-- Formulaire d'ajout de souscripteur -->
            <form id="addSubscriber" action="participation/add/<?= $tricount->get_id() ?>" method="post">
                <div class="edit-selectSub">
                    <select class="selectSub" name="names" id="names">
                        <option value="">--Add a new subscriber--</option>
                        <?php foreach ($users as $u): ?>
                            <option value='<?= $u->getUserId() ?>'><?= $u->getFullName() ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button>Add</button>
                </div>
            </form>
            <div class="buttons_edit_tricount">
                <div class="button-manage-repartition-template">
                    <form action="templates/templates/<?= $tricount->get_id() ?>">
                        <button class="manage-tricount" type="submit">Manage Template</button>
                    </form>
                </div>
                <div class="button-delete-tricount">
                    <form action="tricount/delete/<?= $tricount->get_id() ?>" method="post">
                        <button class="delete-tricount" type="submit">Delete This Tricount</button>
                    </form>
                </div>
            </div>
            <!-- JavaScript Bundle with Popper -->
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
                integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
                crossorigin="anonymous">
                </script>
            <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
            <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
            <!-- Splide -->
            <script src="css/src/splide/splide.min.js"></script>
            <!-- Base Js File -->
            <script src="css/src/js/base.js"></script>
</body>

</html>
