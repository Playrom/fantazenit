<?php
include('header.php');
?>

    <?php

    if(isset($_POST['calc'])){
	    $round=$_POST['calc'];
	}else if(isset($_POST['uncalc'])){

	}

    $round;
    $competition;

    if(isset($_SESSION['username'])){
        $username=$_SESSION['username'];
        $database=new ConnectDatabase("localhost","root","aicon07","fantacalcio",3306);

        if(isset($_POST['calc'])){
            $round=$_POST['calc'];
            $database->closeRound($round);

        }else if(isset($_POST['uncalc'])){

            $round=$_POST['uncalc'];
            $database->openRound($round);

        }else if(isset($_POST['current_round'])){
            $current_round=$_POST['current_round'];
            $time=$_POST['time_close'];
            $database->setCurrentRound($current_round,$time);
        }else if(isset($_POST['add_round'])){
            $add_round=$_POST['add_round'];
            $database->addRound($add_round);
        }



        $user=$database->getUserByUsername($username);

        $config=$database->dumpConfig();


        $available=$config['available-round'];
        $rounds=explode(";",$available);
        sort($rounds);

        $not_available=$config['already-calc'];
        $not_calc=explode(";", $not_available);

        sort($not_calc);

        if($user->getAuth()==1){ ?>
        <div class="main">
            <form class="settings" action="gestionegiornate.php" method="post">
                <div class="setting_item">
                    <div class="setting_item_name">Seleziona Giornata da Calcolare</div>
                    <div class="setting_item_input">
                        <select name="calc" class="market-select">
                        <?php foreach($rounds as $round){
                            echo "<option value=\"".$round."\" >".$round."</option>";
                        } ?>
                        </select>
                        <input class="" type="submit" value="Calcola Giornata">
                    </div>
                </div>
            </form>

            <form class="select-calc" action="gestionegiornate.php" method="post">
                <div class="name-team">Seleziona Giornata da Annullare</div>
                <div class="balance">
                    <select name="uncalc" class="market-select">
                    <?php foreach($not_calc as $round){
                        echo "<option value=\"".$round."\" >".$round."</option>";
                    } ?>
                    </select>
                    <input class="market-select-button" type="submit" value="Annulla Giornata">
                </div>
            </form>

            <form class="select-current-round" action="gestionegiornate.php" method="post">
                <div class="name-team">Aggiungi una giornata</div>
                <div class="balance">
                    <input type="number" name="add_round" min="1" max="38" size="2" class="market-select">
                    <input class="market-select-button" type="submit" value="Crea Giornata">
                </div>
            </form>

             <form class="select-current-round" action="gestionegiornate.php" method="post">
                <div class="name-team">Seleziona Giornata Attuale</div>
                <div class="balance">
                    <input type="number" name="current_round" min="1" max="38" size="2" class="market-select">
                    <input type="datetime" id="datetimepicker" name="time_close" class="market-select">
                    <input class="market-select-button" type="submit" value="Imposta Giornata">
                </div>
            </form>
        </div>
        <?php }


    }else{
        $_SESSION['old_url']=$_SERVER['REQUEST_URI'];
        header("Location:login.php");
    }

?>


<?php include('footer.php'); ?>