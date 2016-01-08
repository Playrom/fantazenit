<?php
	
$title="Classifica";
include('header.php');
$id_competition;

if(!isset($_SESSION['last_competition'])){
    $_SESSION['last_competition']=$config['default_competition'];
}
if(isset($_GET['competition'])){
    $id_competition=$_GET['competition'];
    $_SESSION['last_competition']=$id_competition;
}else{
   $id_competition=-1;
}

if(isset($_GET['round'])){
    $round=$_GET['round'];
}else{
    $round=-1;  
    
}

$error=null;

$handicaps = null;

$users = null;



if($id_competition!=-1){
    
    
	
	if($round==-1){

		$json=$apiAccess->accessApi("/competitions/$id_competition" , "GET");
				
	    if($json["error"]==false){
		    
		    $competition = $json["data"]["competition"];
		    $rounds_list = $competition["competition_rounds"];
		    $real_round  = $competition["real_rounds"];
		    
		    $handicaps = $json["data"]["handicaps"];
		    $standings = $json["data"]["standings"];
		    $type = $json["data"]["competition"]["type"];
		    
		    if(isset($json["data"]["phases"])){
		    	$phases = $json["data"]["phases"];
		    }		    
		    
		    $json = $apiAccess->accessApi("/competitions/$id_competition/teams" , "GET");

			if($json["error"]==false){
				$users = $json["data"]["users"];
			}
		    
		    
		}else{
			$error = $json["message"];
			if(isset($json["error_data"])){
				$competition = $json["error_data"];
				$rounds_list = $json["error_data"]["competition_rounds"];
			}
		}
		
	}else{
		$json=$apiAccess->accessApi("/competitions/$id_competition/standings/$round" , "GET");
		
	    if($json["error"]==false){ 
		    
		    $competition = $json["data"]["competition"];
		    $rounds_list = $competition["competition_rounds"];
		    $real_round  = $competition["real_rounds"];
		    
		    $handicaps = $json["data"]["handicaps"];
		    $standings = $json["data"]["standings"];
		    $type = $json["data"]["competition"]["type"];
		    
		    $json = $apiAccess->accessApi("/competitions/$id_competition/teams" , "GET");

			if($json["error"]==false){
				$users = $json["data"]["users"];
			}
		    
		}else{
			$error = $json["message"];
			if(isset($json["error_data"])){
				$competition = $json["error_data"];
				$rounds_list = $json["error_data"]["competition_rounds"];
				$type=$json["error_data"]["type"];
			}
		}
	}
	
    	
	
	if($type=="CHAMPIONSHIP"){
		
	

         ?>
     
	    <div class="container-fluid">
	        <div class="row">
	            <div class="col-md-12">
					<div id="team-info">
			           	<div class="name-team">
					        <?php if($round==-1) { echo "<a class=\"current-round\" href=\"?competition=".$id_competition."\">Generale</a>"; } else { echo "<a href=\"?competition=".$id_competition."\">Generale</a>"; } ?>
				            <?php foreach($rounds_list as $roundOf){
					            if($roundOf!=$round){
				                	echo "<a href=\"?competition=".$id_competition."&round=".$roundOf."\">".$roundOf."</a>";
			                	}else{
				                	echo "<a class=\"current-round\" href=\"?competition=".$id_competition."&round=".$roundOf."\">".$roundOf."</a>";
			                	}
				            }
				            ?>
			            </div>
			            <div class="balance">Classifica <?php echo $competition["name"]; ?></div>
				    </div>
	            </div>
	        </div>

			<div class="row row_formation">
				<div class="col-md-12">
				    <div class="formation">
				        <?php 
					        					        
					        if($error==null){

					        	$pos=1;
	
							    $ret="<div class=\"roster-item\"><div class=\"old-player info_player\"><div class=\"role-icon\">*</div><div class=\"name-player-item\">Nome Squadra</div>";
							    $ret.="<div class=\"info-player-item\"><div class=\"vote value-player-item\">Punti</div><div class=\"finalvote vote value-player-item\">Gol</div></div></div>";
							
							    
						        foreach($standings as $team){
						
						            $ret.="<div class=\"old-player\" id=\"".$team["team_info"]["id"]."\" >";
						            $ret.="<div class=\"role-icon\"><span class=\"p-but\" >".$pos."</span></div>";
						            $ret.="<div class=\"name-player-item\"><a href=\"teams.php?id=".$team["team_info"]["id"]."\" >".$team["team_info"]["name_team"]."</a></div>";
						            $ret.="<div class=\"info-player-item\"><div class=\"vote value-player-item\">".$team["points"]."</div>";
						            $ret.="<div class=\"finalvote vote value-player-item\">".$team["gol"]."</div></div>";
						            $ret.="</div>";
						
						            $pos++; 
						        }
							
							    $ret.="</div>";
							    
							    echo $ret;
							    
							}else{ ?>
								<div class="alert alert-danger error_display" role="alert">
								<span class="glyphicon glyphicon-alert" aria-hidden="true"></span>
								<span class="sr-only">Error:</span>Giornata Non Calcolata
							</div>
							<?php }

				        ?>
				    </div>
				</div>
			</div>
			
			<?php if($handicaps!=null){ ?>

			<div class="row standing">
				<div class="col-md-12">
				    <div class="formation">
				    	<div class="roster-item">

				    		<div class="old-player" id="14">
		        				<div class="role-icon"><!--<span class="p-but">1</span> --></div>

		        				<div class="name-player-item">Bonus e Malus</div>
		        			</div>

				    		<?php foreach($handicaps as $handicap) { ?>

			        			<div class="old-player" id="14">
			        				<div class="role-icon"><!--<span class="p-but">1</span> --></div>

			        				<div class="name-player-item"><span style="color:#FF0000;"><?php echo $handicap["user"]["name_team"]; ?></span> - <?php echo $handicap["description"]; ?></div>
			        				<div class="info-player-item">
			        					<div class="finalvote vote value-player-item"><?php echo $handicap["points"]; ?></div>
			        				</div>
			        			</div>

				        	<?php } ?>

				        </div>
				    </div>
				</div>
			</div>
			
			<?php } ?>

	    </div>


	
	<?php
			
	}else if($type=="DIRECT" && $round==-1){ // ELSE DIRECT  ?>
	
	
		<div class="container-fluid">
	        <div class="row">
	            <div class="col-md-12">
					<div id="team-info">
			           	<div class="name-team">
					       
			            </div>
			            <div class="balance"><?php echo $competition["name"]; ?></div>
				    </div>
	            </div>
	        </div>
			        
	<?php
		error_log("START HTML:".udate('Y-m-d H:i:s:u'));
		
		$rounds_results = array();

		foreach($phases as $phase){

			$groups = $phase["groups"]; ?>
			
			<div class="row">
	            <div class="col-md-12">
					<div id="team-info" style="background: #FF0000; color: white;">
			           	<div class="name-team">
					        
					        
			            </div>
			            <div class="balance"><?php echo $phase["name"]; ?></div>
				    </div>
	            </div>
	        </div>
	        
	        <?php
			
			
			foreach($groups as $group){
				$standings = $group["standings"];
				
	?>
		
				<div class="row">
		            <div class="col-md-12">
						<div id="team-info">
				           	<div class="name-team">
						        
						        
				            </div>
				            <div class="balance"><?php echo $group["name"]; ?></div>
					    </div>
		            </div>
		        </div>
		
				<div class="row row_formation">
					<div class="col-md-12">
					    <div class="formation">
					        <?php 
						        					        
						        if($error==null){
	
						        	$pos=1;
		
								    $ret="<div class=\"roster-item\"><div class=\"old-player info_player\"><div class=\"role-icon\">*</div><div class=\"name-player-item\">Nome Squadra</div>";
								    $ret.="<div class=\"info-player-item\"><div class=\"vote value-player-item\">Punti</div></div></div>";
								
								    
							        foreach($standings as $team){
								        							
							            $ret.="<div class=\"old-player\" id=\"".$team["team_info"]["id"]."\" >";
							            $ret.="<div class=\"role-icon\"><span class=\"p-but\" >".$pos."</span></div>";
							            $ret.="<div class=\"name-player-item\"><a href=\"teams.php?id=".$team["team_info"]["id"]."\" >".$team["team_info"]["name_team"]."</a></div>";
							            $ret.="<div class=\"info-player-item\"><div class=\"vote value-player-item\">".$team["points"]."</div>";
							            $ret.="</div>";
							            $ret.="</div>";
							
							            $pos++; 
							        }
								
								    $ret.="</div>";
								    
								    echo $ret;
								    
								}else{ ?>
									<div class="alert alert-danger error_display" role="alert">
									<span class="glyphicon glyphicon-alert" aria-hidden="true"></span>
									<span class="sr-only">Error:</span>Giornata Non Calcolata
								</div>
								<?php }
	
					        ?>
					    </div>
					</div>
				</div>
				
				<div class="row row_formation">
					<div class="col-md-12">
					    <div class="formation">
						    <div class="roster-item">
						        <?php 
							       	$matches = $group["matches"];
							       					        
							        if($matches!=null){
								        
								        foreach($matches as $match){ 
									        $id_one = $match["id_one"];
									        $id_two = $match["id_two"];
									        
									        $round = $match["round"];
									        
									        $gol_one = " - ";
									        $gol_two = " - ";
									        
									        $round = $match["round"];
									        
									        if(isset($users[$id_one])){
										        $name_one = $users[$id_one]["name_team"];
										        
										        if(!isset($rounds_results[$round])){
										        	$json=$apiAccess->accessApi("/rounds/$round" , "GET");

										        	if($json["error"]==false){ 
											        	if(isset($json["data"]["results"])) $rounds_results[$round] = $json["data"]["results"];
											        }
										        }
										        
										        if(isset($rounds_results[$round])){
										        	$gol_one = $rounds_results[$round][$id_one]["gol"];
										        }
												
												
										        
									        }else{
										        $name_one = "BYE";
									        }
									        
									        
									        if(isset($users[$id_two])){
										        
										        $name_two = $users[$id_two]["name_team"];
										        
										        if(!isset($rounds_results[$round])){
										        	$json=$apiAccess->accessApi("/rounds/$round" , "GET");
										        	
										        	if($json["error"]==false){ 
											        	if(isset($json["data"]["results"])) $rounds_results[$round] = $json["data"]["results"];
											        }
										        }
										        
										        if(isset($rounds_results[$round])){
										        	$gol_two = $rounds_results[$round][$id_two]["gol"];
										        }
										        
									        }else{
										        $name_two = "BYE";
									        }
									        
									        
									        
									        
									        
									        
								        ?>
									       
									       
											
											<div class="old-player match">
												<div class="col-md-3"><div class="name-player-item"><?php echo $name_one; ?></div></div>
												<div class="col-md-1"><div class="name-player-item"><?php echo $gol_one; ?></div></div>
												<div class="col-md-2"><div class="name-player-item"><?php echo $round."° Giornata"; ?></div></div>
												<div class="col-md-1"><div class="name-player-item"><?php echo $gol_two; ?></div></div>
												<div class="col-md-3"><div class="name-player-item"><?php echo $name_two; ?></div></div>
												<div class="col-md-2"><div class="name-player-item" style="color:red;"><a href="formations.php?round=<?php echo $round;?>&teams=<?php echo $id_one.",".$id_two;?>">Dettagli</a></div></div>
											</div>
											
									<?php		
										}
									    
									
									}else{ ?>
										<div class="alert alert-danger error_display" role="alert">
										<span class="glyphicon glyphicon-alert" aria-hidden="true"></span>
										<span class="sr-only">Error:</span>Giornata Non Calcolata
									</div>
									<?php }
		
						        ?>
						    </div>
					    </div>
					</div>
				</div>
				
								
				<?php } // FINE GROUP ?> 
			</div> <!-- chiusura container -->
		<?php	
		} //FOREACH PHASE
		
	} // FINE DIRECT
		
}else{ ?>
    <div class="row">
        <?php 
	        $json=$apiAccess->accessApi("/competitions?array=true" , "GET");
	
			if($json["error"]==false){ // START ESISTE COMPETITION
	        
	        	$competitions=$json["data"];
	        	
	    		foreach($competitions as $competition){    
	    ?>
            <div class="col-md-6">
				<div id="team-info">
		            <div class="name_team"><a <?php echo "href=\"?competition=".$competition["id"]."\""; ?>><?php echo $competition["name"]; ?></a></div>
	                <!--<div class="name_user name_team"><?php //echo $team->getName()." ".$team->getSurname(); ?></div>
	                <div class="bottom_team name_team">
	                    <div class="credits"><?php //echo $team->getBalance(); ?> Crediti</div>
	                    <div class="position"></div>
	                </div> -->
		        </div>
            </div>
        <?php } 
            }
        ?>
        
    </div>
<?php 
} 
			
include('footer.php'); ?>