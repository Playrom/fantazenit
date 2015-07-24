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

if($username!=null){
	if($userAuth==1){ 

	    if(isset($_POST['calc'])){
	
	        $round=$_POST['calc'];
	        
			$arr_data = array("round"=>$round , "type" => "CLOSE");
                     
			$params = array('postParams' => $arr_data);
            
            $json=$apiAccess->accessApi("/rounds","POST",$params);
                        
            if($json["error"]==true){
                var_dump($json);
            }
        }else if(isset($_POST['uncalc'])){
	
	        $round=$_POST['uncalc'];
			
			$arr_data = array("round"=>$round , "type" => "OPEN");
                     
			$params = array('postParams' => $arr_data);
            
            $json=$apiAccess->accessApi("/rounds","POST",$params);
                        
            if($json["error"]==true){
                var_dump($json);
            }
	
	    }else if(isset($_POST['current_round'])){
	
	        $current_round=$_POST['current_round'];
	        $time=$_POST['time_close'];
	        
	        $arr_data = array("round"=>$current_round , "type" => "SET_CURRENT" , "additional" => $time);
                     
			$params = array('postParams' => $arr_data);
            
            $json=$apiAccess->accessApi("/rounds","POST",$params);
                        
            if($json["error"]==true){
                var_dump($json);
            }
	
	    }else if(isset($_POST['add_round'])){
	
	        $add_round=$_POST['add_round'];

			$arr_data = array("round"=>$add_round , "type" => "ADD");
                     
			$params = array('postParams' => $arr_data);
            
            $json=$apiAccess->accessApi("/rounds","POST",$params);
                        
            if($json["error"]==true){
                var_dump($json);
            }
	
	    }
	
	    $available=$config['available-round'];
	    $rounds=explode(";",$available);
	    sort($rounds);
	
	    $not_available=$config['already-calc'];
	    $already_calc=explode(";", $not_available);
	
	    sort($already_calc);
	    
	    ?>
	
	    <div class="main">
	        <form class="form-horizontal" action="gestionegiornate.php" method="post">
	            <div class="form-group">
	                <label class="col-md-8 control-label left-label">Seleziona Giornata da Calcolare</label>
	                
	                <div class="col-md-2">
	                    <select name="calc" class="form-control">
	                    <?php foreach($rounds as $round){
	                        if ($round<=intval($config["last_stat_round"])){
	                            echo "<option value=\"".$round."\" >".$round."</option>";
	                        }
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
    <?php
	    
	} // Fine Auth


}else{
    $_SESSION['old_url']=$_SERVER['REQUEST_URI'];
    header("Location:login.php");
}

?>


<?php include('footer.php'); ?>