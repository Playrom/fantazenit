<?php
$title="Creazione Rosa";
include('header.php');
?>

<?php

if(!isset($_SESSION['username'])) {

    $_SESSION['old_url']=$_SERVER['REQUEST_URI'];
    header("Location:login.php");

}else if(isset($_SESSION['username']) && $_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['ids'])){

    $user=$database_users->getUserByUsername($_SESSION['username']);

    $players=$database_players->dumpSingoliToList(null,null);
    $ids_string=$_POST['ids'];

    $database_markets->createRoster($user,$players,$ids_string);

}

if($config['creation_market']==0){ ?>

    <div class="alert alert-danger error_display" role="alert">
        <span class="glyphicon glyphicon-alert" aria-hidden="true"></span>
        <span class="sr-only">Error:</span>Non è piu possibile modificare liberamente la rosa
    </div>

<?php }else if(isset($_SESSION['username'])){

    $username=$_SESSION['username'];
    $user=$database_users->getUserByUsername($username);

    $players=$database_players->dumpSingoliToList(null, null);
    /* @var RosterList $roster */
    $roster=$user->getPlayers();

    $max_por=3;
    $max_def=7;
    $max_cen=7;
    $max_att=6;

    if(isset($config['max_por'])){
        $max_por=intval($config["max_por"]);
    }

    if(isset($config['max_def'])){
        $max_def=intval($config["max_def"]);
    }

    if(isset($config['max_cen'])){
        $max_cen=intval($config["max_cen"]);
    }

    if(isset($config['max_att'])){
        $max_att=intval($config["max_att"]);
    }

    if(isset($_SESSION['roster_not_completed'])){  unset($_SESSION['roster_not_completed']); ?>
       <div class="error_display">Attenzione, hai provato ad inserire una formazione ma la tua Rosa non è completa</div>
    <?php } ?>

     <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
    		    <div id="team-info">
    		        <div class="name-team"><?php echo $user->getNameTeam(); ?></div>
    		        <div class="balance">Soldi Disponibili:<div id="balance-display"><?php echo $user->getBalance(); ?></div></div>
    		    </div>
            </div>
        </div>

        <div class="row">
            <div class="side-element col-md-4">

                <div class="roster-item" id="P_free" <?php echo "max=\"".$max_por."\""; ?> >

                    <?php foreach($roster as $player){
                        if(strtolower($player->getPlayer()->getRole())=="p"){

                    ?>
                      <div class="old-player" <?php echo "id=\"".$player->getPlayer()->getId()."\" "; ?>
                        <?php echo "data-value=\"".$player->getPlayer()->getValue()."\" "; ?>
                        <?php echo "name=\"".$player->getPlayer()->getName()."\" "; ?>  >
                          <div class="role-icon"><span <?php echo "class=\"".strtolower($player->getPlayer()->getRole())."-but\" "; ?> ><?php echo strtoupper($player->getPlayer()->getRole()); ?></span></div>
                          <div class="name-player-item"><?php echo $player->getPlayer()->getName(); ?></div>
                          <div class="info-player-item">
                        	<div class="value-player-item"><?php echo $player->getPlayer()->getValue(); ?></div>
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
                        	<div class="value-player-item"><?php echo $player->getPlayer()->getValue(); ?></div>
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
                        	<div class="value-player-item"><?php echo $player->getPlayer()->getValue(); ?></div>
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
                        	<div class="value-player-item"><?php echo $player->getPlayer()->getValue(); ?></div>
                        </div>
                    </div>
                   <?php }
                    } ?>

                </div>

            </div>

            <div class="side-element col-md-8">
                <div id="save" onclick="getValues()">Salva Roster</div>
                <div id="utility-row">
                    <div class="but" onmouseover="hover(this)" onmouseout="stophover(this)" onclick="select_role_create('P',this)"><div class="but-role p-but">P</div><div class="but-over p-but" style="display: none;"></div></div>
                    <div class="but" onmouseover="hover(this)" onmouseout="stophover(this)" onclick="select_role_create('D',this)"><div class="but-role d-but">D</div><div class="but-over d-but" style="display: none;"></div></div>
                    <div class="but" onmouseover="hover(this)" onmouseout="stophover(this)"  onclick="select_role_create('C',this)"><div class="but-role c-but">C</div><div class="but-over c-but" style="display: none;"></div></div>
                    <div class="but" onmouseover="hover(this)" onmouseout="stophover(this)"  onclick="select_role_create('A',this)"><div class="but-role a-but">A</div><div class="but-over a-but" style="display: none;"></div></div>
                    <div id="value-selector"><input type="text" id="range_1" /></div>

                </div>

                <div id="players">

                    <div id="search-box"><input class="search" id="search-element" placeholder="Cerca">
                        <button id="sort-name-button" class="sort" data-sort="nam">Ordina per Nome</button>
                        <button id="sort-value-button" class="sort" data-sort="val">Ordina per Costo</button>
                    </div>

                    <ul class="list" id="free-table">
                          <?php /* @var Player $player */  foreach($players as $player){   ?>
        	                <li class="new-player" <?php echo "id=\"".$player->getId()."_free\" "; 
        		            	echo " id_player=\"".$player->getId()."\" "; ?>
        	                    class="free-player"
        	                    <?php echo "data-value=\"".$player->getValue()."\" "; ?>
        	                    <?php echo "role=\"".$player->getRole()."\" "; ?>
        	                    <?php echo "name=\"".$player->getName()."\" "; ?>
        	                    <?php if($roster->searchPlayer($player->getId())!=null){ ?> style="display:none;" in-roster="yes" <?php } ?>
        	                >
        		                <div class="role-icon"><span <?php echo "class=\"".strtolower($player->getRole())."-but\" "; ?> ><?php echo strtoupper($player->getRole()); ?></span></div>
        		                <div class="name-player-item nam"><?php echo $player->getName(); ?></div>
        						<div class="info-player-item">
        			                <div class="value-player-item val"><?php echo $player->getValue(); ?></div>
        							<div class="info-player-link-item"><a href="playersinfo.php?id=<?php echo $player->getId();?>">i</a></div>
        						</div>
        		            </li>
                       <?php } ?>
                    </ul>


                </div>
            </div>
        </div><!-- row -->
    </div><!-- container-->

<?php } ?>

<script src="js/jquery-1.11.0.min.js"></script>
<script src="js/ion.rangeSlider.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/list.js/1.1.1/list.min.js"></script>
<script>

    $("body").on('click', '.old-player', remove_roster);


    $("body").on('click', '.new-player', add_roster);

</script>

<?php include('footer.php'); ?>