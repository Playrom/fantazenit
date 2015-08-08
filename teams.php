<?php
	$title = "Le Squadre";
include('header.php');
?>

<?php
    $round;
    $competition;

    
    if(isset($_GET['id'])){
        $id=$_GET['id']; 
        $team=$apiAccess->accessApi("/users/".$id,"GET");
        $roster=null;


        if(isset($team["data"])){
            $arr=$team["data"];

            $roster=$arr["players"];
            $transfers=$arr["transfers"];
        }

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
       <?php
            
            $json=$apiAccess->accessApi("/users","GET");

            $users=$json["data"];

            $count=0;
            foreach($users as $temp){ 
	            $count++;
                $id = $temp['id'];
                $json=$apiAccess->accessApi("/users/".$id,"GET");

                $team = $json["data"];
                                
				if($count %2 !=0) { echo "<div class=\"row_formation row\" >"; }
                ?>
                <div class="col-md-6">
	                
                    <div <?php if($count&1){echo " class=\"team_item_list\"";}else{echo " class=\"team_item_list\"";} ?> >
	                    
	                    <div class="avatar">
			                <?php if($team["url_avatar"]!=null){
				                echo "<img src=\"".$team["url_avatar"]."\" >"; 
			                }else{ ?>
				                <img src="img/default_avatar.png">
			         <?php  } ?>
		                </div>
		                
		                <div class="info">
	                    
	                        <div class="name_team"><a <?php echo "href=\"?id=".$team["id"]."\""; ?>><?php echo $team["name_team"]; ?></a></div>
	                        <div class="name_user name_team"><?php echo $team["name"]." ".$team["surname"]; ?></div>
	                        <div class="bottom_team name_team">
	                            <div class="credits name_team"><?php echo $team["balance"]; ?> Crediti</div>
	                            <div class="position"></div>
	                        </div>
	                        
		                </div>
                    </div>
                </div>
            <?php
				if($count %2 ==0) { echo "</div>"; }
            }
            ?>
            </div>
       </div>
<?php } ?>
<?php include('footer.php'); ?>