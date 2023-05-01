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
    <script src="lib/jquery-3.6.3.min.js" type="text/javascript"></script>
    <script src="lib/sweetalert2@11.js" type="text/javascript"></script>

    <script>

        let addSubscriberButton;
        let deleteSubscriberButton;
        let usersList;
        let addSubDropdown;
        let creator = "<?= $tricount->get_creator_id() ?>";

        const tricount_id = "<?= $tricount->get_id() ?>";

        let isDeletable = <?= json_encode($users_deletable) ?>;// users who participate but not deletable
        let user_JSON = <?= $users_json ?>; //users who not participate
        let subscribers_json = <?= $subscribers_json ?>; // users who participate
        let addingUser = false;
        console.log("ISDELETABLE O ---> "+isDeletable);
        

        async function addUser() {
            const id = $('#addSubDropdown option:selected').data('user-id');
            const userToAdd = user_JSON.find(el => el.id == id);

            if (subscribers_json.some(sub => sub.id === id)) {
                alert("User already subscribed!");
            } else {
                subscribers_json.push(userToAdd);
                user_JSON = user_JSON.filter(el => el.id != id);
                try {
                    await $.post("participation/add_service/" + tricount_id, { "names": id });
                    displayUserList();
                    //updateUserDeletability();
                } catch (e) {
                    showError("Error encountered while retrieving the users!");
                }
            }
        }

        async function deleteUser(id) {
            const userToDelete = subscribers_json.find(u => u.id == id);
            subscribers_json = subscribers_json.filter(u => u.id !== id);

            if (userToDelete) {
                user_JSON.push(userToDelete);
            }
            displayUserList();

            try {
                await $.post("participation/delete_service/" + tricount_id, { "userId": id });
            } catch (e) {
                showError("Error encountered while retrieving the users!");
            }
        }

        async function checkTitle() {
            const title = $(".tricountTitle").val();

            try {
                const data = await $.post("tricount/check_title/"+ title +"/"+ tricount_id);
                console.log(data + "   ->");

                if (data === "true") {
                    $("#errorTitle").show();
                    $("#errorTitle").html("Title existe déjà");
                    $(".addTricount_btn").hide();
                } else {
                    $("#errorTitle").html("");
                    $(".addTricount_btn").removeAttr("disabled");
                    $(".addTricount_btn").show();

                }
            } catch (e) {
                console.log("Error encountered while checking the title!");
            }
        }


        async function updateUserDeletability() {
            for (let u of subscribers_json) {
                isDeletable[u.id] = await checkUserDeletability(u.id);
                console.log(isDeletable);
            }
            displayUserList();
        }

        async function checkUserDeletability(userId) {
            try {
                const response = await fetch("user/handle_can_be_delete_request", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        userId: userId,
                        tricountId: tricount_id,
                        creator: creator
                    })
                });
                console.log("RESPONSE__> "+response);
                const data = await response.json();
                console.log("DATA__> "+data);
                return data.deletable;
            } catch (error) {
                console.log("Error retrieving user deletability: " + error);
                return false;
            }
        }


        function displayUserList() {
            const sortedSubscribers = sortUsers(subscribers_json);

            let html = "<ul class='edit-subscriberInput'>";
            for (let u of sortedSubscribers) {
                html += `
                <li>
                    <div class='infos_tricount_edit'>
                        <div class='name_tricount_edit'>
                            <input type='text' name='name' value='${u.full_name}${u.id == creator ? " (creator)" : ""}' disabled/>
                            ${isDeletable[u.id] && u.id !== creator ? `
                            <div class='trash_edit_tricount'>
                                <button class='btnDeleteSubscriber' onclick='deleteUser(${u.id})' style='background-color:transparent;'>
                                    <i class='bi bi-trash3'></i>
                                </button>
                            </div>` : ""}
                        </div>
                    </div>
                </li>`;
            }
            html += `
            </ul>
            <div class='add-subscriber-container'>
                <select id='addSubDropdown' data-id='addSubDropdown'>
                    <option selected disabled>--- Add user to tricount ---</option>`;

            const sortedUsers = user_JSON.sort((a, b) => a.full_name.localeCompare(b.full_name));
            for (let u of sortedUsers) {
                if (u.id != creator) {
                    html += `<option data-user-id='${u.id}' value='${u.id}'>${u.full_name}</option>`;
                }
            }

            html += `
                </select>
                <button class='btn btn-success' onclick='addUser()'>Add</button>
            </div>`;
            $('#usersList').html(html);
        }

        function sortUsers(users) {
            const creatorUser = users.find(el => el.id == creator);
            const otherUsers = users.filter(el => el.id != creator);

            otherUsers.sort((a, b) => a.full_name.localeCompare(b.full_name));

            return [creatorUser].concat(otherUsers);
        }

        function showError(message) {
            $('#usersList').html(`<tr><td>${message}</td></tr>`);
        }

        

    function showDeleteButton(){
        let deleteBtn ='<button class="it3DeleteButton" onclick="confirmDelete()"  style="background-color: red" color: white;"> delete Tricount';
        $('.button-delete-tricount').html(deleteBtn);
    }

    function confirmDelete() {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteExpense();
                    Swal.fire(
                        'Deleted!',
                        'Your tricount has been deleted.',
                        'success'
                    ).then(() => {
                        //redirect
                        window.location.href = 'tricount/index';
                    });
                }
        })
    }

    async function deleteExpense(tricount){
        try {
            await $.post("tricount/delete_service/" + tricount_id);
        } catch(e){
            console.log("Erreur : " + e);
        }
    }

    $(function() {
            usersList = $('#usersList');
            addSubDropdown = $('#addSubDropdown');
            addSubscriberButton = $('#btnAddSubscriber');
            addSubscriberButton.attr("type", "button");
            //addSubscriberButton.click(dropdownUserList);
            displayUserList();
            $('#subForm').hide();

            //$('.button-delete-tricount').hide();
            showDeleteButton();
            //updateUserDeletability();
            checkTitle();
        });
    </script>

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
            <input type="text" name="title" class="tricountTitle" onchange="checkTitle()" value='<?= $tricount->get_title() ?>'>
            <p id="errorTitle"></p>
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
        <div id="usersList" class="edit-subscriberInput">
            <!-- Boucle sur les souscriptions -->
            <?php foreach ($sub as $s): ?>
                <li>
                    <div class="infos_tricount_edit">
                        <!-- Nom de l'utilisateur -->
                        <div class="name_tricount_edit">
                            <!-- Indication que l'utilisateur est le créateur -->
                            <input id="subName"type="text" name="name" value="<?=($s->getUserId() == $tricount->get_creator_id() ? $s->getFullName()." (créateur)" : $s->getFullName())?>" disabled/>
                            <!-- Bouton de suppression (si autorisé) -->
                            <div class="trash_edit_tricount">
                                <?php if ($s->can_be_delete($tricount->get_id()) && $s->getUserId() != $tricount->get_creator_id()): ?>
                                    <form action="participation/delete/<?=  $tricount->get_id() ?>" method="POST">
                                        <input name="userId" value="<?= $s->getUserId() ?>" hidden />
                                        <button id="btnDeleteSubscriber" type="submit" style="background-color:transparent;">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </div>
            <!-- Formulaire d'ajout de souscripteurs -->
            <div id="subForm" >
                <form  action="participation/add/<?= $tricount->get_id() ?>" method="post">
                    <div  class="edit-selectSub">
                        <select class="selectSub" name="names" id="names">
                            <option value="">--Add a new subscriber--</option>
                            <?php foreach ($users as $u): ?>
                                <option id="subValue" value='<?= $u->getUserId() ?>'><?= $u->getFullName() ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button id="btnAddSubscriber">Add</button>
                    </div>
                </form>
            </div>
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
