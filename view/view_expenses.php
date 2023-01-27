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

            .cont{
                margin:25px;
            }
            .view_expenses{
                justify-content: center;
            }

            .edit-btn{
                float:right;
                margin-right:55px;
            }

            .view-balance-button {
            width:80%;
            margin-left:190px;
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1em;
            font-weight: bold;
            /* Add 3D button effect */
            box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease-in-out;
        }

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
        }


        .add-button {
            background-color: blue; 
            color: white; 
            border-radius: 50%; 
            width: 40px; 
            height: 40px; 
            font-size: 20px; 
            cursor: pointer; 
        }


        .add-button:before {
            content: "+";
        }




    @media screen and (min-width: 320px) and (max-width: 480px) {
        .view-balance-button {
            
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
        <div class="view_expenses">     
        <button class="edit-btn">
            <a href="https://www.example.com/edit" style="text-decoration: none; color: black;">Edit</a>
        </button>

                    <p><?php echo $tricount->get_title();?> > Expenses</p>    
                    <a href="/prwb_2223_c03/Operation/balance/<?php echo $tricount->id?>">    
                        <button class="view-balance-button">
                            <i class="fas fa-dollar-sign"></i>View Balance
                        </button>
                    </a>
                        <ul>
                        <div class="container">
                            <ul class="data-list">
                                <?php if(!is_null($amounts)):
                                    foreach($amounts as $amount):
                                        echo '<li class="data-item">';
                                            foreach($amount as $am):
                                                // echo '<pre>';
                                                // print_r($am);
                                                // echo '</pre>';
                                                // die();
                                            $id=$am->initiator;
                                            $id_expense = $am->id;
                                            // print_r($id_expense);
                                            echo '<a href="Operation/detail_expense/'.$id_expense.'">
                                                <div class="data-card">
                                                        <h2 class="title">'.$am->title.'</h2>
                                                        <p class="amount">'.$am->amount.'$</p>
                                                        <p class="initiator">Paid by '.$am->getInitiator().'</p>
                                                        <p class="date">'.$am->created_at.'</p>
                                                </div>
                                            </a>';
                                            endforeach;
                                        echo '</li>';
                                    endforeach;
                                else: 
                                    echo '<p>dommage</p>';
                                endif;?>
                            </ul>

                        </div>
                        </ul>
            </div>
            <div class=totals>
                <div class="exp">
                    <p>TOTAL EXPENSES <br> <?php echo round($totalExp["0"]) . "$"?></p>                    
                </div>
                <div class="add-btn">
                <a href="/prwb_2223_c03/Operation/add/<?php echo $tricount->id?>">    
                        <button class="add-button">
                            
                        </button>
                    </a>
                </div>
                <div class="mytot">
                    <p>MY TOTAL <br> <?php echo round($mytot["0"]) . "$"?></p>                  
                </div>
            </div>
           
            </div>
    </div>
    

</body>
</html>