<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <title>Balance </title>
    <base href="<?= $web_root ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/style.css" rel="stylesheet" type="text/css" />
    <link href="css/balance.css" rel="stylesheet" type="text/css" />
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400&family=Sen:wght@400;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
</head>

<body>
    <?php include 'menu.html' ?>
    <div class="view_balance">

        <p>
            <?php echo $tricount->get_title(); ?> > Balance
        </p>

        <div class="balance_container">
            <ul>
                <?php
                $total_usr = 0;
                $tot = intval($total["sum(amount)"]);
                $max_balance = 0;
                foreach ($users as $user):
                    $alb = Operation::total_alberti($tricount->get_id(), $user->get_user());
                    $total_usr = 0; foreach ($operations_of_tricount as $operation):
                        if ($user->is_in_operation($operation->get_id()) || $user->getUserInfo() === $operation->getInitiator())
                            $total_usr += Operation::total_by_user($user->get_user(), $operation->get_id());
                    endforeach;
                    $balance = $alb - $total_usr;
                    if (abs($balance) > $max_balance) {
                        $max_balance = abs($balance);
                    }
                endforeach;
                foreach ($users as $user):
                    $alb = Operation::total_alberti($tricount->get_id(), $user->get_user());
                    $total_usr = 0; foreach ($operations_of_tricount as $operation):
                        if ($user->is_in_operation($operation->get_id()) || $user->getUserInfo() === $operation->getInitiator())
                            $total_usr += Operation::total_by_user($user->get_user(), $operation->get_id());
                    endforeach;
                    $balance = $alb - $total_usr;
                    if ($balance >= 0) {
                        $bar_width = ($balance / $max_balance) * 50 . "%";
                        echo '<li>' . $user->getUserInfo() . '<div style="width: ' . $bar_width . '; background-color: green; color: white; display: inline-block;">' . number_format($balance, 2) . '</div></li>';
                    } else {
                        $bar_width = (abs($balance) / $max_balance) * 50 . "%";
                        echo '<li><div style="width: ' . $bar_width . '; background-color: red; color: white; display: inline-block;">' . number_format($balance, 2) . '</div>' . $user->getUserInfo() . '</li>';
                    }
                endforeach;
                ?>
            </ul>
        </div>
    </div>
</body>

</html>