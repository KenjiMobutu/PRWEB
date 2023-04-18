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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<style>
    ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .user-item {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
        padding: 10px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        background-color: white;
    }

    .user-info {
        flex-grow: 1;
        font-weight: bold;
        margin-right: 10px;
    }

    .balance-bar {
        width: 100%;
        height: 10px;
        border: none;
        border-radius: 5px;
    }

    .balance-bar::-webkit-meter-bar {
        background-color: #f5f5f5;
        border-radius: 5px;
    }

    .balance-bar::-webkit-meter-optimum-value {
        background-color: green;
        border-radius: 5px;
    }

    .balance-bar::-webkit-meter-suboptimum-value {
        background-color: yellow;
        border-radius: 5px;
    }

    .balance-bar::-webkit-meter-even-less-good-value {
        background-color: red;
        border-radius: 5px;
    }
</style>

<body>
    <?php include 'menu.html' ?>
    <div class="view_balance">

        <p>
            <?php echo $tricount->get_title(); ?> > Balance
        </p>

        <div>
            <canvas id="myChart"></canvas>
        </div>

        <div class="balance_container">
            <ul>
                <?php
                $max_balance = 1;
                $labels = [];
                $data = [];
                usort($users, function ($a, $b) {
                    return strcmp($a->getUserInfo(), $b->getUserInfo());
                });
                foreach ($users as $user):
                    $total_balance = 0;
                    $alberti_balance = Operation::total_alberti($tricount->get_id(), $user->get_user()); foreach ($operations_of_tricount as $operation):
                        if ($user->is_in_operation($operation->get_id()) || $user->getUserInfo() === $operation->getInitiator()) {
                            $total_balance += Operation::total_by_user($user->get_user(), $operation->get_id());
                        }
                    endforeach;
                    $balance = $alberti_balance - $total_balance;
                    if (abs($balance) > $max_balance) {
                        $max_balance = abs($balance);
                    }
                    array_push($labels, $user->getUserInfo());
                    array_push($data, $balance);
                endforeach;

                foreach ($users as $user):
                    $total_balance = 0;
                    $alberti_balance = Operation::total_alberti($tricount->get_id(), $user->get_user());
                    foreach ($operations_of_tricount as $operation):
                        if ($user->is_in_operation($operation->get_id()) || $user->getUserInfo() === $operation->getInitiator()) {
                            $total_balance += Operation::total_by_user($user->get_user(), $operation->get_id());
                        }
                    endforeach;
                    $balance = $alberti_balance - $total_balance;
                    $proportion = $balance / $max_balance * 100;
                    $user_info = $user->getUserInfo() == $_SESSION['user']->getFullName() ? $user->getUserInfo() . ' ( me )' : $user->getUserInfo();
                    $bar_style = $balance >= 0 ? 'background-color: green; color: white;' : 'background-color: red; color: white;';
                    echo '<li style="display: flex; align-items: center; justify-content: center; padding: 10px;">';
                    if ($balance >= 0) {
                        echo '<div style="text-align: left; margin-right: 10px;">' . $user_info . '</div>';
                    }
                    echo '<div><div style="width: ' . $proportion . '%; padding: 10px; border-radius: 5px; text-align: center; ' . $bar_style . '"><span style="display: flex; align-items: center;">' . number_format($balance, 2) . ' <span style="margin-left: 5px;">€</span></span></div></div>';
                    if ($balance < 0) {
                        echo '<div style="text-align: right; margin-left: 10px;">' . $user_info . '</div>';
                    }
                    echo '</li>';
                endforeach;
                ?>
            </ul>
        </div>

    </div>
    <script>
var ctx = document.getElementById('myChart').getContext('2d');
var myChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($labels); ?>,
        datasets: [{
            label: 'Balance',
            data: <?php echo json_encode($data); ?>,
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(255, 159, 64, 0.2)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        indexAxis:'y',
        scales: {
            y: {
                ticks: {
                    callback: function(value, index, values) {
                        return value + ' €';
                    }
                }
            }
        }
    }
});
</script>
</body>

</html>
