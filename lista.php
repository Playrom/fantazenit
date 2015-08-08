<?php
	$title = "Quotazioni del Fanta Zenit";
include('header.php');
?>

<?php
	
	$players = null;

    $json = $apiAccess->accessApi("/players","GET");
    
    if($json["error"]==false){
	    $players = $json["data"];
    }
    
	include('error-box.php');
    
    if($players!=null){
    
    ?>
	    <div class="container-fluid">
	        <div class="row">
	            <div class="col-md-12">
	    		    <div id="team-info">
	    		        <div class="name-team">Quotazioni Fantazenit</div>
	    		    </div>
	            </div>
	        </div>
			
			<div class="row" style="margin-top:10px;">
	            <div class="col-md-12">
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
	        		            	onclick="window.location ='playersinfo.php?id=<?php echo $player["id"]; ?>'"
	        	                    class="free-player"
	        	                    <?php echo "data-value=\"".$player["value"]."\" "; ?>
	        	                    <?php echo "role=\"".$player["role"]."\" "; ?>
	        	                    <?php echo "name=\"".$player["name"]."\" "; ?>
	        	                    <?php if(isset($roster[$player["id"]])){ ?> style="display:none;" in-roster="yes" <?php } ?>
	        	                >
	        		                <div class="role-icon"><span <?php echo "class=\"".strtolower($player["role"])."-but\" "; ?> ><?php echo strtoupper($player["role"]); ?></span></div>
	        		                <div class="name-player-item nam"><?php echo $player["name"]; ?></div>
	        						<div class="info-player-item">
										<div class="team-player-item"><?php echo $player["team"]; ?></div>
	        			                <div class="value-player-item val"><?php echo $player["value"]; ?></div>
	        							<div class="info-player-link-item"><a href="playersinfo.php?id=<?php echo $player["id"];?>">i</a></div>
	        						</div>
	        		            </li>
	                       <?php } ?>
	                    </ul>
	                </div>
	            </div>
			</div>
	    </div>
	    
    <?php 
	    }
	?>






<script src="js/jquery-1.11.0.min.js"></script>
<script src="js/ion.rangeSlider.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/list.js/1.1.1/list.min.js">

</script>





<?php include('footer.php'); ?>