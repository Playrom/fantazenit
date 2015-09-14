<?php

$title="Inserisci Formazione";
include('header.php');

if(isset($_SESSION['username']) && $_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['ids']) && isset($_POST['reserves']) && isset($_POST['ids_position']) && isset($_POST['reserves_position'])){


	
	$team=$apiAccess->accessApi("/users/".$userId,"GET");
	
	$user = null;
	if($team["error"] == false){
		$user = $team["data"];
	
	
		$data = array();
		
		$ids = $_POST['ids'];
		$ids_position = $_POST['ids_position'];
		
		$reserves = $_POST['reserves'];
		$reserves_position = $_POST['reserves_position'];
		
		$ids_arr = array();
		$reserves_arr = array();
		
		for($i = 0;$i<count($ids) ; $i++){
			$temp = array();
			$temp["id"] = $ids[$i];
			$temp["position"] = $ids_position[$i];
			$ids_arr[] = $temp;
		}
		
		for($i = 0;$i<count($reserves) ; $i++){
			$temp = array();
			$temp["id"] = $reserves[$i];
			$temp["position"] = $reserves_position[$i];
			$reserves_arr[] = $temp;
		}
	
	    $data["ids"]=$ids_arr;
	    $data["reserves"]=$reserves_arr;
	    
	    $data["round"]=intval($_POST['round']);
	    $data["tactic"]=$_POST['tactic'];
	    $data["id_user"]=$user["id"];
	    
	    
	    $params = array('postParams' => $data);
	    	    
	    
	    $json=$apiAccess->accessApi("/teams","POST",$params);
	    
	    
	    if($json["error"]==true){
		    $error_json[] = $json;
	        
	    }
	        
    }

}

if(!isset($_SESSION['username'])) {

    $_SESSION['old_url']=$_SERVER['REQUEST_URI'];
    header("Location:login.php");

}else if(isset($_SESSION['username'])){
	
	$json=$apiAccess->accessApi("/users/".$userId,"GET");

    $user = null;
	if($json["error"] == false){
		$user = $json["data"];
	}else{
		$error_json[] = $json;
	}
		
	$json=$apiAccess->accessApi("/players","GET");
	
	$players = null;
	
	if($json["error"] == false){
		$players=$json["data"];
	}else{
		$error_json[] = $json;
	}
	
	
	

    $roster=$user["players"];

    $round=1;
    
    $num_giocatori=$config['max_por']+$config['max_def']+$config['max_cen']+$config['max_att'];
    
    if(count($roster)!=$num_giocatori){

        $_SESSION["roster_not_completed"]=true;
        header("Location: createroster.php");

    }

    if(isset($config['current_round'])){
        $round=intval($config['current_round']);
    }
    
    $json_round=$apiAccess->accessApi("/rounds/$round","GET");
    
    $possibleToEdit = false;
    
    if($json_round["error"]==false){
	    $possibleToEdit = $json_round["data"]["formations_editing"];
    }else{
		$error_json[] = $json_round;
	}
        
    
    
    
    
    $apiPath = "/team/$userId/$round?orderById=true";
				
	$json_team=$apiAccess->accessApi($apiPath,"GET");
	
	
	
	//$username=$temp['username'];
	
	$temp = null;
	$team = null;
	$result = null ;
	
	if($json_team["error"]==false){
	
	    if($json_team["data"]!=null){
	        $result=$json_team["data"];
	    }
	
	}
	
	$findBySistem=false;
	
	if($result!=null) {
	    if (isset($result["players"])) {
	        $team = $result["players"];
	    }
	}
	
	
	$tempArr=null;
	$start=null;
	$back=null;
	$info_round=null;
	
	$rescued_team = false;
	
	if($team==null && $round>1){
	
	    $json_team=$apiAccess->accessApi("/team/$userId/$round?orderById=true","GET");
	
	    $team = null;
	    $result = null;
	
	    if(!$json_team["error"]){
	        
	        if($json_team["data"]!=null){
	            $result=$json_team["data"];
	        }
	
	    }
	
	    if($result==null){
	
	        $r=$round-1;
	        
	        
	        if($json_team["valid_formation"]){
	
	            $json_team=$apiAccess->accessApi("/team/$userId/$r?orderById=true","GET");
	            
	
	            if(!$json_team["error"]){
	
	                if($json_team["data"]!=null) {
	                    $result = $json_team["data"];
	                    
	                    if($result["players"]!=null){
							$team = $result["players"];
							$rescued_team = true;
						}
	
	                }
	            }
	        }
	    }
	
	   
	}
	
      

    /*$team=$database_rounds->getTeam($user->getId(),$round);
    $tactic=$database_rounds->getTactic($user,$round );

    $rescued_team=false;

    if($tactic==null && $round!=1){
        $team=$database_rounds->getTeam($user->getId(),$round-1);
        $tactic=$database_rounds->getTactic($user,$round-1 );
        $rescued_team=true;
    }
*/
    $max_por=1;
    $max_def=4;
    $max_cen=4;
    $max_att=2;

    if($team!=null){
        $max_def=$result["def"];
        $max_cen=$result["cen"];
        $max_att=$result["att"];
        
    }
    
    $tactic=$max_def.$max_cen.$max_att;

    $num_giocatori=$max_por+$max_def+$max_cen+$max_att;


    $max_role_reserve=2;

    if(isset($config['max-role-reserve'])){
        $max_role_reserve=intval($config["max-role-reserve"]);
    }

    $official_players=($max_role_reserve*3)+1+$num_giocatori;
    
    include("error-box.php");

    if($json_round["data"]["formations_editing"]){

    ?>
    <div id="official_players" <?php echo "number=\"".$official_players."\""; ?> ></div>

        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div id="team-info">
                        <div class="name-team"><?php echo $user["name_team"]; ?></div>
                        <div class="balance">Giornata <?php echo $round; ?></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="side-element col-md-4">
                    <div class="roster-item" id="P_free" <?php echo "max=\"".$max_por."\""; ?> >

                        <?php foreach($roster as $player){
                            if(strtolower($player["player"]["role"])=="p"){

                        ?>
                        <div class="old-player in-roster-player" 
                            <?php echo "id=\"".$player["player"]["id"]."\" "; ?>
                            <?php echo "data-value=\"".$player["player"]["value"]."\" "; ?>
                            <?php echo "role=\"".$player["player"]["role"]."\" "; ?>
                            <?php echo "name=\"".$player["player"]["name"]."\" "; ?>
	                        <?php echo "team=\"".$player["player"]["team"]."\" "; ?>  
                            <?php if(isset($result["players"])) {  if(isset($result["players"][$player["player"]["id"]])) { echo "style=\"display:none;\" "; } } ?>
                        >

                            <div class="role-icon"><span <?php echo "class=\"".strtolower($player["player"]["role"])."-but\" "; ?> ><?php echo strtoupper($player["player"]["role"]); ?></span></div>
                            <div class="name-player-item"><?php echo $player["player"]["name"]; ?></div>
                            <div class="info-player-item">
        						<img <?php echo "src=\"teamlogo/small/".$player["player"]["team"].".png\""; ?> class="team_logo_small" >
								<div class="team-player-item team_for_list"><?php echo $player["player"]["team"]; ?></div>
    							<div class="info-player-link-item"><a href="playersinfo.php?id=<?php echo $player["player"]["id"];?>">i</a></div>
    						</div>
                        </div>
                       <?php }
                        } ?>

                    </div>

                    <div class="roster-item" id="D_free" <?php echo "max=\"".$max_def."\""; ?>>

                        <?php foreach($roster as $player){

                            if(strtolower($player["player"]["role"])=="d"){

                        ?>
                        <div class="old-player in-roster-player" 
                            <?php echo "id=\"".$player["player"]["id"]."\" "; ?>
                            <?php echo "data-value=\"".$player["player"]["value"]."\" "; ?>
                            <?php echo "role=\"".$player["player"]["role"]."\" "; ?>
                            <?php echo "name=\"".$player["player"]["name"]."\" "; ?>  
	                        <?php echo "team=\"".$player["player"]["team"]."\" "; ?>  
                            <?php if(isset($result["players"])) {  if(isset($result["players"][$player["player"]["id"]])) { echo "style=\"display:none;\" "; } } ?>
                        >

                            <div class="role-icon"><span <?php echo "class=\"".strtolower($player["player"]["role"])."-but\" "; ?> ><?php echo strtoupper($player["player"]["role"]); ?></span></div>
                            <div class="name-player-item"><?php echo $player["player"]["name"]; ?></div>
                            <div class="info-player-item">
        						<img <?php echo "src=\"teamlogo/small/".$player["player"]["team"].".png\""; ?> class="team_logo_small" >
								<div class="team-player-item team_for_list"><?php echo $player["player"]["team"]; ?></div>
    							<div class="info-player-link-item"><a href="playersinfo.php?id=<?php echo $player["player"]["id"];?>">i</a></div>
    						</div>
                        </div>
                       <?php }
                        } ?>

                    </div>

                    <div class="roster-item in-roster-player" id="C_free" <?php echo "max=\"".$max_cen."\""; ?>>

                        <?php foreach($roster as $player){

                            if(strtolower($player["player"]["role"])=="c"){

                        ?>
                        <div class="old-player in-roster-player" 
                            <?php echo "id=\"".$player["player"]["id"]."\" "; ?>
                            <?php echo "data-value=\"".$player["player"]["value"]."\" "; ?>
                            <?php echo "role=\"".$player["player"]["role"]."\" "; ?>
                            <?php echo "name=\"".$player["player"]["name"]."\" "; ?> 
	                        <?php echo "team=\"".$player["player"]["team"]."\" "; ?>  
                            <?php if(isset($result["players"])) {  if(isset($result["players"][$player["player"]["id"]])) { echo "style=\"display:none;\" "; } } ?>
                        >

	                        <div class="role-icon"><span <?php echo "class=\"".strtolower($player["player"]["role"])."-but\" "; ?> ><?php echo strtoupper($player["player"]["role"]); ?></span></div>
                            <div class="name-player-item"><?php echo $player["player"]["name"]; ?></div>
                            <div class="info-player-item">
        						<img <?php echo "src=\"teamlogo/small/".$player["player"]["team"].".png\""; ?> class="team_logo_small" >
								<div class="team-player-item team_for_list"><?php echo $player["player"]["team"]; ?></div>
    							<div class="info-player-link-item"><a href="playersinfo.php?id=<?php echo $player["player"]["id"];?>">i</a></div>
    						</div>
                        </div>
                       <?php }
                        } ?>

                    </div>

                    <div class="roster-item " id="A_free" <?php echo "max=\"".$max_att."\""; ?>>

                        <?php foreach($roster as $player){

                            if(strtolower($player["player"]["role"])=="a"){

                        ?>
                        <div class="old-player in-roster-player" 
                            <?php echo "id=\"".$player["player"]["id"]."\" "; ?>
                            <?php echo "data-value=\"".$player["player"]["value"]."\" "; ?>
                            <?php echo "role=\"".$player["player"]["role"]."\" "; ?>
                            <?php echo "name=\"".$player["player"]["name"]."\" "; ?> 
	                        <?php echo "team=\"".$player["player"]["team"]."\" "; ?>  
                            <?php if(isset($result["players"])) {  if(isset($result["players"][$player["player"]["id"]])) { echo "style=\"display:none;\" "; } } ?>
                        >

                            <div class="role-icon"><span <?php echo "class=\"".strtolower($player["player"]["role"])."-but\" "; ?> ><?php echo strtoupper($player["player"]["role"]); ?></span></div>
                            <div class="name-player-item"><?php echo $player["player"]["name"]; ?></div>
                            <div class="info-player-item">
        						<img <?php echo "src=\"teamlogo/small/".$player["player"]["team"].".png\""; ?> class="team_logo_small" >
								<div class="team-player-item team_for_list"><?php echo $player["player"]["team"]; ?></div>
    							<div class="info-player-link-item"><a href="playersinfo.php?id=<?php echo $player["player"]["id"];?>">i</a></div>
    						</div>
                        </div>
                       <?php }
                        } ?>

                    </div>
                </div> <!-- side roster end -->

                <div class="side-element col-md-8">
	                <div id="save" onclick="getValues();">Salva Formazione</div>
                    <div id="utility-row">
                        <select name="module" id="module" <?php echo "round=\"".$round."\""; ?> onchange="changemodule(this)">
                              <?php
                              if(isset($config["available-tactics"])){
                                $modules=explode(";",$config["available-tactics"]);
                              }
                              foreach($modules as $module){
                                if(strtolower($tactic)==strtolower($module)){
                              ?>
                                <option selected="selected" <?php echo "value=\"".$module."\""; ?> ><?php echo $module[0]."-".$module[1]."-".$module[2];?></option>
                              <?php } else { ?>
                                <option <?php echo "value=\"".$module."\""; ?> ><?php echo $module[0]."-".$module[1]."-".$module[2];?></option>
                              <?php } } ?>
                        </select>
                        <?php if($rescued_team) { ?><span class="rescued">Formazione Recuperata dal Turno Precedente</span><?php } ?>
                    </div>

                    

                    <div class="team campo-verde">


                        <div class="roster-item" id="P_table" <?php echo "max=\"".$max_por."\""; ?>>
                            
                            <!-- <div class="old-player p-but"><div class="name-role">Portieri</div></div> -->
							<div class="col-md-4"></div>
                            <?php 
                            if(isset($result["players"])){

                                foreach($result["players"] as $pla){
	                                
	                                if(strtolower($pla["player"]["role"])=="p"){

	                                    if($pla["position"]==0){ ?>
	                                        <?php $player=$pla["player"]; ?>
		                                    <div class="col-md-4">
		                                        <div class="old-player in-team-player" <?php echo " position=\"".$pla["position"]."\" ";?>
		                                            <?php echo "id=\"".$player["id"]."_team\" "; ?>
		                                            <?php echo "name=\"".$player["name"]."\" "; ?> 
		                                            role="P"
		                                            <?php echo "id_player=\"".$player["id"]."\" "; ?> >
		                                            <div class="name-player-item">
			                                            <?php echo $player["name"]; ?>
														<img <?php echo "src=\"teamlogo/small/".$player["team"].".png\""; ?> class="team_logo_small" >
													</div>
		                                        </div>
		                                    </div>
	                            <?php   }
		                            }
                                }
                            }
                            ?>
                            <div class="col-md-4"></div>
                        </div>


                        <div class="roster-item " id="D_table" <?php echo "max=\"".$max_def."\""; ?>>
                            
                            <!-- <div class="old-player d-but"><div class="name-role">Difensori</div></div> -->

                            <?php 
                            if(isset($result["players"])){

                                foreach($result["players"] as $pla){
	                                
	                                if(strtolower($pla["player"]["role"])=="d"){

	                                    if($pla["position"]==0){ ?>
	                                        <?php $player=$pla["player"]; ?>
											<div <?php echo "class=\"col-md-".getCol($max_def)." player_column\""; ?> >
		                                        <div class="old-player in-team-player" <?php echo " position=\"".$pla["position"]."\" ";?>
		                                            <?php echo "id=\"".$player["id"]."_team\" "; ?>
		                                            <?php echo "name=\"".$player["name"]."\" "; ?> 
		                                            role="D"
		                                            <?php echo "id_player=\"".$player["id"]."\" "; ?> >
		                                            <div class="name-player-item">
			                                            <?php echo $player["name"]; ?>
														<img <?php echo "src=\"teamlogo/small/".$player["team"].".png\""; ?> class="team_logo_small" >
													</div>
		                                        </div>
											</div>
	                            <?php   }
		                            }
                                }
                            }
                            ?>
                        </div>

                        <div class="roster-item " id="C_table" <?php echo "max=\"".$max_cen."\""; ?>>
                            
                            <!-- <div class="old-player c-but"><div class="name-role">Centrocampisti</div></div> -->

                            <?php 
                            if(isset($result["players"])){

                                foreach($result["players"] as $pla){
	                                
	                                if(strtolower($pla["player"]["role"])=="c"){

	                                    if($pla["position"]==0){ ?>
	                                        <?php $player=$pla["player"]; ?>
											<div <?php echo "class=\"col-md-".getCol($max_cen)." player_column\""; ?> >
		                                        <div class="old-player in-team-player" <?php echo " position=\"".$pla["position"]."\" ";?>
		                                            <?php echo "id=\"".$player["id"]."_team\" "; ?>
		                                            <?php echo "name=\"".$player["name"]."\" "; ?> 
		                                            role="C"
		                                            <?php echo "id_player=\"".$player["id"]."\" "; ?> >
		                                            <div class="name-player-item">
			                                            <?php echo $player["name"]; ?>
														<img <?php echo "src=\"teamlogo/small/".$player["team"].".png\""; ?> class="team_logo_small" >
													</div>
		                                        </div>
											</div>
	                            <?php   }
		                            }
                                }
                            }
                            ?>
                        </div>

                        <div class="roster-item " id="A_table" <?php echo "max=\"".$max_att."\""; ?>>
                            
                            <!--  <div class="old-player a-but"><div class="name-role">Attaccanti</div></div> -->

                            <?php 
                            if(isset($result["players"])){

                                foreach($result["players"] as $pla){
	                                
	                                if(strtolower($pla["player"]["role"])=="a"){

	                                    if($pla["position"]==0){ ?>
	                                        <?php $player=$pla["player"]; ?>
											<div <?php echo "class=\"col-md-".getCol($max_att)." player_column\""; ?> >
		                                        <div class="old-player in-team-player" <?php echo " position=\"".$pla["position"]."\" ";?>
		                                            <?php echo "id=\"".$player["id"]."_team\" "; ?>
		                                            <?php echo "name=\"".$player["name"]."\" "; ?> 
		                                            role="A"
		                                            <?php echo "id_player=\"".$player["id"]."\" "; ?> >
		                                            <div class="name-player-item">
			                                            <?php echo $player["name"]; ?>
														<img <?php echo "src=\"teamlogo/small/".$player["team"].".png\""; ?> class="team_logo_small" >
													</div>
		                                        </div>
											</div>
	                            <?php   }
		                            }
                                }
                            }
                            ?>
                        </div>

                    </div> <!-- end team -->

                    <div id="reserve_team">
                        <div class="type_name">Panchina</div>

                        <div class="roster-item " id="P_reserve" <?php echo "max=\"1\""; ?>>
                            
                            <div class="old-player p-but"><div class="name-role">Portieri</div></div>

                            <?php 
                            if(isset($result["players"])){
	                            
                                foreach($result["players"] as $pla){
	                                
	                                if(strtolower($pla["player"]["role"])=="p"){

	                                    if($pla["position"]>=1){ ?>
	                                        <?php $player=$pla["player"]; ?>
											<div class="col-md-12" >
		                                        <div class="old-player in-reserve-player" <?php echo " position=\"".$pla["position"]."\" ";?>
		                                            <?php echo "id=\"".$player["id"]."_reserve\" "; ?>
		                                            <?php echo "name=\"".$player["name"]."\" "; ?> 
		                                            role="P"
		                                            <?php echo "id_player=\"".$player["id"]."\" "; ?> >
		                                            <div class="name-player-item">
			                                            <?php echo $player["name"]; ?>
														<img <?php echo "src=\"teamlogo/small/".$player["team"].".png\""; ?> class="team_logo_small" >
													</div>
		                                        </div>
											</div>
	                            <?php   }
		                            }
                                }
                            }
                            ?>
                        </div>


                        <div class="roster-item " id="D_reserve" <?php echo "max=\"".$max_role_reserve."\""; ?>>
                            
                            <div class="old-player d-but"><div class="name-role">Difensori</div></div>

                            <?php 
                            if(isset($result["players"])){
	                            
                                foreach($result["players"] as $pla){
	                                
	                                if(strtolower($pla["player"]["role"])=="d"){

	                                    if($pla["position"]>=1){ ?>
	                                        <?php $player=$pla["player"]; ?>
											<div class="col-md-12" >
		                                        <div class="old-player in-reserve-player" <?php echo " position=\"".$pla["position"]."\" ";?>
		                                            <?php echo "id=\"".$player["id"]."_reserve\" "; ?>
		                                            <?php echo "name=\"".$player["name"]."\" "; ?> 
		                                            role="D"
		                                            <?php echo "id_player=\"".$player["id"]."\" "; ?> >
		                                            <div class="name-player-item">
			                                            <?php echo $player["name"]; ?>
														<img <?php echo "src=\"teamlogo/small/".$player["team"].".png\""; ?> class="team_logo_small" >
													</div>
		                                        </div>
											</div>
	                            <?php   }
		                            }
                                }
                            }
                            ?>
                        </div>

                        <div class="roster-item " id="C_reserve" <?php echo "max=\"".$max_role_reserve."\""; ?>>
                            
                            <div class="old-player c-but"><div class="name-role">Centrocampisti</div></div>

                            <?php 
                            if(isset($result["players"])){
	                            
                                foreach($result["players"] as $pla){
	                                
	                                if(strtolower($pla["player"]["role"])=="c"){

	                                    if($pla["position"]>=1){ ?>
	                                        <?php $player=$pla["player"]; ?>
											<div class="col-md-12" >
		                                        <div class="old-player in-reserve-player" <?php echo " position=\"".$pla["position"]."\" ";?>
		                                            <?php echo "id=\"".$player["id"]."_reserve\" "; ?>
		                                            <?php echo "name=\"".$player["name"]."\" "; ?> 
		                                            role="C"
		                                            <?php echo "id_player=\"".$player["id"]."\" "; ?> >
		                                            <div class="name-player-item">
			                                            <?php echo $player["name"]; ?>
														<img <?php echo "src=\"teamlogo/small/".$player["team"].".png\""; ?> class="team_logo_small" >
													</div>
		                                        </div>
											</div>
	                            <?php   }
		                            }
                                }
                            }
                            ?>
                        </div>

                        <div class="roster-item " id="A_reserve" <?php echo "max=\"".$max_role_reserve."\""; ?>>
                            
                            <div class="old-player a-but"><div class="name-role">Attaccanti</div></div>

                            <?php 
                            if(isset($result["players"])){
	                            
                                foreach($result["players"] as $pla){
	                                
	                                if(strtolower($pla["player"]["role"])=="a"){

	                                    if($pla["position"]>=1){ ?>
	                                        <?php $player=$pla["player"]; ?>
											<div class="col-md-12" >
		                                        <div class="old-player in-reserve-player" <?php echo " position=\"".$pla["position"]."\" ";?>
		                                            <?php echo "id=\"".$player["id"]."_reserve\" "; ?>
		                                            <?php echo "name=\"".$player["name"]."\" "; ?> 
		                                            role="A"
		                                            <?php echo "id_player=\"".$player["id"]."\" "; ?> >
		                                            <div class="name-player-item">
			                                            <?php echo $player["name"]; ?>
														<img <?php echo "src=\"teamlogo/small/".$player["team"].".png\""; ?> class="team_logo_small" >
													</div>
		                                        </div>
											</div>
	                            <?php   }
		                            }
                                }
                            }
                            ?>
                        </div>

                    </div> <!-- reserve end -->
                </div>
            </div> <!-- row end -->
        </div> <!-- container end -->
        <?php }else{ ?>
        <div class="alert alert-danger error_display" role="alert">
            <span class="glyphicon glyphicon-alert" aria-hidden="true"></span>
            <span class="sr-only"></span>Attenzione , non è piu possibile inserire la formazione
        </div>
      <?php } ?>
<?php }

?>


<script src="js/jquery-1.11.0.min.js"></script>
<script src="js/ion.rangeSlider.min.js"></script>
<script>
	$.noConflict();
	
	var getCol = function(num){

		switch(parseInt(num)){
			case 4:
				return 3;
			case 3:
				return 4;
			case 2:
				return 6;
			case 1:
				return 12;
			default:
				return 12;
		}
	}



    var changemodule=function(mo){

        var tact=mo.value;

        var def=tact[0];
        var cen=tact[1];
        var att=tact[2];

        var def_tab=document.getElementById("D_table");
        def_tab.setAttribute("max",def);
        in_module_change(def_tab,def);

        var cen_tab=document.getElementById("C_table");
        cen_tab.setAttribute("max",cen);
        in_module_change(cen_tab,cen);


        var att_tab=document.getElementById("A_table");
        att_tab.setAttribute("max",att);
        in_module_change(att_tab,att);

        };

    var in_module_change=function(def_tab,def){


        var len=def_tab.getElementsByClassName("player_column").length;

        
        var elements = def_tab.getElementsByClassName("player_column");
        
        for(var i = 0 ; i< elements.length ; i++){
	        elements[i].className = "col-md-" + getCol(def) + " player_column";
        }

        if(len>def){
            var diff=len-def;
            def=parseInt(def);

            for(var i=0;i<diff;i++){
                var obj=def_tab.getElementsByClassName("in-team-player")[def];

                var roster_table=document.getElementById(obj.getAttribute("role"));
                var id=obj.getAttribute("id_player");
                var id_element=id;
                var table_element=document.getElementById(id_element);
                table_element.style.display="block";
                
                var element = document.getElementById(obj.id);
                
                var toRemove = element.parentNode;
                
                toRemove.parentNode.removeChild(toRemove);
            }

        }
    };

    var change = function(obj){
        var min=obj["fromNumber"];
        var max=obj["toNumber"];
        
        var players=document.getElementsByClassName("new-player");
        for (var i = 0; i < players.length; ++i) {
            var player = players[i];
            var value=player.getAttribute("data-value");
            if(value<min || value>max){
                player.style.display="none";
            }else{
                player.style.display="table-row";
            }
        }

    };
    var ol=null,ne=null;
    var remove_roster=function(pass){
        var obj=pass.currentTarget;

        var roster_table=document.getElementById(obj.getAttribute("role")+"_free");

        var id=obj.getAttribute("id_player");

        var id_element=id;

        var table_element=document.getElementById(id_element);

        table_element.style.display="block";
        var index=obj.rowIndex;

		obj.parentNode.parentNode.removeChild(obj.parentNode);

    };

    var remove_reserve=function(pass){
        var obj=pass.currentTarget;

        var roster_table=document.getElementById(obj.getAttribute("role"));

        var id=obj.getAttribute("id_player");
        
        var original_pos = obj.getAttribute("position");

        var id_element=id;

        var table_element=document.getElementById(id_element);

        table_element.style.display="block";
        var index=obj.rowIndex;
        
        var table = obj.parentNode.parentNode;
        
        
        obj.parentNode.parentNode.removeChild(obj.parentNode);
        
        
        var arr = table.getElementsByClassName("in-reserve-player");
        
        
        for(var i=0; i < arr.length; i++){
	        var pos = arr[i].getAttribute("position");

	        if(pos!=null && pos>original_pos){
	        	arr[i].setAttribute("position", pos-1);
	        }
        }


    };

    var add_roster=function(pass){
        var obj=pass.currentTarget;


        var table=document.getElementById(obj.getAttribute("role")+"_table");


        var max_team=parseInt(table.getAttribute("max"));
        var lenght_table=table.getElementsByClassName("in-team-player").length;


        if(lenght_table<max_team){

            var row = document.createElement('div');

            row.className = "old-player in-team-player";
            row.id = obj.getAttribute("id")+"_team";

            row.setAttribute("name", obj.getAttribute("name"));
            row.setAttribute("role",obj.getAttribute("role"));
            row.setAttribute("id_player",obj.getAttribute("id"));
            row.setAttribute("position","0");

            var namecell = document.createElement('div');
            namecell.innerHTML = obj.getAttribute("name");
            namecell.className = "name-player-item";
            
            var imagecell = document.createElement('img');
			imagecell.src = "/teamlogo/small/" + obj.getAttribute("team") + ".png";
            
            namecell.appendChild(imagecell);
            
            var wrapper = document.createElement('div');
                        
            wrapper.className = "player_column col-md-" + getCol(max_team);
            
            
            row.appendChild(namecell);
            wrapper.appendChild(row);

            table.appendChild(wrapper);


            obj.style.display="none";

        }else{

            var table_reserve=document.getElementById(obj.getAttribute("role")+"_reserve");


            var max_reserve=parseInt(table_reserve.getAttribute("max"));

            var lenght_table_reserve=table_reserve.getElementsByClassName("in-reserve-player").length;

            if(lenght_table_reserve<max_reserve){

                var row = document.createElement('div');

                row.className = "old-player in-reserve-player";
                row.id = obj.getAttribute("id")+"_reserve";

                row.setAttribute("name", obj.getAttribute("name"));
                row.setAttribute("role",obj.getAttribute("role"));
                row.setAttribute("id_player",obj.getAttribute("id"));
                row.setAttribute("position",lenght_table_reserve+1);

                var namecell = document.createElement('div');
                namecell.innerHTML = obj.getAttribute("name");
                namecell.className = "name-player-item";
                
                var imagecell = document.createElement('img');
				imagecell.src = "/teamlogo/small/" + obj.getAttribute("team") + ".png";
	            
	            namecell.appendChild(imagecell);
                
                row.appendChild(namecell);
                
                var toAdd = document.createElement('div');
                toAdd.className = "col-md-12";
                toAdd.appendChild(row);
                
                table_reserve.appendChild(toAdd);


                obj.style.display="none";

            };
        };


    };

    var getValues=function(){
        var jsonObj = [];
        var reserves=[];
        var jsonString;
        var table = document.getElementById("P_table");
        for (var r = 0, n = table.getElementsByClassName("in-team-player").length; r < n; r++) {
            var player = table.getElementsByClassName("in-team-player")[r].getAttribute("id_player");
            var position = table.getElementsByClassName("in-team-player")[r].getAttribute("position");
            var item = [player,position];
            jsonObj.push(item);
        };
        

        var table = document.getElementById("D_table");
        for (var r = 0, n = table.getElementsByClassName("in-team-player").length; r < n; r++) {
            var player = table.getElementsByClassName("in-team-player")[r].getAttribute("id_player");
            var position = table.getElementsByClassName("in-team-player")[r].getAttribute("position");
            var item = [player,position];
            jsonObj.push(item);
        };

        var table = document.getElementById("C_table");
        for (var r = 0, n = table.getElementsByClassName("in-team-player").length; r < n; r++) {
            var player = table.getElementsByClassName("in-team-player")[r].getAttribute("id_player");
            var position = table.getElementsByClassName("in-team-player")[r].getAttribute("position");
            var item = [player,position];
            jsonObj.push(item);
        };

        var table = document.getElementById("A_table");
        for (var r = 0, n = table.getElementsByClassName("in-team-player").length; r < n; r++) {
            var player = table.getElementsByClassName("in-team-player")[r].getAttribute("id_player");
            var position = table.getElementsByClassName("in-team-player")[r].getAttribute("position");
            var item = [player,position];
            jsonObj.push(item);
        };



        var table = document.getElementById("P_reserve");
        for (var r = 0, n = table.getElementsByClassName("in-reserve-player").length; r < n; r++) {
            var player = table.getElementsByClassName("in-reserve-player")[r].getAttribute("id_player");
            var position = table.getElementsByClassName("in-reserve-player")[r].getAttribute("position");
            var item = [player,position];
            reserves.push(item);
        };

        var table = document.getElementById("D_reserve");
        for (var r = 0, n = table.getElementsByClassName("in-reserve-player").length; r < n; r++) {
            var player = table.getElementsByClassName("in-reserve-player")[r].getAttribute("id_player");
            var position = table.getElementsByClassName("in-reserve-player")[r].getAttribute("position");
            var item = [player,position];
            reserves.push(item);
        };

        var table = document.getElementById("C_reserve");
        for (var r = 0, n = table.getElementsByClassName("in-reserve-player").length; r < n; r++) {
            var player = table.getElementsByClassName("in-reserve-player")[r].getAttribute("id_player");
            var position = table.getElementsByClassName("in-reserve-player")[r].getAttribute("position");
            var item = [player,position];
            reserves.push(item);
        };

        var table = document.getElementById("A_reserve");
        for (var r = 0, n = table.getElementsByClassName("in-reserve-player").length; r < n; r++) {
            var player = table.getElementsByClassName("in-reserve-player")[r].getAttribute("id_player");
            var position = table.getElementsByClassName("in-reserve-player")[r].getAttribute("position");
            var item = [player,position];
            reserves.push(item);
        };

        var tot=reserves.length+jsonObj.length;
        var official=document.getElementById("official_players").getAttribute("number");

  
  
        if(tot==official){



           var url = 'maketeam.php';
           var text='<form action="' + url + '" method="post">';
           
           

           for(var i=0, n=jsonObj.length;i<n;i++){
               text=text+'<input type="hidden" name="ids[]" value="'+jsonObj[i][0]+'" />';
			   text=text+'<input type="hidden" name="ids_position[]" value="'+jsonObj[i][1]+'" />';
           }

           for(var i=0, n=reserves.length;i<n;i++){
               text=text+'<input type="hidden" name="reserves[]" value="'+reserves[i][0]+'" />';
               text=text+'<input type="hidden" name="reserves_position[]" value="'+reserves[i][1]+'" />';
           }

           var tactic_form=document.getElementById("module");

           var tactic=tactic_form.options[tactic_form.selectedIndex].value;


           var round=tactic_form.getAttribute("round");
           text=text+'<input type="hidden" name="round" value="'+round+'" />';
           text=text+'<input type="hidden" name="tactic" value="'+tactic+'" />';

            var form = $(text + '</form>');
            
            console.log(form);


            $('body').append(form);  // This line is not necessary
            $(form).submit();

        }


    };

    var balance=function(){
        var item=document.getElementById("market-cost");
        return parseInt(item.innerHTML);
    };

    /* var cost_change=function(cost,add){
        if(add){
            var item=document.getElementById("market-cost");
            var balance=item.innerHTML;
            item.innerHTML=parseInt(balance)-parseInt(cost);
            var value=parseInt(item.innerHTML);
            if(value<0) disable_but(true); else disable_but(false);

        }else{
           var item=document.getElementById("market-cost");
            var balance=item.innerHTML;
            item.innerHTML=parseInt(balance)+parseInt(cost);
            var value=parseInt(item.innerHTML);
            if(value<0) disable_but(true); else disable_but(false);

        }
    }; */


    $("body").on('click','.in-team-player',remove_roster);
    $("body").on('click','.in-reserve-player',remove_reserve);

    $("body").on('click','.in-roster-player',add_roster);

</script>

<?php include('footer.php'); ?>

<?php function getCol($num){
	switch($num){
		case 4:
			return 3;
		case 3:
			return 4;
		case 2:
			return 6;
		case 1:
			return 12;
		default:
			return 12;
	}
}