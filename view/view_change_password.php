<!doctype html>
<html lang="en">



    <head>

        <meta charset="UTF-8">

        <base href="<?= $web_root ?>" />

        <meta name="viewport" +
            content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">

        <meta http-equiv="X-UA-Compatible" content="ie=edge">

        <link href="css/style.css" rel="stylesheet" type="text/css" />
        <title>Change Password</title>

    </head>

    <?php include('menu.html'); ?>

<style>
    
  .chpass_page {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
  }

  .chpass-container {
    background-color: #f2f2f2;
    padding: 40px;
    border-radius: 10px;
    box-shadow: 0px 0px 10px #ccc;
    width: 500px;
    text-align: center;
  }

  .chpass-title h2 {
    margin-bottom: 30px;
  }

  .form-floating {
    margin-bottom: 30px;
  }

  .chpass-form-items {
    width: 100%;
    padding: 10px;
    margin-bottom: 20px;
    border: none;
    border-bottom: 2px solid #ccc;
    font-size: 16px;
  }

  .chpass-form-btn {
    background-color: #4CAF50;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
  }

  .errors {
    color: red;
    margin-top: 20px;
  }

  .success {
    color: green;
  }
  @media (max-width: 550px) {
        .chpass-container {
            width: 90%;
        }
    }
    .chpass-title {
        margin-bottom: 2rem;
    }
    .chpass-form {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-top: 2rem;
    }
    .chpass-form-items {
        width: 100%;
        padding: 1rem;
        margin-bottom: 1rem;
        font-size: 1.2rem;
        border-radius: 5px;
        border: 1px solid #ddd;
    }
    .chpass-form-btn {
        background-color: #3897f0;
        color: #fff;
        padding: 1rem;
        margin-top: 1rem;
        border-radius: 5px;
        border: none;
        cursor: pointer;
        font-size: 1.2rem;
    }
    .errors {
        color: red;
        margin-top: 1rem;
    }
    .success {
        color: green;
        margin-top: 1rem;
    }

</style>

    <body>
        </main>

        <div class="chpass_page">
            <div class="chpass-container">
                <form class="chpass-form" method="POST" enctype="multipart/form-data">
                    <div class="chpass-title">
                        <h2 style="color:black">Change password for <?= $user->getFullName() ?>
                        </h2>
                    </div>
                    <div class="form-floating">

                        <?php echo "<input class='chpass-form-items' type='password' name='currentPassword' id='currentPassword' placeholder='Current Password'>"; ?>

                    </div>
                    <input class="chpass-form-items" type="password" placeholder="New Password" name="newPassword" +
                        id="newPassword" required>
                    <input class="chpass-form-items" type="password" placeholder="Confirm Password" +
                        name="confirmPassword" id="confirmPassword" required>
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