<?php
include('header.php');



if($username != null){
	
	$editConfig=array();
	
	$new_pass = null;
	$email = null;
	$url_fb = null;
	


    if(isset($_POST['pass1']) && isset($_POST['pass2'])){
        $pass1 = $_POST['pass1'];
        $pass2 = $_POST['pass2'];
        
        if($pass1 == $pass2 && $pass1!=""){
	        $new_pass= md5($pass1);
	        $editConfig["new_pass"] = $new_pass;
        }
    }
    
    if(isset($_POST['email'])){
        $email = $_POST['email'];
        $editConfig["email"] = $email;
    }

    if(isset($_POST['url_fb'])){
        $url_fb = $_POST["url_fb"];
        $editConfig["url_fb"]=$url_fb;
    }

    if(isset($_POST['current_pass'])){
        $current_pass = $_POST["current_pass"];
        $encoded = md5($current_pass);
        
        $editConfig["current_pass"] = $encoded;
        
        if(count($editConfig)>1){
	        
	        $params = array("postParams" => $editConfig);
        
        	$json = $apiAccess->accessApi("/me","POST",$params);
        	if($json["error"]==true){
	        	$error_json[] = $json;
        	}
        	
        }
    }
	
    $round;
    $competition;

    
    $id=$userId;
    $team=$apiAccess->accessApi("/users/".$id,"GET");
    $roster=null;


    if(isset($team["data"])){
        $arr=$team["data"];
        
        $user = $arr;
        
        $roster=$arr["players"];
        $transfers=$arr["transfers"];
        
    }
    
    include("error-box.php");

	?>
    <div class="container-fluid">
	    
        <div class="row">
            <div class="col-md-12">
                <div id="team-info">
                    <div class="name-team"><?php echo $arr["name_team"];?></div>
                    <div class="balance"><?php echo $arr["name"]." ".$arr["surname"]; ?></div>
                </div>
            </div>
        </div>
    

	    <div class="row_formation row">
	        <div class="col-md-6 team_single">
	            <div class="title_team_single">LA ROSA</div>
	            
	            <div id="side-roster">
	                <div class="roster-item" id="P_free"  >
	                <div class="old-player info_player"><div class="role-icon">*</div><div class="name-player-item">Nome</div><div class="info-player-item"><div class="vote value-player-item">Valore</div><div class="finalvote vote value-player-item">Iniziale</div></div></div>
	                    <?php foreach($roster as $player){
	                        if(strtolower($player["player"]["role"])=="p"){
	
	                    ?>
	                      <div class="old-player" <?php echo "id=\"".$player["player"]["id"]."\" "; ?>
	                        <?php echo "data-value=\"".$player["player"]["value"]."\" "; ?>
	                        <?php echo "name=\"".$player["player"]["name"]."\" "; ?>  >
	                          <div class="role-icon"><span <?php echo "class=\"".strtolower($player["player"]["role"])."-but\" "; ?> ><?php echo strtoupper($player["player"]["role"]); ?></span></div>
	                          <div class="name-player-item"><?php echo $player["player"]["name"]; ?></div>
	                          <div class="info-player-item">
	                            <div class="vote value-player-item"><?php echo $player["player"]["value"]; ?></div>
	                            <div class="finalvote vote value-player-item"><?php echo $player["player"]["first_value"]; ?></div>
	                          </div>
	                      </div>
	                   <?php }
	                    } ?>
	
	                </div>
	
	                <div class="roster-item" id="D_free" >
	
	                    <?php foreach($roster as $player){
	
	                        if(strtolower($player["player"]["role"])=="d"){
	
	                    ?>
	                        <div class="old-player" <?php echo "id=\"".$player["player"]["id"]."\" "; ?>
	                            <?php echo "data-value=\"".$player["player"]["value"]."\" "; ?>
	                            <?php echo "name=\"".$player["player"]["name"]."\" "; ?>  >
	                            <div class="role-icon"><span <?php echo "class=\"".strtolower($player["player"]["role"])."-but\" "; ?> ><?php echo strtoupper($player["player"]["role"]); ?></span></div>
	                            <div class="name-player-item"><?php echo $player["player"]["name"]; ?></div>
	                            <div class="info-player-item">
	                                <div class="vote value-player-item"><?php echo $player["player"]["value"]; ?></div>
	                                <div class="finalvote vote value-player-item"><?php echo $player["player"]["first_value"]; ?></div>
	                            </div>
	                        </div>
	                   <?php }
	                    } ?>
	
	                </div>
	
	                <div class="roster-item" id="C_free" >
	
	                    <?php foreach($roster as $player){
	
	                        if(strtolower($player["player"]["role"])=="c"){
	
	                    ?>
	                        <div class="old-player" <?php echo "id=\"".$player["player"]["id"]."\" "; ?>
	                            <?php echo "data-value=\"".$player["player"]["value"]."\" "; ?>
	                            <?php echo "name=\"".$player["player"]["name"]."\" "; ?>  >
	                            <div class="role-icon"><span <?php echo "class=\"".strtolower($player["player"]["role"])."-but\" "; ?> ><?php echo strtoupper($player["player"]["role"]); ?></span></div>
	                            <div class="name-player-item"><?php echo $player["player"]["name"]; ?></div>
	                            <div class="info-player-item">
	                                <div class="vote value-player-item"><?php echo $player["player"]["value"]; ?></div>
	                                <div class="finalvote vote value-player-item"><?php echo $player["player"]["first_value"]; ?></div>
	                            </div>
	                        </div>
	                   <?php }
	                    } ?>
	
	                </div>
	
	                <div class="roster-item" id="A_free" >
	
	                    <?php foreach($roster as $player){
	
	                        if(strtolower($player["player"]["role"])=="a"){
	
	                    ?>
	                        <div class="old-player" <?php echo "id=\"".$player["player"]["id"]."\" "; ?>
	                            <?php echo "data-value=\"".$player["player"]["value"]."\" "; ?>
	                            <?php echo "name=\"".$player["player"]["name"]."\" "; ?>  >
	                            <div class="role-icon"><span <?php echo "class=\"".strtolower($player["player"]["role"])."-but\" "; ?> ><?php echo strtoupper($player["player"]["role"]); ?></span></div>
	                            <div class="name-player-item"><?php echo $player["player"]["name"]; ?></div>
	                            <div class="info-player-item">
	                                <div class="vote value-player-item"><?php echo $player["player"]["value"]; ?></div>
	                                <div class="finalvote vote value-player-item"><?php echo $player["player"]["first_value"]; ?></div>
	                            </div>
	                        </div>
	                   <?php }
	                    } ?>
	
	                </div>
	            </div>
	
	        </div>
	
	        <div class="col-md-6">
	
	            <div class="title_team_single title_transfers_single ">OPERAZIONI DI MERCATO</div>
	            
	            
	            <div class="transfers">
	                <?php foreach($transfers as $transfer){
	
	                $result=$apiAccess->accessApi("/markets/".$transfer["id_market"],"GET");
	
	                $market=$result["data"];
	
	                $date=$transfer["date"];
	
	                $old=$transfer["old_player"];
	                $new=$transfer["new_player"];
	                ?>
	                <div class="name_market"><?php echo $market["name"]; ?> - <?php echo $date ?></div>
	                <div class="operation">
	                    <div class="old transfers_player">
	                        <span class="role-icon"><span <?php echo "class=\"".strtolower($old["player"]["role"])."-but\" "; ?> ><?php echo strtoupper($old["player"]["role"]); ?></span></span>
	                        <div class="player_name"><?php echo $old["player"]["name"]; ?></div>
	                        <div class="info_transfer">
	                            <div class="value_transfer"><?php echo $old["cost"]; ?></div>
	                            <img src="img/redarrow.png">
	                        </div>
	                    </div>
	                    <div class="new transfers_player">
	                        <span class="role-icon"><span <?php echo "class=\"".strtolower($new["player"]["role"])."-but\" "; ?> ><?php echo strtoupper($new["player"]["role"]); ?></span></span>
	                        <div class="player_name"><?php echo $new["player"]["name"]; ?></div>
	                        <div class="info_transfer">
	                            <div class="value_transfer"><?php echo $new["cost"]; ?></div>
	                            <img src="img/greenarrow.png">
	                        </div>
	                    </div>
	                </div>
	        
	                <?php }?>
	                
	                <?php if(count($transfers)==0){ ?>
	                    <div class="name_market">Nessuna Operazione di Mercato Effettuata</div>
	                <?php } ?>
	    
	                
	            </div>
	    	</div>
	    </div>
	    
	    <div class="row">
		    <div class="col-md-12">
	            <form class="settings-profile  form-horizontal" action="profile.php" method="post">
	                <div class="form-group">
	                    <h3 class="col-md-8 control-label left-label">Nuova Password&nbsp;&nbsp;<small></small></h3>
	                    <div class="col-md-4">
	                        <input class="form-control"  type="text" name="pass1"  >
	                    </div>
	                </div>
	
	                <div class="form-group">
	                    <h3 class="col-md-8 control-label left-label">Conferma Password&nbsp;&nbsp;<small></small></h3>
	                    <div class="col-md-4">
	                        <input class="form-control" type="text" name="pass2"  >
	                    </div>
	                </div>
	                
	                <div class="form-group">
	                    <h3 class="col-md-8 control-label left-label">Email&nbsp;&nbsp;<small></small></h3>
	                    <div class="col-md-4">
	                        <input class="form-control" type="text" name="email" <?php echo "value=\"".$user['email']."\""; ?> >
	                    </div>
	                </div>
	
	                <div class="form-group">
	                    <h3 class="col-md-8 control-label left-label">URL Facebook&nbsp;&nbsp;<small></small></h3>
	                    <div class="col-md-4">
	                        <input class="form-control" type="text" name="url_fb" <?php echo "value=\"".$user['url_fb']."\""; ?> >
	                    </div>
	                </div>
	
	                <div class="form-group">
	                    <h3 class="col-md-8 control-label left-label">Password Attuale&nbsp;&nbsp;<small></small></h3>
	                    <div class="col-md-4">
	                        <input class="form-control" type="password" name="current_pass" >
	                    </div>
	                </div>
	
	               	
	                <div class="form-group">
	                    <div class="col-md-12">
	                        <button type="submit" class="btn btn-default col-md-12">Modifica</button>
	                    </div>
	                </div>
	            </form>
		    </div>
        </div>
	    
    </div>
    
    

<?php
}else{

    $_SESSION['old_url']=$_SERVER['REQUEST_URI'];
    header("Location:login.php");
	
} // END USERNAME

?>
<?php include('footer.php'); ?>