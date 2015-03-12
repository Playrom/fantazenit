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
        $already_calc=explode(";", $not_available);

        sort($already_calc);

        if($user->getAuth()==1){ ?>
        <div class="main">
            <form class="form-horizontal" action="gestionegiornate.php" method="post">
                <div class="form-group">
                    <label class="col-md-8 control-label left-label">Seleziona Giornata da Calcolare</label>
                    
                    <div class="col-md-2">
                        <select name="calc" class="form-control">
                        <?php foreach($rounds as $round){
                            echo "<option value=\"".$round."\" >".$round."</option>";
                        } ?>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
	                    <button type="submit" class="btn btn-default">Calcola Giornata</button>
                    </div>
                </div>
            </form>

            <form class="form-horizontal" action="gestionegiornate.php" method="post">
                <div class="form-group">
                    <label class="col-md-8 control-label left-label">Seleziona Giornata da Annullare</label>
                    
                    <div class="col-md-2">
	                    <select name="uncalc" class="form-control">
	                    <?php foreach($already_calc as $round){
	                        echo "<option value=\"".$round."\" >".$round."</option>";
	                    } ?>
	                    </select>
                    </div>
                    
                    <div class="col-md-2">
	                    <button type="submit" class="btn btn-default">Annulla Giornata</button>
                    </div>
                </div>
            </form>

            <form class="form-horizontal" action="gestionegiornate.php" method="post">
                <div class="form-group">
                    <label class="col-md-8 control-label left-label">Aggiungi una Giornata</label>
                    
                    <div class="col-md-2">
                    	<input type="number" name="add_round" min="1" max="38" size="2" class="form-control">
                    </div>
                    
                    <div class="col-md-2">
	                    <button type="submit" class="btn btn-default">Crea Giornata</button>
                    </div>
                </div>
            </form>

             <form class="form-horizontal"  action="gestionegiornate.php" method="post">
                <div class="form-group">
                    <label class="col-md-6 control-label left-label">Seleziona Giornata Attuale</label>

                    <div class="col-md-2">
                    	<select name="current_round" class="form-control">
	                    <?php foreach($rounds as $round){
                            echo "<option value=\"".$round."\" >".$round."</option>";
                        } ?>
	                    </select>
                    </div>
                    
                    <div class="col-md-2">
                    	<input type="datetime" id="datetimepicker" name="time_close" class="form-control">
                    </div>
                    
                    <div class="col-md-2">
	                    <button type="submit" class="btn btn-default">Imposta Giornata</button>
                    </div>
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