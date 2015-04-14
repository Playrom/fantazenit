<?php
$title="Formazioni";
include('header.php');


$round;
$competition;

$config=$database->dumpConfig();

if(!isset($_SESSION['last_competition'])){
    $_SESSION['last_competition']=$config['default_competition'];
}

if(isset($_GET['round']) && isset($_GET['competition'])){

    $round=$_GET['round'];
    $competition=$_GET['competition'];
    $_SESSION['last_competition']=$competition;

}else if(isset($_GET['round']) && !isset($_GET['competition'])){

    $round=$_GET['round'];
    $competition=$_SESSION['last_competition'];

}else if(!isset($_GET['round']) && isset($_GET['competition'])){

    $round=$config['current_round'];
    $competition=$_GET['competition'];
    $_SESSION['last_competition']=$competition;
    $round=$database_rounds->getRoundCompetitionByRealRound($round,$competition);

}else if(isset($_SESSION['last_competition'])){

    $round=$config['current_round'];
    $competition=$_SESSION['last_competition'];
    $round=$database_rounds->getRoundCompetitionByRealRound($round,$competition);

}else{

    $round=$config['current_round'];
    $competition=$config['default_competition'];
    $round=$database_rounds->getRoundCompetitionByRealRound($round,$competition);

}

$real_round=$database_rounds->getRealRoundByRoundCompetition($round,$competition);

$teams=$database_rounds->getTeamsByRoundAndCompetition($real_round,$competition,$database_players->dumpSingoliToList(null,null));
$isCalc=!$database_rounds->isOpenRound($real_round);

$rounds_list=$database_rounds->getRoundsOfCompetition($competition);
$possibleToEdit=$database_rounds->isPossibleToEditFormation($real_round);

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

    $sum=0;
    $conteggio=0;

    foreach($teams as $temp){

        $id_user=$temp['id_user'];
        $username=$temp['username'];
        $team=$temp['team'];
        $findBySistem=false;

        $roster=$team->getPlayers();

        $tempArr=null;
        $start=null;
        $back=null;
        $info_round=null;

        if($roster==null && !$possibleToEdit && $real_round>1){

            $team=$database_rounds->getTeam($id_user,$real_round);
            $roster=$team->getPlayers();
            $findBySistem=true;

        }

        if($roster!=null){

            $tempArr=$roster->orderByRole();
            $start=$tempArr[0];
            $back=$tempArr[1];
            $info_round=null;

        }

        $name_team=$temp['name_team'];
        $sum=0;

        if($isCalc){
            $info_round=$database_rounds->getInfoRound($real_round);
        }

        $conteggio++;

        if($conteggio %2 !=0) { echo "<div class=\"row_formation row\" >"; } ?>

            <div class="col-md-6">

                <div <?php if(count($start)>0){ echo "class=\"formation\""; } else { echo "class=\"formation no_formation\""; } ?> >

                    <div class="title_formation"><span class="username_title"><?php echo $name_team; ?></span>
                        <?php if($team->getDef()!=0){ ?>
                            <span class="tactic_title"><?php echo $team->getDef()."-".$team->getCen()."-".$team->getAtt(); ?></span>
                        <?php } ?>
                    </div>

                    <div class="roster-item">
                        <!-- Riga Legenda <div class="old-player info_player"></div> -->
                        <?php if(count($start)>0){

                                foreach($start as $player){

                                $arr_stat=$database_players->dumpStats($player->getPlayer()->getId());

                                if(isset($arr_stat[$real_round])){
                                    $stat=$arr_stat[$real_round];
                                }

                                if(!$isCalc){ ?>
                                    <div class="old-player" <?php echo "id=\"".$player->getPlayer()->getId()."\" "; ?>
                                        <?php echo "data-value=\"".$player->getPlayer()->getValue()."\" "; ?>
                                        <?php echo "role=\"".$player->getPlayer()->getRole()."\" "; ?>
                                        <?php echo "name=\"".$player->getPlayer()->getName()."\" "; ?>  >
                                        <div class="role-icon"><span <?php echo "class=\"".strtolower($player->getPlayer()->getRole())."-but\" "; ?> ><?php echo strtoupper($player->getPlayer()->getRole()); ?></span></div>
                                        <div class="name-player-item"><?php echo $player->getPlayer()->getName(); ?>
                                            <?php if(isset($arr_stat[$real_round]) && $stat['scored']->getValue()>0) {  for($i=0;$i<$stat['scored']->getValue();$i++){ ?><img src="img/gol_ball.png"><?php } } ?>
                                            <?php if(isset($arr_stat[$real_round]) && $stat['taken']->getValue()>0) {  for($i=0;$i<$stat['taken']->getValue();$i++){ ?><img src="img/gol_taken.png"><?php } } ?>
                                            <?php if(isset($arr_stat[$real_round]) && $stat['autogol']->getValue()>0) {  for($i=0;$i<$stat['autogol']->getValue();$i++){ ?><img src="img/gol_auto.png"><?php } } ?>
                                            <?php if(isset($arr_stat[$real_round]) && $stat['yellow_card']->getValue()>0) {  for($i=0;$i<$stat['yellow_card']->getValue();$i++){ ?><img src="img/yellow_card.png"><?php } } ?>
                                            <?php if(isset($arr_stat[$real_round]) && $stat['red_card']->getValue()>0) {  for($i=0;$i<$stat['red_card']->getValue();$i++){ ?><img src="img/red_card.png"><?php } } ?>
                                        </div>
                                        <div class="info-player-item">
                        	                <div class="vote value-player-item"><?php if(isset($arr_stat[$real_round])) { if($stat['vote']->getValue()==-1) { echo "S.V."; } else { echo $stat['vote']->getValue(); }  } else { echo " - "; } ?></div>
                        	                <div class="finalvote vote value-player-item"><?php if(isset($arr_stat[$real_round])) { $temp=calc($stat,$player->getPlayer()->getRole()); if($stat['vote']->getValue()==-1) { echo "S.V."; } else { echo $temp; } $sum=$sum+$temp; } else { echo " - "; } ?></div>
                                        </div>
                                    </div>

                            <?php }else{ ?>
                                    <div class="old-player" <?php echo "id=\"".$player->getPlayer()->getId()."\" "; ?>
                                        <?php echo "data-value=\"".$player->getPlayer()->getValue()."\" "; ?>
                                        <?php echo "role=\"".$player->getPlayer()->getRole()."\" "; ?>
                                        <?php echo "name=\"".$player->getPlayer()->getName()."\" "; ?>  >
                                        <div class="role-icon"><span <?php echo "class=\"".strtolower($player->getPlayer()->getRole())."-but\" "; ?> ><?php echo strtoupper($player->getPlayer()->getRole()); ?></span></div>
                                        <div class="name-player-item"><?php echo $player->getPlayer()->getName(); ?>
                                            <?php if(isset($arr_stat[$real_round]) && $stat['scored']->getValue()>0) {  for($i=0;$i<$stat['scored']->getValue();$i++){ ?><img src="img/gol_ball.png"><?php } } ?>
                                            <?php if(isset($arr_stat[$real_round]) && $stat['taken']->getValue()>0) {  for($i=0;$i<$stat['taken']->getValue();$i++){ ?><img src="img/gol_taken.png"><?php } } ?>
                                            <?php if(isset($arr_stat[$real_round]) && $stat['autogol']->getValue()>0) {  for($i=0;$i<$stat['autogol']->getValue();$i++){ ?><img src="img/gol_auto.png"><?php } } ?>
                                            <?php if(isset($arr_stat[$real_round]) && $stat['yellow_card']->getValue()>0) {  for($i=0;$i<$stat['yellow_card']->getValue();$i++){ ?><img src="img/yellow_card.png"><?php } } ?>
                                            <?php if(isset($arr_stat[$real_round]) && $stat['red_card']->getValue()>0) {  for($i=0;$i<$stat['red_card']->getValue();$i++){ ?><img src="img/red_card.png"><?php } } ?>
                                        </div>
                                        <div class="info-player-item">
                        	                <div class="vote value-player-item"><?php if(isset($arr_stat[$real_round])) { if($stat['vote']->getValue()==-1) { echo "S.V."; } else { echo $stat['vote']->getValue(); }  } else { echo " - "; } ?></div>
                        	                <div class="finalvote vote value-player-item"><?php if(isset($arr_stat[$real_round]) && isset($stat['final'])) {  if($stat['final']->getValue()==-1) { echo "S.V."; } else { echo $stat['final']->getValue(); } } else { echo " - "; } ?></div>
                                        </div>
                                    </div>
                            <?php } ?>

                        <?php } //fine count start ?> 

                        <div class="old-player panchina_info">Panchina</div>
                            
                            <?php foreach($back as $player){

                                $arr_stat=$database_players->dumpStats($player->getPlayer()->getId());

                                if(isset($arr_stat[$real_round])){
                                    $stat=$arr_stat[$real_round];
                                }

                                if(!$isCalc){ ?>
                                    
                                    <div class="old-player" <?php echo "id=\"".$player->getPlayer()->getId()."\" "; ?>
                                        <?php echo "data-value=\"".$player->getPlayer()->getValue()."\" "; ?>
                                        <?php echo "role=\"".$player->getPlayer()->getRole()."\" "; ?>
                                        <?php echo "name=\"".$player->getPlayer()->getName()."\" "; ?>  >
                                        <div class="role-icon"><span <?php echo "class=\"".strtolower($player->getPlayer()->getRole())."-but\" "; ?> ><?php echo strtoupper($player->getPlayer()->getRole()); ?></span></div>
                                        <div class="name-player-item"><?php echo $player->getPlayer()->getName(); ?>
                                            <?php if(isset($arr_stat[$real_round]) && $stat['scored']->getValue()>0) {  for($i=0;$i<$stat['scored']->getValue();$i++){ ?><img src="img/gol_ball.png"><?php } } ?>
                                            <?php if(isset($arr_stat[$real_round]) && $stat['taken']->getValue()>0) {  for($i=0;$i<$stat['taken']->getValue();$i++){ ?><img src="img/gol_taken.png"><?php } } ?>
                                            <?php if(isset($arr_stat[$real_round]) && $stat['autogol']->getValue()>0) {  for($i=0;$i<$stat['autogol']->getValue();$i++){ ?><img src="img/gol_auto.png"><?php } } ?>
                                            <?php if(isset($arr_stat[$real_round]) && $stat['yellow_card']->getValue()>0) {  for($i=0;$i<$stat['yellow_card']->getValue();$i++){ ?><img src="img/yellow_card.png"><?php } } ?>
                                            <?php if(isset($arr_stat[$real_round]) && $stat['red_card']->getValue()>0) {  for($i=0;$i<$stat['red_card']->getValue();$i++){ ?><img src="img/red_card.png"><?php } } ?>
                                        </div>
                                        <div class="info-player-item">
                        	                <div class="vote value-player-item"><?php if(isset($arr_stat[$real_round])) { if($stat['vote']->getValue()==-1) { echo "S.V."; } else { echo $stat['vote']->getValue(); }  } else { echo " - "; } ?></div>
                        	                <div class="finalvote vote value-player-item"><?php if(isset($arr_stat[$real_round])) { $temp=calc($stat,$player->getPlayer()->getRole()); if($stat['vote']->getValue()==-1) { echo "S.V."; } else { echo $temp; } } else { echo " - "; } ?></div>
                                        </div>
                                    </div>

                            <?php }else{ ?>
                                    <div class="old-player" <?php echo "id=\"".$player->getPlayer()->getId()."\" "; ?>
                                        <?php echo "data-value=\"".$player->getPlayer()->getValue()."\" "; ?>
                                        <?php echo "role=\"".$player->getPlayer()->getRole()."\" "; ?>
                                        <?php echo "name=\"".$player->getPlayer()->getName()."\" "; ?>  >
                                        <div class="role-icon"><span <?php echo "class=\"".strtolower($player->getPlayer()->getRole())."-but\" "; ?> ><?php echo strtoupper($player->getPlayer()->getRole()); ?></span></div>
                                        <div class="name-player-item"><?php echo $player->getPlayer()->getName(); ?>
                                            <?php if(isset($arr_stat[$real_round]) && $stat['scored']->getValue()>0) {  for($i=0;$i<$stat['scored']->getValue();$i++){ ?><img src="img/gol_ball.png"><?php } } ?>
                                            <?php if(isset($arr_stat[$real_round]) && $stat['taken']->getValue()>0) {  for($i=0;$i<$stat['taken']->getValue();$i++){ ?><img src="img/gol_taken.png"><?php } } ?>
                                            <?php if(isset($arr_stat[$real_round]) && $stat['autogol']->getValue()>0) {  for($i=0;$i<$stat['autogol']->getValue();$i++){ ?><img src="img/gol_auto.png"><?php } } ?>
                                            <?php if(isset($arr_stat[$real_round]) && $stat['yellow_card']->getValue()>0) {  for($i=0;$i<$stat['yellow_card']->getValue();$i++){ ?><img src="img/yellow_card.png"><?php } } ?>
                                            <?php if(isset($arr_stat[$real_round]) && $stat['red_card']->getValue()>0) {  for($i=0;$i<$stat['red_card']->getValue();$i++){ ?><img src="img/red_card.png"><?php } } ?>
                                        </div>
                                        <div class="info-player-item">
                        	                <div class="vote value-player-item"><?php if(isset($arr_stat[$real_round])) { if($stat['vote']->getValue()==-1) { echo "S.V."; } else { echo $stat['vote']->getValue(); }  } else { echo " - "; } ?></div>
                        	                <div class="finalvote vote value-player-item"><?php if(isset($arr_stat[$real_round]) && isset($stat['final'])) {  if($stat['final']->getValue()==-1) { echo "S.V."; } else { echo $stat['final']->getValue(); } } else { echo " - "; } ?></div>
                                        </div>
                                    </div>
                            <?php } ?>

                        <?php } //FOREACH PANCHINA ?>

                        <?php if($isCalc) {  ?>
                      
                            <?php
                            $handicaps=$database_handicaps->getHandicapsRoundsByUserId($id_user);
                            $tot_points=$info_round[$id_user]['points'];
                            foreach($handicaps as $handicap){
                                if(intval($handicap->getRound())==intval($real_round)){
                                    $round_handicap=$handicap->getPoints();
                                    $tot_points=$tot_points+$round_handicap;
                                    /*if($result>=66){
                                        $gol=floor(($result-66)/6)+1;
                                    }*/
                                    ?>
                                    <div class="old-player handicap-formation" style="border: none;">
                                        <div class="name-player-item"><span style="color:#CCCCCC;">
                                        <?php if($round_handicap<0) { ?>Penalizzazione<?php }else{?>Bonus<?php } ?> di <?php echo $round_handicap." punti</span> : ".$handicap->getDescription(); ?></div>
                                    </div>
                                    <?php
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

                    <?php }else { // IF COUNT START >0 ?>

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

<?php /*} else { //FINE SE ROUND EXIST ?>
<div class="error_display">Errore , non esiste questo turno </div>
<?php }*/ ?>
<?php include('footer.php'); ?>