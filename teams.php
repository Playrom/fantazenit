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

        <div id="team-info">
            <div class="name-team"><?php echo $team->getNameTeam();?></div>
            <div class="balance"><?php echo $team->getName()." ".$team->getSurname(); ?></div>
        </div>

        <div class="main team_single team_list">
            <div class="title_team_single half_size">LA ROSA</div>
            <div class="title_team_single title_transfers_single half_size">OPERAZIONI DI MERCATO</div>
            <div id="side-roster" class="half_size">
                <table class="roster-item" id="P_free"  >
                    <tr class="old-player info_player"><th colspan="2">Nome Calciatore</th><th>Valore</th><th>Valore Iniziale</th></tr>
                    <?php foreach($roster as $player){
                        if(strtolower($player->getPlayer()->getRole())=="p"){

                    ?>
                      <tr class="old-player" <?php echo "id=\"".$player->getPlayer()->getId()."\" "; ?>
                        <?php echo "data-value=\"".$player->getPlayer()->getValue()."\" "; ?>
                        <?php echo "name=\"".$player->getPlayer()->getName()."\" "; ?>  >
                          <td class="role-icon"><span <?php echo "class=\"".strtolower($player->getPlayer()->getRole())."-but\" "; ?> ><?php echo strtoupper($player->getPlayer()->getRole()); ?></span></td>
                          <td><?php echo $player->getPlayer()->getName(); ?></td>
                          <td class="value_player"><?php echo $player->getPlayer()->getValue(); ?></td>
                          <td class="value_player"><?php echo $player->getPlayer()->getFirstValue(); ?></td>

                      </tr>
                   <?php }
                    } ?>


                    <?php foreach($roster as $player){

                        if(strtolower($player->getPlayer()->getRole())=="d"){

                    ?>
                    <tr class="old-player" <?php echo "id=\"".$player->getPlayer()->getId()."\" "; ?>
                        <?php echo "data-value=\"".$player->getPlayer()->getValue()."\" "; ?>
                        <?php echo "name=\"".$player->getPlayer()->getName()."\" "; ?> >
                        <td class="role-icon"><span <?php echo "class=\"".strtolower($player->getPlayer()->getRole())."-but\" "; ?> ><?php echo strtoupper($player->getPlayer()->getRole()); ?></span></td>
                        <td><?php echo $player->getPlayer()->getName(); ?></td>
                        <td class="value_player"><?php echo $player->getPlayer()->getValue(); ?></td>
                        <td class="value_player"><?php echo $player->getPlayer()->getFirstValue(); ?></td>
                    </tr>
                   <?php }
                    } ?>

                    <?php foreach($roster as $player){

                        if(strtolower($player->getPlayer()->getRole())=="c"){

                    ?>
                    <tr class="old-player" <?php echo "id=\"".$player->getPlayer()->getId()."\" "; ?>
                        <?php echo "data-value=\"".$player->getPlayer()->getValue()."\" "; ?>
                        <?php echo "name=\"".$player->getPlayer()->getName()."\" "; ?> >
                        <td class="role-icon"><span <?php echo "class=\"".strtolower($player->getPlayer()->getRole())."-but\" "; ?> ><?php echo strtoupper($player->getPlayer()->getRole()); ?></span></td>
                        <td><?php echo $player->getPlayer()->getName(); ?></td>
                        <td class="value_player"><?php echo $player->getPlayer()->getValue(); ?></td>
                        <td class="value_player"><?php echo $player->getPlayer()->getFirstValue(); ?></td>
                    </tr>
                   <?php }
                    } ?>


                    <?php foreach($roster as $player){

                        if(strtolower($player->getPlayer()->getRole())=="a"){

                    ?>
                    <tr class="old-player" <?php echo "id=\"".$player->getPlayer()->getId()."\" "; ?>
                        <?php echo "data-value=\"".$player->getPlayer()->getValue()."\" "; ?>
                        <?php echo "name=\"".$player->getPlayer()->getName()."\" "; ?> >
                        <td class="role-icon"><span <?php echo "class=\"".strtolower($player->getPlayer()->getRole())."-but\" "; ?> ><?php echo strtoupper($player->getPlayer()->getRole()); ?></span></td>
                        <td><?php echo $player->getPlayer()->getName(); ?></td>
                        <td class="value_player"><?php echo $player->getPlayer()->getValue(); ?></td>
                        <td class="value_player"><?php echo $player->getPlayer()->getFirstValue(); ?></td>
                    </tr>
                   <?php }
                    } ?>

                </table>

            </div>
            
            <div class="transfers half_size">
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

    <?php
    }else{

         ?>

        <div id="team-info">
            <div class="name-team">
            
            </div>
            <div class="balance">Tutte le Squadre</div>
        </div>

        <div class="main team_list">

    <?php
        
        $users=$database->getUsers();
        $count=0;
        foreach($users as $team){ $count++;  ?>
            <div <?php if($count&1){echo " class=\"team_item_list half_size\"";}else{echo " class=\"team_item_list half_size margin_left\"";} ?> >
                <div class="name_team"><a <?php echo "href=\"?id=".$team->getId()."\""; ?>><?php echo $team->getNameTeam(); ?></a></div>
                <div class="name_user name_team"><?php echo $team->getName()." ".$team->getSurname(); ?></div>
                <div class="bottom_team name_team">
                    <div class="credits"><?php echo $team->getBalance(); ?> Crediti</div>
                    <div class="position"></div>
                </div>
            </div>
        <?php
        }
        ?>
        </div>
<?php } ?>
<?php include('footer.php'); ?>