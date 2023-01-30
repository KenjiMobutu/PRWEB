<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <title>Expenses </title>
    <base href="<?= $web_root ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/style.css" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400&family=Sen:wght@400;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link rel="stylesheet" href="path/to/font-awesome/css/font-awesome.min.css">
</head>
<style>
          /* General styles */
        .data-item {
        list-style: none;
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        padding: 0;
        }

        .data-card {
        width: 300px;
        height: 150px;
        background-color: #ffffff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
        text-align: center;
        margin: 20px;
        overflow: hidden;
        transition: 0.3s;
        }

        .data-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 6px 10px rgba(0, 0, 0, 0.1);
        }

        .data-card .title {
        font-size: 20px;
        margin: 20px 0 10px;
        color: #333333;
        }

        .data-card .amount {
        font-size: 25px;
        font-weight: bold;
        color: #FF5722;
        margin-bottom: 10px;
        }

        .data-card .initiator {
        font-size: 16px;
        color: #9E9E9E;
        margin-bottom: 10px;
        }

        .data-card .date {
        font-size: 16px;
        color: #9E9E9E;
        }

        /* View balance button */
        .view-balance-button {
        background-color: #00BFA5;
        color: #FFFFFF;
        border: none;
        border-radius: 20px;
        padding: 10px 20px;
        font-size: 16px;
        cursor: pointer;
        transition: 0.3s;
        margin-bottom: 20px;
        display: flex;
        justify-content: center;
        align-items: center;
        display: block;
                    margin: 0 auto;
                    text-align: center;
        }

        .view-balance-button:hover {
        background-color: #008E76;
        }

        .view-balance-button i {
        margin-right: 10px;
        }

        /* Totals section */
        .totals {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 50px;
        width: 100%;
        }

        .totals .exp {
        font-size: 20px;
        color: #333333;
        text-align: center;
        width: 33.33%;
        }

        .totals .mytot {
        font-size: 20px;
        color: #333333;
        text-align: center;
        width: 33.33%;
        }

        .totals .add-btn {
        width: 33.33%;
        display: flex;
        justify-content: center;
        align-items: center;
        }

        .add-button {
        background-color: #00BFA5;
        border-radius: 50%;
        width: 60px;
        height: 60px;
        border: none;
        display: flex;
        justify-content: center;
        align-items: center
        }

        p{
            color:yellow;
        }




    @media screen and (min-width: 320px) and (max-width: 480px) {
        .view-balance-button {
            display: block;
            margin: 0 auto;
            text-align: center;
            background-color: green;
            color: white;
            padding: 10px 20px;
            border-radius: 4px;
            border: none;
            font-size: 18px;
            width: 70%;
            cursor: pointer;
        }

        .container {
            width: 90%;
            margin: 0 auto;
            margin-left:-10px;
        }
    }

</style>
<?php var_dump($nbOperations);
var_dump($participants); ?>
<body>
    <?php include 'menu.html' ?>
    <div class="cont">
        <div class="view_expenses">
            <button class="edit-btn">
                <a href="tricount/edit/<?= $tricount->get_id()?>" style="text-decoration: none; color: black;">Edit</a>
            </button>
                <p><?php echo $tricount->get_title();?> > Expenses</p>

                        <ul>
                        <div class="container">
                            <ul class="data-list">
                            <?php
                                 if(!empty($amounts)){
                                     foreach($amounts as $amount):
                                         if(!empty($amount)){
                                            echo '<a href="/prwb_2223_c03/Operation/balance/'.$tricount->get_id().'">';
                                                                                                echo '<button class="view-balance-button">';
                                                                                                    echo '<i class="fa fa-exchange"></i>View Balance';
                                                                                                echo '</button>';
                                                                                            echo '</a>';
                                                                                            echo '<li class="data-item">';
                                                                                        foreach($amount as $am):
                                                                                            $id=$am->initiator;
                                                                                            $id_expense = $am->id;
                                                                                            echo '<a href="Operation/detail_expense/'.$id_expense.'">
                                                                                                <div class="data-card">
                                                                                                        <h2 class="title">'.$am->title.'</h2>
                                                                                                        <input type="hidden" name="operationId" value="$id_expense">
                                                                                                        <p class="amount">'.$am->amount.' €</p>
                                                                                                        <p class="initiator">Paid by '.$am->getInitiator().'</p>
                                                                                                        <p class="date">'.$am->operation_date.'</p>
                                                                                                </div>
                                                                                            </a>';
                                                                                                endforeach;
                                                                                           echo '</li>';
                                                                                            echo '<div class="totals">
                                                                                            <div class="mytot">
                                                                                                <p>MY TOTAL <br> '.round($mytot["0"]).' €</p>                  
                                                                                             </div>
                                                                                             <div class="add-btn">
                                                                                                                                                 <a href="/prwb_2223_c03/Operation/add/'.$tricount->get_id().'">   
                                                                                                                                                     <button class="add-button">+
                                                                                                                                                     </button>
                                                                                                                                                 </a>
                                                                                                                                             </div>
                                                                                                                                             <div class="exp">
                                                                                                                                                 <p>TOTAL EXPENSES <br> '.round($totalExp["0"]).' €</p>                    
                                                                                                                                             </div>
                                                                                                                                         </div>';
                                                                                                                                    } 
                                                                                                                             
                                                                                                                                 if(empty($amount) && ($participants >0)) {
                                                                                                                                     echo "<h1 style='text-align:center;'>this is empty :(</h1>";
                                                                                                                                         echo '<a href="/prwb_2223_c03/Operation/add/'.$tricount->get_id().'">';
                                                                                                                                                 echo '<button class="view-balance-button">';
                                                                                                                                                     echo 'ADD AN EXPENSE';
                                                                                                                                                 echo '</button>';
                                                                                                                                             echo '</a>';
                                                                                                                                             echo '<div class="totals">
                                                                                                                                             <div class="mytot">
                                                                                                                                             <p>MY TOTAL <br> 0 €</p>                  
                                                                                                                                           </div>
                                                                                                                                             <div class="exp">';
                                                                                                                                             echo '<p>TOTAL EXPENSES <br> 0 €</p>';
                                                                                                                                         echo '</div>
                                                                                                                                           </div>';
                                                                                                                                     }if(empty($amount) && ($participants == 0)) {
                                                                                                                                         echo "<h1 style='text-align:center;'>you are alone loser :(</h1>";
                                                                                                                                        echo '<a href="'.$tricount->get_id().'">';
                                                                                                                                                 echo '<button class="view-balance-button">';
                                                                                                                                                     echo 'ADD FRIENDS';
                                                                                                                                                 echo '</button>';
                                                                                                                                             echo '</a>';
                                                                                                                                             echo '<div class="totals">
                                                                                                                                             <div class="mytot">
                                                                                                                                                 <p>MY TOTAL <br> 0 €</p>                  
                                                                                                                                             </div>
                                                                                                                                             <div class="add-btn">
                                                                                                                                                 <a href="/prwb_2223_c03/Operation/add/'.$tricount->get_id().'">    
                                                                                                                                                     <button class="add-button">+
                                                                                                                                                     </button>
                                                                                                                                                 </a>
                                                                                                                                             </div>
                                                                                                                                             <div class="exp">
                                                                                                                                             echo <p>TOTAL EXPENSES <br> 0 €</p>;              
                                                                                                                                          </div>
                                                                                                                                          </div>';
                                        }endforeach;
                                    }
                                    ?>
                            </ul>

                        </div>
                        </ul>
            </div>

            </div>
    </div>


</body>
</html>
