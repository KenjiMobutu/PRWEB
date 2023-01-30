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

<style>
    body {
        font-family: Arial, sans-serif;
        font-size: 16px;
    }

    .cont {
    width: 80%;
    margin: 0 auto;
    text-align: center;
    }

    .edit-btn {
    background-color: #0f80e7;
    color: white;
    padding: 5px 10px;
    border-radius: 5px;
    display: inline-block;
    margin-top: 10px;
    }

    .btn {
    background-color: #4CAF50; /* Green */
    border: none;
    color: white;
    padding: 15px 32px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 4px 2px;
    cursor: pointer;
    border-radius: 5px;
    }

    .edit-btn:hover {
    background-color: #0a5fb3;
    cursor: pointer;
    }

    .view_expenses {
    margin-top: 20px;
    }

    h2 {
    font-size: 36px;
    margin-bottom: 10px;
    }

    p {
    margin-bottom: 10px;
    }

    table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    }

    th,
    td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
    }

    th {
    background-color: #0f80e7;
    color: white;
    }

    form {
    margin-top: 20px;
    display: inline-block;
    }

    input[type="submit"] {
    background-color: #0f80e7;
    color: white;
    padding: 5px 10px;
    border-radius: 5px;
    border: none;
    cursor: pointer;
    }

    input[type="submit"]:hover {
    background-color: #0a5fb3;
    }

    @media (max-width: 600px) {
    .cont {
        width: 90%;
    }
    }

</style>

<body>
    <?php include 'menu.html' ?>
    <div class="cont">
            <input type="hidden" name="tricount_id" value="<?php echo $tricount->get_id(); ?>" hidden>
            <input type="hidden" name="operation" value="<?php echo $operation_data->get_id(); ?>" hidden>
        <p><?php echo $tricount->get_title();?> > <?php echo $operation_data->title ?>  <button class="edit-btn">
            <a href="Operation/edit/<?php echo $operation_data->id ?>" style="text-decoration: none; color: black;">Edit</a>
        </button>
        <div class="view_expenses">   
        <h2><?php echo number_format($operation_data->amount, 2) ?></h2>
        <p>Paid by <?php echo $usr ?></p><p><?php echo $operation_data->operation_date ?></p>
        <p>For <?php echo $participants["0"] ?> participants, including me</p>    
        <?php echo $operation_data->title ?>
        
        <table>
            <thead>
                <tr>
                <th>User</th>
                <th>Debt</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($operationUsers as $user): ?>
                <tr>
                    <?php
                        $username = User::get_by_id($user['user']);
                        $debt = Operation::get_dette_by_operation($_GET['param1'],$user['user']);
                    ?>
                    <td><?php echo $username->getFullName() ?></td>
                    <td><?php echo number_format($debt['result'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <form action="operation/next_expense" method="post">
            <input type="hidden" name="tricount_id" value="<?php echo $tricount->get_id(); ?>" hidden>
            <input type="hidden" name="operation" value="<?php echo $operation_data->get_id(); ?>" hidden>
            <input class="btn" type="submit" name="submit" value="Previous">
        </form>
        <form action="operation/next_expense" method="post">
            <input type="hidden" name="tricount_id" value="<?php echo $tricount->get_id(); ?>" hidden>
            <input type="hidden" name="operation" value="<?php echo $operation_data->get_id(); ?>" hidden>
            <input class="btn" type="submit" name="submit" value="Next">
        </form>

    </div>


</body>
</html>