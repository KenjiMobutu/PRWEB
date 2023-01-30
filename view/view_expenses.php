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

<<<<<<< HEAD
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
=======
                .view-balance-button:hover {
                    /* Change button elevation on hover */
                    transform: translateY(-2px);
                    box-shadow: 2px 4px 8px rgba(0, 0, 0, 0.2);
                }

                .view-balance-button:active {
                    /* Change button elevation when active */
                    transform: translateY(2px);
                    box-shadow: 0px 0px 0px rgba(0, 0, 0, 0.2);
                }



                .container {
                width: 90%;
                margin: 0 auto;
            }

            .amount{
                margin-top: -12px;
                float : right;
            }

            .date{
                margin-top: -18px;
                float:right;
            }

            .data-list {
                display: flex;
                flex-wrap: wrap;
                list-style: none;
                padding: 0;
            }

            .data-item {
                flex: 1;
                margin: 10px;
                border: 1px solid #ccc;
                border-radius: 10px;
                padding: 20px;
            }

            .data-card {
                margin-bottom: 20px;
                border-bottom: 2px solid grey;

            }

            .title {
                font-size: 20px;
                font-weight: bold;
                margin-bottom: 10px;
            }

            .amount {
                font-size: 18px;
                margin-bottom: 10px;
            }

            .initiator {
                font-size: 16px;
                margin-bottom: 10px;
            }

            .date {
                font-size: 14px;
            }

            .exp{
                float:right;
                margin-right:150px;
                background-color:grey;
                padding-left:15%;
            }

            .mytot{
                margin-top:-40px;
                padding-right:15%;
                float:left;
                margin-left:150px;
                background-color:grey;
            }


        .add-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left:20%;
>>>>>>> origin/feat_Kenji
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
<<<<<<< HEAD
        background-color: #00BFA5;
        border-radius: 50%;
        width: 60px;
        height: 60px;
        border: none;
        display: flex;
        justify-content: center;
        align-items: center
=======
            background-color: blue;
            color: white;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            font-size: 20px;
            cursor: pointer;
>>>>>>> origin/feat_Kenji
        }

        p{
            color:yellow;
        }




    @media screen and (min-width: 320px) and (max-width: 480px) {
<<<<<<< HEAD
        .view-balance-button {         
            display: block;
            margin: 0 auto;
            text-align: center;
=======
        .view-balance-button {

>>>>>>> origin/feat_Kenji
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

<body>
    <?php include 'menu.html' ?>
    <div class="cont">
<<<<<<< HEAD
        <div class="view_expenses">     
            <button class="edit-btn">
                <a href="https://www.example.com/edit" style="text-decoration: none; color: black;">Edit</a>
            </button>
                <p><?php echo $tricount->get_title();?> > Expenses</p>  
            
                      
                    <!-- <a href="/prwb_2223_c03/Operation/balance/<?php echo $tricount->get_id()?>">    
=======
        <div class="view_expenses">
        <button class="edit-btn">
            <a href="tricount/edit/<?= $tricount->get_id()?>" style="text-decoration: none; color: black;">Edit</a>
        </button>

                    <p><?php echo $tricount->get_title();?> > Expenses</p>
                    <a href="/prwb_2223_c03/Operation/balance/<?php echo $tricount->get_id()?>">
>>>>>>> origin/feat_Kenji
                        <button class="view-balance-button">
                            <i class="fas fa-dollar-sign"></i>View Balance
                        </button>
                    </a> -->
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
<<<<<<< HEAD
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
                                
                                    if(empty($amount) && ($participants > 1)) {
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
                                        }if(empty($amount) && ($participants == 1)) {
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
=======
                                            </a>';
                                            endforeach;
                                        echo '</li>';
                                    endforeach;
                                else:
                                    echo '<p>dommage</p>';
                                endif;?>
>>>>>>> origin/feat_Kenji
                            </ul>

                        </div>
                        </ul>
<<<<<<< HEAD
        </div>
=======
            </div>
            <div class=totals>
                <div class="exp">
                    <p>TOTAL EXPENSES <br> <?php echo round($totalExp["0"]) . "$"?></p>
                </div>
                <div class="add-btn">
                <a href="/prwb_2223_c03/Operation/add/<?php echo $tricount->get_id()?>">
                        <button class="add-button">

                        </button>
                    </a>
                </div>
                <div class="mytot">
                    <p>MY TOTAL <br> <?php echo round($mytot["0"]) . "$"?></p>
                </div>
            </div>

            </div>
>>>>>>> origin/feat_Kenji
    </div>


</body>
</html>
