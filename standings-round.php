<?php
	include('header.php');
    $id_competition;

    if(isset($_SESSION['username'])){
    	$username=$_SESSION['username'];
		$user=$database->getUserByUsername($username);
	}

    $database=new ConnectDatabase(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);
    $config=$database->dumpConfig();

    if(!isset($_SESSION['last_competition'])){
        $_SESSION['last_competition']=$config['default_competition'];
    }
    if(isset($_GET['competition'])){
        $id_competition=$_GET['competition'];
        $_SESSION['last_competition']=$competition;
    }else{
       $id_competition=-1;
    }

    if(isset($_GET['round'])){
        $round=$_GET['round'];
    }else{
        $round=$config['last-round'];  
        
    }
    
    if($id_competition!=-1){
	    
		$round=$database->getRoundCompetitionByRealRound($round,$id_competition);
		    
		$competition=$database->getCompetition($id_competition);
	    if($competition!=null){ // START ESISTE COMPETITION
			$rounds_list=$database->getRoundsOfCompetition($id_competition);
	         ?>
			<div class="container-fluid">
	            <div class="row">
		    <div class="col-md-12">
		        <div id="team-info">
		            <div class="name-team">
			        <?php echo "<a href=\"standings.php?competition=".$id_competition."\">Generale</a>"; ?>
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
					    <div class="standing formation">
					        <?php echo getStandingsRound($id_competition,$round); ?>
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
		        <?php $competitions=$database->getCompetitions();
			    		foreach($competitions as $competition){    
			    ?>
		            <div class="col-md-6">
						<div id="team-info">
				            <div class="name_team"><a <?php echo "href=\"?competition=".$competition->getId()."\""; ?>><?php echo $competition->getName(); ?></a></div>
			                <!--<div class="name_user name_team"><?php echo $team->getName()." ".$team->getSurname(); ?></div>
			                <div class="bottom_team name_team">
			                    <div class="credits"><?php echo $team->getBalance(); ?> Crediti</div>
			                    <div class="position"></div>
			                </div> -->
				        </div>
		            </div>
	            <?php } ?>
	        </div>
	    </div>
	<?php } ?>

<script type="text/javascript">

</script>

<?php include('footer.php'); ?>
