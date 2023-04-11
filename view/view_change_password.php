<!doctype html>
<html lang="en">



    <head>

        <meta charset="UTF-8">

        <base href="<?= $web_root ?>" />

        <meta name="viewport" +
            content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">

        <meta http-equiv="X-UA-Compatible" content="ie=edge">

        <link href="css/style.css" rel="stylesheet" type="text/css" />
        <link href="css/ch-pass.css" rel="stylesheet" type="text/css" />
        <title>Change Password</title>

    </head>

    <?php include('menu.html'); ?>

    <script>
        function togglePasswordVisibility(passwordInputId) {
            var passwordField = document.getElementById(passwordInputId);

            if (passwordField.type === "password") {
                passwordField.type = "text";
            } else {
                passwordField.type = "password";
            }
        }
    </script>

    <body>
        </main>

        <div class="chpass_page">
            <div class="chpass-container">
                <form class="chpass-form" method="POST" enctype="multipart/form-data">
                    <div class="chpass-title">
                        <h2 style="color:black">Change password for
                            <?= $user->getFullName() ?>
                        </h2>
                    </div>
                    <div class="password-input">
                        <input class="chpass-form-items" type="password" name="currentPassword" id="currentPassword"
                            value="<?= $currentPasswordValue ?>" placeholder="Current Password" required>
                        <button type="button" onclick="togglePasswordVisibility('currentPassword')">Show
                            Password</button>
                    </div>
                    <div class="password-input">
                        <input class="chpass-form-items" type="password" placeholder="New Password" name="newPassword" +
                            id="newPassword" value='<?= $newPasswordValue ?>' required>
                        <button type="button" onclick="togglePasswordVisibility('newPassword')">Show Password</button>
                    </div>
                    <div class="password-input">
                        <input class="chpass-form-items" type="password" placeholder="Confirm Password" +
                            name="confirmPassword" id="confirmPassword" value='<?= $confirmPasswordValue ?>' required>
                        <button type="button" onclick="togglePasswordVisibility('confirmPassword')">Show
                            Password</button>
                    </div>
                    <input class="chpass-form-btn" type="submit" value="Save">

                </form>

                <?php if (count($errors) != 0): ?>
                    <div class='errors'>
                        <p>Please correct the following error(s) :</p>
                        <ul>

                            <?php foreach ($errors as $error): ?>
                                <li>
                                    <?= $error ?>
                                </li>

                            <?php endforeach; ?>

                        </ul>
                    </div>
                <?php elseif (strlen($success) != 0): ?>
                    <p><span class='success'>
                            <?= $success ?>
                        </span></p>

                <?php endif; ?>

            </div>
        </div>

    </body>



</html>