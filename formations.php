<?php
$title="Formazioni";
include('header.php');


$round;
$competitionID;

if(!isset($_SESSION['last_competition'])){
    $_SESSION['last_competition']=$config['default_competition'];
}

if(isset($_GET['competition'])){
    $competitionID=$_GET['competition'];
    $_SESSION['last_competition']=$competition;
}else{
    //$competition=$_SESSION['last_competition'];
    $competitionID=$config['default_competition'];
}

$apiPath = "/competitions";

$json=$apiAccess->accessApi($apiPath,"GET");


if($json["error"]==false && isset($json["data"][$competitionID])){

	$competition = $json["data"][$competitionID];
	
	$rounds_list=$competition["competition_rounds"];
	
	if(isset($_GET['round'])){
		
	    $round=$_GET['round'];
	
	}else if(!isset($_GET['round'])){
	
	    $round=$config['current_round'];
	    
	    $round=$competition["competition_rounds"][$round];
	
	}
	
	
	
	$real_round=$competition["real_rounds"][$round];
	
	?>
	
	<div class="container-fluid">
	    <div class="row">
		    <div class="col-md-12">
		        <div id="team-info">
		            <div class="name-team">
		            <?php foreach($rounds_list as $roundOf){
			            if($roundOf!=$round){
		                	echo "<a href=\"?round=".$roundOf."\">".$roundOf."</a>";
	                	}else{
		                	echo "<a class=\"current-round\" href=\"?round=".$roundOf."\">".$roundOf."</a>";
	                	}
		            }
		            ?>
		            </div>
		            <div class="balance">Giornata <?php echo $round; ?> - Seria A <?php echo $real_round; ?></div>
		        </div>
		    </div>
	    </div>
			    
		<?php
		
		
		
		
		$json_round=$apiAccess->accessApi("/rounds/$real_round","GET");
		
		
		
		
		
		if($json_round["error"]==false){ // SE ROUND NON ESISTE NON PRINT NULLA
		
			if(intval($config['current_round']) - intval($real_round) >= 0){  // OK ROUND COMINCIATO
				
				$isCalc=!$json_round["data"]["open"];
				$possibleToEdit=$json_round["data"]["formations_editing"];
			
			
				
				$apiPath = "/competitions/$competitionID/teams";
				
				$json=$apiAccess->accessApi($apiPath,"GET");
				
				$teams=$json["data"];
				
				?>
				         
				
					
				    <?php
				
				    $sum=0;
				    $conteggio=0;
				
				    foreach($teams as $temporary){
				
				        $id_user=$temporary['id'];
				        $name_team=$temporary['name_team'];
				
				
				        $apiPath = "/team/$id_user/$real_round?orderByRole=true";
				
				        $json_team=$apiAccess->accessApi($apiPath,"GET");
				
				        //$username=$temp['username'];
				
				        $temp = null;
				        $team = null;
				        $roster = null;
				
				        if(!$json_team["error"]){
				
				            if($json_team["data"]!=null){
				                $team=$json_team["data"];
				            }
				
				        }
				
				        $findBySistem=false;
				
				        if($team!=null) {
				            if (isset($team["players"])) {
				                $roster = $team["players"];
				            }
				        }
				
				
				        $tempArr=null;
				        $start=null;
				        $back=null;
				        $info_round=null;
				
				        if($roster==null && !$possibleToEdit && $real_round>1){
				
				            $json_team=$apiAccess->accessApi("/team/$id_user/$real_round?orderByRole=true","GET");
				
				            $team = null;
				            $roster = null;
				
				            if(!$json_team["error"]){
				                
				                if($json_team["data"]!=null){
				                    $team=$json_team["data"];
				                }
				
				            }
				
				            if($team==null){
				
				                $r=$real_round-1;
				                
				                if($json_team["valid_formation"]){
				
					                $json_team=$apiAccess->accessApi("/team/$id_user/".$r."?orderByRole=true&stat=$real_round","GET");
					
					                if(!$json_team["error"]){
					
					                    if($json_team["data"]!=null) {
					                        $team = $json_team["data"];
					
					                    }
					                }
					            }
				            }
				
				            if($team!=null){
				
				                $roster=$team["players"];
				
				                $findBySistem=true;
				
				            }
				
				
				
				        }
				
				        if($roster!=null){
				
				            $tempArr=$roster;
				            $start=$tempArr["titolari"];
				            $back=$tempArr["panchina"];
				            $info_round=null;
				
				        }
				
				        $sum=0;
				
				        if($isCalc){
				            $info_round=$json_round["data"]["results"];
				            $json_handicaps=$apiAccess->accessApi("/handicaps/rounds/$real_round","GET");
				        }
				
				        $conteggio++;
				
				        if($conteggio %2 !=0) { echo "<div class=\"row_formation row\" >"; } ?>
				
				            <div class="col-md-6">
				
				                <div <?php if(count($start)>0){ echo "class=\"formation\""; } else { echo "class=\"formation no_formation\""; } ?> >
				
				                    <div class="title_formation"><span class="username_title"><?php echo $name_team; ?></span>
				                        <?php if($team["def"]!=0){ ?>
				                            <span class="tactic_title"><?php echo $team["def"]."-".$team["cen"]."-".$team["att"]; ?></span>
				                        <?php } ?>
				                    </div>
				
				                    <div class="roster-item">
				                        <!-- Riga Legenda <div class="old-player info_player"></div> -->
				                        <?php if(count($start)>0){
					                        
				                                foreach($start as $player){
				
				                                $arr_stat=$player["player"]["stat"];
				
				                                if(isset($arr_stat)){
				                                    $stat=$arr_stat;
				                                }
				                                
				                                ?>
				                                
				
				                                    <div class="old-player" <?php echo "id=\"".$player["player"]["id"]."\" "; ?>
				                                        <?php echo "data-value=\"".$player["player"]["value"]."\" "; ?>
				                                        <?php echo "role=\"".$player["player"]["role"]."\" "; ?>
				                                        <?php echo "name=\"".$player["player"]["name"]."\" "; ?>  >
				                                        <div class="role-icon"><span <?php echo "class=\"".strtolower($player["player"]["role"])."-but\" "; ?> ><?php echo strtoupper($player["player"]["role"]); ?></span></div>
				                                        <div class="name-player-item"><?php echo $player["player"]["name"]; ?>
				                                            <?php if(isset($arr_stat[$real_round]) && $stat['scored']["value"]>0) {  for($i=0;$i<$stat['scored']["value"];$i++){ ?><img src="img/gol_ball.png"><?php } } ?>
				                                            <?php if(isset($arr_stat[$real_round]) && $stat['taken']["value"]>0) {  for($i=0;$i<$stat['taken']["value"];$i++){ ?><img src="img/gol_taken.png"><?php } } ?>
				                                            <?php if(isset($arr_stat[$real_round]) && $stat['autogol']["value"]>0) {  for($i=0;$i<$stat['autogol']["value"];$i++){ ?><img src="img/gol_auto.png"><?php } } ?>
				                                            <?php if(isset($arr_stat[$real_round]) && $stat['yellow_card']["value"]>0) {  for($i=0;$i<$stat['yellow_card']["value"];$i++){ ?><img src="img/yellow_card.png"><?php } } ?>
				                                            <?php if(isset($arr_stat[$real_round]) && $stat['red_card']["value"]>0) {  for($i=0;$i<$stat['red_card']["value"];$i++){ ?><img src="img/red_card.png"><?php } } ?>
				                                        </div>
				
				                                <?php if(!$isCalc){ ?>
				
				                                    <div class="info-player-item">
				                                        <div class="vote value-player-item"><?php if(isset($arr_stat)) { if($stat['vote']["value"]==-1) { echo "S.V."; } else { echo $stat['vote']["value"]; }  } else { echo " - "; } ?></div>
				                                        <div class="finalvote vote value-player-item"><?php if(isset($arr_stat)) { $temp=calc($stat,$player["player"]["role"]); if($stat['vote']["value"]==-1) { echo "S.V."; } else { echo $temp; } $sum=$sum+$temp; } else { echo " - "; } ?></div>
				                                    </div>
				
				                                <?php }else{ ?>
				
				                                    <div class="info-player-item">
				                                        <div class="vote value-player-item"><?php if(isset($arr_stat)) { if($stat['vote']["value"]==-1) { echo "S.V."; } else { echo $stat['vote']["value"]; }  } else { echo " - "; } ?></div>
				                                        <div class="finalvote vote value-player-item"><?php if(isset($arr_stat) && isset($stat['final'])) {  if($stat['final']["value"]==-1) { echo "S.V."; } else { echo $stat['final']["value"]; } } else { echo " - "; } ?></div>
				                                    </div>
				
				                                <?php } ?>
				
				                                </div>
				
				                            <?php } ?>
				
				                        <?php //} //fine count start ?>
				
				                        <div class="old-player panchina_info">Panchina</div>
				                            
				                            <?php foreach($back as $player){
				
				                                $arr_stat=$player["player"]["stat"];
				
				                                if(isset($arr_stat)){
				                                    $stat=$arr_stat;
				                                }
				
				                            ?>
				
				
				
				                            <div class="old-player" <?php echo "id=\"".$player["player"]["id"]."\" "; ?>
				                                <?php echo "data-value=\"".$player["player"]["value"]."\" "; ?>
				                                <?php echo "role=\"".$player["player"]["role"]."\" "; ?>
				                                <?php echo "name=\"".$player["player"]["name"]."\" "; ?>  >
				                                <div class="role-icon"><span <?php echo "class=\"".strtolower($player["player"]["role"])."-but\" "; ?> ><?php echo strtoupper($player["player"]["role"]); ?></span></div>
				                                <div class="name-player-item"><?php echo $player["player"]["name"]; ?>
				                                    <?php if(isset($arr_stat[$real_round]) && $stat['scored']["value"]>0) {  for($i=0;$i<$stat['scored']["value"];$i++){ ?><img src="img/gol_ball.png"><?php } } ?>
				                                    <?php if(isset($arr_stat[$real_round]) && $stat['taken']["value"]>0) {  for($i=0;$i<$stat['taken']["value"];$i++){ ?><img src="img/gol_taken.png"><?php } } ?>
				                                    <?php if(isset($arr_stat[$real_round]) && $stat['autogol']["value"]>0) {  for($i=0;$i<$stat['autogol']["value"];$i++){ ?><img src="img/gol_auto.png"><?php } } ?>
				                                    <?php if(isset($arr_stat[$real_round]) && $stat['yellow_card']["value"]>0) {  for($i=0;$i<$stat['yellow_card']["value"];$i++){ ?><img src="img/yellow_card.png"><?php } } ?>
				                                    <?php if(isset($arr_stat[$real_round]) && $stat['red_card']["value"]>0) {  for($i=0;$i<$stat['red_card']["value"];$i++){ ?><img src="img/red_card.png"><?php } } ?>
				                                </div>
				
				
				                                <?php if(!$isCalc){ ?>
				
				                                    <div class="info-player-item">
				                                        <div class="vote value-player-item"><?php if(isset($arr_stat)) { if($stat['vote']["value"]==-1) { echo "S.V."; } else { echo $stat['vote']["value"]; }  } else { echo " - "; } ?></div>
				                                        <div class="finalvote vote value-player-item"><?php if(isset($arr_stat)) { $temp=calc($stat,$player["player"]["role"]); if($stat['vote']["value"]==-1) { echo "S.V."; } else { echo $temp; } } else { echo " - "; } ?></div>
				                                    </div>
				
				                                <?php }else{ ?>
				
				                                    <div class="info-player-item">
				                                        <div class="vote value-player-item"><?php if(isset($arr_stat)) { if($stat['vote']["value"]==-1) { echo "S.V."; } else { echo $stat['vote']["value"]; }  } else { echo " - "; } ?></div>
				                                        <div class="finalvote vote value-player-item"><?php if(isset($arr_stat) && isset($stat['final'])) {  if($stat['final']["value"]==-1) { echo "S.V."; } else { echo $stat['final']["value"]; } } else { echo " - "; } ?></div>
				                                    </div>
				
				                                <?php } ?>
				
				                            </div>
				
				                        <?php } //FOREACH PANCHINA ?>
				
				                        <?php if($isCalc) {  
					                        
					                        
					                        if($json_handicaps["error"]==false){
						                        
					                            $handicaps=$json_handicaps["data"];
					                            
					                            $tot_points=$info_round[$id_user]['points'];
					                            
					                            foreach($handicaps as $handicap){
					                                if(intval($handicap["user"]["id"])==intval($id_user)){
					                                    $round_handicap=floatval($handicap["points"]);
					                                    $tot_points=$tot_points+$round_handicap;
					                                    /*if($result>=66){
					                                        $gol=floor(($result-66)/6)+1;
					                                    }*/
					                                    ?>
					                                    <div class="old-player handicap-formation" style="border: none;">
					                                        <div class="name-player-item"><span style="color:#CCCCCC;">
					                                        <?php if($round_handicap<0) { ?>Penalizzazione<?php }else{?>Bonus<?php } ?> di <?php echo $round_handicap." punti</span> : ".$handicap["description"]; ?></div>
					                                    </div>
					                                    <?php
					                                }
					                            }
					                        }
				                            ?>
				
				                            <div class="old-player total_info">
				                                Totale:<?php echo $tot_points; ?>
				                            </div>
				
				                        <?php }else{ ?>
				
				                            <div class="old-player total_info">
				                                Totale Solo Titolari:<?php echo $sum; ?>
				                            </div>
				
				                        <?php } ?>
				
				                    <?php } else { // IF COUNT START >0 ?>
				
				                        <div class="old-player" >
				                           	Nessuna Informazione Inserita
				                        </div>
				
				                    <?php } ?>
				
				                    <?php if($findBySistem){ // IF START NULL ?>
				
				                        <div class="old-player" >
				                            Formazione recuperata dal sistema
				                        </div>
				
				                    <?php }  ?>
				                    
				                    </div>
				                </div>
				            </div>
				    <?php if($conteggio %2==0) { echo "</div>"; } ?>
				    
				<?php } // FINE FORMATION ?>
				</div>
			
			
			<?php
				}else{ // FINE OK ROUND COMINCIATO	 ?>
					<div class="alert alert-danger error_display" role="alert">
						<span class="glyphicon glyphicon-alert" aria-hidden="true"></span>
						<span class="sr-only">Error:</span>Questo Round non è ancora cominciato
					</div>
			<?php
				} // FINE ELSE OK ROUND COMINCIATO
			?>
		
		<?php
			}else{ // FINE ERRORE SE ROUND NON ESISTE	 ?>
				<div class="alert alert-danger error_display" role="alert">
					<span class="glyphicon glyphicon-alert" aria-hidden="true"></span>
					<span class="sr-only">Error:</span>Round Non Esistente
				</div>
		<?php
			} // FINE ELSE ERRORE SE ROUND NON ESISTE
		?>
		
	</div> <!-- FINE CONTAINER FLUID -->
	
<?php
	}else{ // FINE ERROR COMPETITION EXISTS
?>
		<div class="alert alert-danger error_display" role="alert">
			<span class="glyphicon glyphicon-alert" aria-hidden="true"></span>
			<span class="sr-only">Error:</span>Questa Competizione non Esiste
		</div>
<?php
	} // FINE ELSE ERROR COMPETITION EXISTS
?>


<?php /*} else { //FINE SE ROUND EXIST ?>
<div class="error_display">Errore , non esiste questo turno </div>
<?php }*/ ?>
<?php include('footer.php'); ?>