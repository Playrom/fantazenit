<?php
	$title="Classifica";
	include('header.php');
    $id_competition;

    if(isset($_SESSION['username'])){
    	$username=$_SESSION['username'];
		$user=$database_user->getUserByUsername($username);
	}

    $config=$database->dumpConfig();

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
    
    if($id_competition!=-1){
	    
	    
		$rounds_list=$database_rounds->getRoundsOfCompetition($id_competition);
    
		$competition=$database_competitions->getCompetition($id_competition);
	    if($competition!=null){ // START ESISTE COMPETITION
	
	
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
			            <div class="balance">Classifica <?php echo $competition->getName(); ?></div>
				    </div>
	            </div>
	        </div>
	      
			<div class="row standing">
				<div class="col-md-12">
				    <div class="formation">
				        <?php if($round!=-1) { echo getStandingsRound($id_competition,$round); }else{ echo getStandings($id_competition); } ?>
				    </div>
				</div>
			</div>
	    </div>
	
		<?php } else { //FINE SE COMPETITION EXIST ?>
		<div class="error_display">Errore , non esiste questa competizione </div>
		<?php } ?>
	<?php }else{ ?>
		<div class="container-fluid">
	        <div class="row">
		        <?php $competitions=$database_competitions->getCompetitions();
			    		foreach($competitions as $competition){    
			    ?>
		            <div class="col-md-6">
						<div id="team-info">
				            <div class="name_team"><a <?php echo "href=\"?competition=".$competition->getId()."\""; ?>><?php echo $competition->getName(); ?></a></div>
			                <!--<div class="name_user name_team"><?php //echo $team->getName()." ".$team->getSurname(); ?></div>
			                <div class="bottom_team name_team">
			                    <div class="credits"><?php //echo $team->getBalance(); ?> Crediti</div>
			                    <div class="position"></div>
			                </div> -->
				        </div>
		            </div>
	            <?php } ?>
	        </div>
	    </div>
	<?php } ?>


<?php include('footer.php'); ?>
