<?php
include('header.php');
?>

<?php
    $round;
    $competition;
    $database=new ConnectDatabase("localhost","root","aicon07","fantacalcio",3306);
    $config=$database->dumpConfig();

    
    if(isset($_GET['id'])){
        $id=$_GET['id']; 
        $team=$database->getUserById($id);
        $database->getTransfers($team,$database->dumpSingoliToList(null,null));
        $roster=$team->getPlayers();
        $transfers=$team->getTransfers();
    ?>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div id="team-info">
                        <div class="name-team"><?php echo $team->getNameTeam();?></div>
                        <div class="balance"><?php echo $team->getName()." ".$team->getSurname(); ?></div>
                    </div>
                </div>
            </div>
        

        <div class="row_formation row">
            <div class="col-md-6 team_single">
                <div class="title_team_single">LA ROSA</div>
                
                <div id="side-roster">
                    <div class="roster-item" id="P_free" <?php echo "max=\"".$max_por."\""; ?> >
                    <div class="old-player info_player"><div class="role-icon">*</div><div class="name-player-item">Nome</div><div class="info-player-item"><div class="vote value-player-item">Valore</div><div class="finalvote vote value-player-item">Iniziale</div></div></div>                        <?php foreach($roster as $player){
                            if(strtolower($player->getPlayer()->getRole())=="p"){

                        ?>
                          <div class="old-player" <?php echo "id=\"".$player->getPlayer()->getId()."\" "; ?>
                            <?php echo "data-value=\"".$player->getPlayer()->getValue()."\" "; ?>
                            <?php echo "name=\"".$player->getPlayer()->getName()."\" "; ?>  >
                              <div class="role-icon"><span <?php echo "class=\"".strtolower($player->getPlayer()->getRole())."-but\" "; ?> ><?php echo strtoupper($player->getPlayer()->getRole()); ?></span></div>
                              <div class="name-player-item"><?php echo $player->getPlayer()->getName(); ?></div>
                              <div class="info-player-item">
                                <div class="vote value-player-item"><?php echo $player->getPlayer()->getValue(); ?></div>
                                <div class="finalvote vote value-player-item"><?php echo $player->getPlayer()->getFirstValue(); ?></div>
                              </div>
                          </div>
                       <?php }
                        } ?>

                    </div>

                    <div class="roster-item" id="D_free" <?php echo "max=\"".$max_def."\""; ?>>

                        <?php foreach($roster as $player){

                            if(strtolower($player->getPlayer()->getRole())=="d"){

                        ?>
                        <div class="old-player" <?php echo "id=\"".$player->getPlayer()->getId()."\" "; ?>
                            <?php echo "data-value=\"".$player->getPlayer()->getValue()."\" "; ?>
                            <?php echo "name=\"".$player->getPlayer()->getName()."\" "; ?> >
                            <div class="role-icon"><span <?php echo "class=\"".strtolower($player->getPlayer()->getRole())."-but\" "; ?> ><?php echo strtoupper($player->getPlayer()->getRole()); ?></span></div>
                            <div class="name-player-item"><?php echo $player->getPlayer()->getName(); ?></div>
                            <div class="info-player-item">
                                <div class="vote value-player-item"><?php echo $player->getPlayer()->getValue(); ?></div>
                                <div class="finalvote vote value-player-item"><?php echo $player->getPlayer()->getFirstValue(); ?></div>
                            </div>
                        </div>
                       <?php }
                        } ?>

                    </div>

                    <div class="roster-item" id="C_free" <?php echo "max=\"".$max_cen."\""; ?>>

                        <?php foreach($roster as $player){

                            if(strtolower($player->getPlayer()->getRole())=="c"){

                        ?>
                        <div class="old-player" <?php echo "id=\"".$player->getPlayer()->getId()."\" "; ?>
                            <?php echo "data-value=\"".$player->getPlayer()->getValue()."\" "; ?>
                            <?php echo "name=\"".$player->getPlayer()->getName()."\" "; ?> >
                            <div class="role-icon"><span <?php echo "class=\"".strtolower($player->getPlayer()->getRole())."-but\" "; ?> ><?php echo strtoupper($player->getPlayer()->getRole()); ?></span></div>
                            <div class="name-player-item"><?php echo $player->getPlayer()->getName(); ?></div>
                            <div class="info-player-item">
                                <div class="vote value-player-item"><?php echo $player->getPlayer()->getValue(); ?></div>
                                <div class="finalvote vote value-player-item"><?php echo $player->getPlayer()->getFirstValue(); ?></div>
                            </div>
                        </div>
                       <?php }
                        } ?>

                    </div>

                    <div class="roster-item" id="A_free" <?php echo "max=\"".$max_att."\""; ?>>

                        <?php foreach($roster as $player){

                            if(strtolower($player->getPlayer()->getRole())=="a"){

                        ?>
                        <div class="old-player" <?php echo "id=\"".$player->getPlayer()->getId()."\" "; ?>
                            <?php echo "data-value=\"".$player->getPlayer()->getValue()."\" "; ?>
                            <?php echo "name=\"".$player->getPlayer()->getName()."\" "; ?> >
                            <div class="role-icon"><span <?php echo "class=\"".strtolower($player->getPlayer()->getRole())."-but\" "; ?> ><?php echo strtoupper($player->getPlayer()->getRole()); ?></span></div>
                            <div class="name-player-item"><?php echo $player->getPlayer()->getName(); ?></div>
                            <div class="info-player-item">
                                <div class="vote value-player-item"><?php echo $player->getPlayer()->getValue(); ?></div>
                                <div class="finalvote vote value-player-item"><?php echo $player->getPlayer()->getFirstValue(); ?></div>
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
                    $market=$database->getMarketById($transfer->getIdMarket());
                    $date=ucwords(strftime("%A %e %B %Y , %H:%M",$transfer->getDate()->getTimestamp()));
                    $old=$database->dumpPlayerById(intval($transfer->getOldPlayer()->getPlayer()->getId()));
                    $new=$database->dumpPlayerById(intval($transfer->getNewPlayer()->getPlayer()->getId()));
                    ?>
                    <div class="name_market"><?php echo $market->getName(); ?> - <?php echo $date ?></div>
                    <div class="operation">
                        <div class="old transfers_player">
                            <span class="role-icon"><span <?php echo "class=\"".strtolower($old->getRole())."-but\" "; ?> ><?php echo strtoupper($old->getRole()); ?></span></span>
                            <div class="player_name"><?php echo $old->getName(); ?></div>
                            <div class="info_transfer">
                                <div class="value_transfer"><?php echo $transfer->getOldPlayer()->getCost(); ?></div>
                                <img src="img/redarrow.png">
                            </div>
                        </div>
                        <div class="new transfers_player">
                            <span class="role-icon"><span <?php echo "class=\"".strtolower($new->getRole())."-but\" "; ?> ><?php echo strtoupper($new->getRole()); ?></span></span>
                            <div class="player_name"><?php echo $new->getName(); ?></div>
                            <div class="info_transfer">
                                <div class="value_transfer"><?php echo $transfer->getNewPlayer()->getCost(); ?></div>
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
        </div>

    <?php
    }else{

         ?>

       <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div id="team-info">
                        <div class="name-team"></div>
                        <div class="balance">Tutte le Squadre</div>
                    </div>
                </div>
            </div>
            <div class="row">
        <?php
            
            $users=$database->getUsers();
            $count=0;
            foreach($users as $team){ $count++;  ?>
                <div class="row_formation col-md-6">
                    <div <?php if($count&1){echo " class=\"team_item_list\"";}else{echo " class=\"team_item_list\"";} ?> >
                        <div class="name_team"><a <?php echo "href=\"?id=".$team->getId()."\""; ?>><?php echo $team->getNameTeam(); ?></a></div>
                        <div class="name_user name_team"><?php echo $team->getName()." ".$team->getSurname(); ?></div>
                        <div class="bottom_team name_team">
                            <div class="credits"><?php echo $team->getBalance(); ?> Crediti</div>
                            <div class="position"></div>
                        </div>
                    </div>
                </div>
            <?php
            }
            ?>
            </div>
        </div>
<?php } ?>
<?php include('footer.php'); ?>