<?php
$title="Creazione Rosa";
include('header.php');
?>

<?php

if(!isset($_SESSION['username'])) {

    $_SESSION['old_url']=$_SERVER['REQUEST_URI'];
    header("Location:login.php");

}else if(isset($_SESSION['username']) && $_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['ids'])){

    $team=$apiAccess->accessApi("/users/".$userId,"GET");
    $user = null;
    
    if($team["error"]==false){
	    $user = $team["data"];
    }

    $ids=$_POST['ids'];
            
    $arr_data = array("ids" => $ids , "id_user" => intval($userId));
    
    $params = array('postParams' => $arr_data);
            
    $json=$apiAccess->accessApi("/users/roster","POST",$params);
    
    var_dump($json);
    
    if($json["error"]){
	    var_dump($json);
    }

}

if($config['creation_market']==0){ ?>

    <div class="alert alert-danger error_display" role="alert">
        <span class="glyphicon glyphicon-alert" aria-hidden="true"></span>
        <span class="sr-only">Error:</span>Non è piu possibile modificare liberamente la rosa
    </div>

<?php }else if(isset($_SESSION['username'])){

    
    $team=$apiAccess->accessApi("/users/".$userId."?orderByRole=true","GET");
    $user = null;
    
    $roster = null;
    
    if($team["error"] == false){
	    $user = $team["data"];
	    $roster=$user["players"];
    }
    

	$json = $apiAccess->accessApi("/players","GET");
    
    if($json["error"]==false){
	    $players = $json["data"];
    }    /* @var RosterList $roster */
    
    

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
    		        <div class="name-team"><?php echo $user["name_team"]; ?></div>
    		        <div class="balance">Soldi Disponibili:<div id="balance-display"><?php echo $user["balance"]; ?></div></div>
    		    </div>
            </div>
        </div>

        <div class="row">
            <div class="side-element col-md-4">

                <div class="roster-item" id="P_free" <?php echo "max=\"".$max_por."\""; ?> >

                    <?php foreach($roster as $player){
                        if(strtolower($player["player"]["role"])=="p"){

                    ?>
                      <div class="old-player" <?php echo "id=\"".$player["player"]["id"]."\" "; ?>
                        <?php echo "data-value=\"".$player["player"]["value"]."\" "; ?>
                        <?php echo "name=\"".$player["player"]["name"]."\" "; ?>  >
                          <div class="role-icon"><span <?php echo "class=\"".strtolower($player["player"]["role"])."-but\" "; ?> ><?php echo strtoupper($player["player"]["role"]); ?></span></div>
                          <div class="name-player-item"><?php echo $player["player"]["name"]; ?></div>
                          <div class="info-player-item">
                        	<div class="value-player-item"><?php echo $player["player"]["value"]; ?></div>
                        </div>
                      </div>
                   <?php }
                    } ?>

                </div>

                <div class="roster-item" id="D_free" <?php echo "max=\"".$max_def."\""; ?>>

                    <?php foreach($roster as $player){

                        if(strtolower($player["player"]["role"])=="d"){

                    ?>
                    <div class="old-player" <?php echo "id=\"".$player["player"]["id"]."\" "; ?>
                        <?php echo "data-value=\"".$player["player"]["value"]."\" "; ?>
                        <?php echo "name=\"".$player["player"]["name"]."\" "; ?>  >
                          <div class="role-icon"><span <?php echo "class=\"".strtolower($player["player"]["role"])."-but\" "; ?> ><?php echo strtoupper($player["player"]["role"]); ?></span></div>
                          <div class="name-player-item"><?php echo $player["player"]["name"]; ?></div>
                          <div class="info-player-item">
                        	<div class="value-player-item"><?php echo $player["player"]["value"]; ?></div>
                        </div>
                      </div>
                   <?php }
                    } ?>

                </div>

                <div class="roster-item" id="C_free" <?php echo "max=\"".$max_cen."\""; ?>>

                    <?php foreach($roster as $player){

                        if(strtolower($player["player"]["role"])=="c"){

                    ?>
                    <div class="old-player" <?php echo "id=\"".$player["player"]["id"]."\" "; ?>
                        <?php echo "data-value=\"".$player["player"]["value"]."\" "; ?>
                        <?php echo "name=\"".$player["player"]["name"]."\" "; ?>  >
                          <div class="role-icon"><span <?php echo "class=\"".strtolower($player["player"]["role"])."-but\" "; ?> ><?php echo strtoupper($player["player"]["role"]); ?></span></div>
                          <div class="name-player-item"><?php echo $player["player"]["name"]; ?></div>
                          <div class="info-player-item">
                        	<div class="value-player-item"><?php echo $player["player"]["value"]; ?></div>
                        </div>
                      </div>
                   <?php }
                    } ?>

                </div>

                <div class="roster-item" id="A_free" <?php echo "max=\"".$max_att."\""; ?>>

                    <?php foreach($roster as $player){

                        if(strtolower($player["player"]["role"])=="a"){

                    ?>
                    <div class="old-player" <?php echo "id=\"".$player["player"]["id"]."\" "; ?>
                        <?php echo "data-value=\"".$player["player"]["value"]."\" "; ?>
                        <?php echo "name=\"".$player["player"]["name"]."\" "; ?>  >
                          <div class="role-icon"><span <?php echo "class=\"".strtolower($player["player"]["role"])."-but\" "; ?> ><?php echo strtoupper($player["player"]["role"]); ?></span></div>
                          <div class="name-player-item"><?php echo $player["player"]["name"]; ?></div>
                          <div class="info-player-item">
                        	<div class="value-player-item"><?php echo $player["player"]["value"]; ?></div>
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
        	                <li class="new-player" <?php echo "id=\"".$player["id"]."_free\" "; 
        		            	echo " id_player=\"".$player["id"]."\" "; ?>
        	                    class="free-player"
        	                    <?php echo "data-value=\"".$player["value"]."\" "; ?>
        	                    <?php echo "role=\"".$player["role"]."\" "; ?>
        	                    <?php echo "name=\"".$player["name"]."\" "; ?>
        	                    <?php if(isset($roster[$player["id"]])){ ?> style="display:none;" in-roster="yes" <?php } ?>
        	                >
        		                <div class="role-icon"><span <?php echo "class=\"".strtolower($player["role"])."-but\" "; ?> ><?php echo strtoupper($player["role"]); ?></span></div>
        		                <div class="name-player-item nam"><?php echo $player["name"]; ?></div>
        						<div class="info-player-item">
        			                <div class="value-player-item val"><?php echo $player["value"]; ?></div>
        							<div class="info-player-link-item"><a href="playersinfo.php?id=<?php echo $player["id"];?>">i</a></div>
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