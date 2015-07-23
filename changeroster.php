<?php
$title="Mercato di Riparazione";
include('header.php');

$error_code=0;
$error_message = null;

if(isset($_SESSION['username']) && $_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['old']) && isset($_POST['new'])){
	
	
	

    $data["id_old"]=$_POST['old'];
    $data["id_new"]=$_POST['new'];
    $data["id_market"]=$_POST['id_market'];
    
    $data["id_user"] = $userId;
    
    $params = array('postParams' => $data);
    
    
    $json=$apiAccess->accessApi("/markets/transfers","POST",$params);
    
    var_dump($json);
    
    if($json["error"]){
	    if(isset($json["error_code"])){
		    $error_code=$json["error_code"];
	    }else{
		    $error_message = $json["message"];
	    }
        
    }

    $market_pre=$data["id_market"];

}


if(!isset($_SESSION['username'])) {
    $_SESSION['old_url']=$_SERVER['REQUEST_URI'];
    header("Location:login.php");
}else if(isset($_SESSION['username'])){

    $team=$apiAccess->accessApi("/users/".$userId."?orderByRole=true","GET");
    $user = $team["data"] ; 
    $roster=null;
    
    $players = null;
    
    $json = $apiAccess->accessApi("/players","GET");
    
    if($json["error"]==false){
	    $players = $json["data"];
    }
    

    if(isset($team["data"])){
        $arr=$team["data"];
		
        $roster=$arr["players"];
    }
    
    $markets=null;


    $json=$apiAccess->accessApi("/markets/open","GET");

    if($json["error"]==false){
	    $markets = $json["data"];
    }
    

    $market=null;
    $now_max=null;
    $selected=false;
    $finish=false;
    

    if(isset($_GET['market']) || count($markets)==1 || isset($market_pre)){

        $id_market=0;
        $market=null;

        if(isset($_GET['market'])){

            $id_market=$_GET['market'];
            
            $json=$apiAccess->accessApi("/markets/$id_market","GET");
    
		    if($json["error"]==false){
			    $market = $json["data"];
		    }

        }else if(count($markets)==1){

            $market=$markets[0];
            $id_market=$market["id"];


        }else{

            $id_market=$market_pre;
            $json=$apiAccess->accessApi("/markets/$id_market","GET");
    
		    if($json["error"]==false){
			    $market = $json["data"];
		    }

        }
        
        $json=$apiAccess->accessApi("/markets/$id_market/transfers/$userId","GET");
            
		$trans = null;
		
	    if($json["error"]==false){
		    $trans = $json["data"];
	    }

        if($trans!=null){
	        $already_transfer=count($trans);
        
	        $selected=true;
	        $now_max=intval($markets[0]["max_change"])-$already_transfer;
	
	        if($now_max==0){
	            $finish=true;
	        }
        }
        

    }else{
        $selected=false;
    }
    
if($error_message!=null){ ?>
	<div class="alert alert-danger error_display" role="alert">
		<span class="glyphicon glyphicon-alert" aria-hidden="true"></span>
		<span class="sr-only"></span><?php echo $error_message; ?>
	</div>
<?php 
}

	if($error_code==-1){  ?>

    	<div class="alert alert-danger error_display" role="alert">
			<span class="glyphicon glyphicon-alert" aria-hidden="true"></span>
			<span class="sr-only"></span>Attenzione , Errore Cambio Non Valido
		</div>
	<?php  
    	}
    ?>

    <?php if(count($markets)==0){ ?>

        <div class="alert alert-danger error_display" role="alert">
            <span class="glyphicon glyphicon-alert" aria-hidden="true"></span>
            <span class="sr-only"></span>Attenzione , nessuna sessione di mercato aperta
        </div>

    <?php } ?>

    <?php if($finish){ ?>

        <div class="alert alert-danger error_display" role="alert">
            <span class="glyphicon glyphicon-alert" aria-hidden="true"></span>
            <span class="sr-only"></span>
        </div>

    <?php } ?>
    
    	
    
    <div class="container-fluid">

    <?php if($finish){ ?>

    <?php }else if($selected){ ?>

        <div class="row">
            <div class="col-md-12">
	            <div id="team-info">
	                <div class="name-team"><?php echo $user["name_team"]; ?></div>
	                <div class="balance">Sessione di Mercato: <?php echo $market["name"]; ?></div>
	            </div>
            </div>
	    </div>
        
    <?php }else if(count($markets)==0){ ?>

    <?php }else{ ?>

        <div class="row">
            <div class="col-md-12">
                <div id="team-info">
                    <form class="form-inline" action="changeroster.php" method="get">
                        <div class="form-group">Sono in corso <?php echo count($markets); ?> Mercati</div>
                        <div class="form-group" style="float:right;">
                            <select name="market" class="form-control">
                            <?php foreach($markets as $mar){
                                echo "<option value=\"".$mar["id"]."\" >".$mar["name"]."</option>";
                            } ?>
                            </select>
                            <input  type="submit" value="Seleziona Mercato">
                        </div>
                    </form>
                </div>
            </div>
        </div>

    <?php  } ?>

    <?php if($selected && !$finish){ ?>

        <div class="row">
            <div class="side-element col-md-4">
                
                <div class="roster-item">
    	
    	            <?php 
    		        foreach($roster as $player){
                    	unset($players[$player["player"]["id"]]);

    	
    	            ?>
    		            <div class="old-player" <?php echo "id=\"".$player["player"]["id"]."\" "; ?>
    		                <?php echo "data-value=\"".$player["player"]["value"]."\" "; ?>
    		                <?php echo "name=\"".$player["player"]["name"]."\" "; ?>  
    			        >
    		                <div class="role-icon"><span <?php echo "class=\"".strtolower($player["player"]["role"])."-but\" "; ?> ><?php echo strtoupper($player["player"]["role"]); ?></span></div>
    		                <div class="name-player-item"><?php echo $player["player"]["name"]; ?></div>
    		                <div class="info-player-item">
    		                	<div class="value-player-item"><?php echo $player["player"]["value"]; ?></div>
    		                </div>
    		            </div>
    	           <?php } ?>

            </div>

            </div>

            <div class="side-element col-md-8">
                <div id="utility-row">
                    <div id="change-left">Cambi Rimasti:<?php echo $now_max; ?></div>
                    <div id="value-selector"><input type="text" id="range_1" /></div>

                </div>

                <div id="players">

                    <div id="market">
                        <div class="market-player" id="market-old"></div>
                        <div class="market-player" id="market-new"></div>
                        <form id="mod-form" action="changeroster.php" method="post">
                            <div id="market-cost">Milioni dopo lo scambio:<span id="market-cost-value" <?php echo "data-value=\"".$user["balance"]."\" "; ?>><?php echo $user["balance"]; ?></span></div>
                            <input type="hidden" id="old-form" name="old" value="">
                            <input type="hidden" id="new-form" name="new" value="">
                            <input type="hidden" id="market-id-form" name="id_market" <?php echo "value=\"".$market["id"]."\" "; ?> >
                            <input id="mod-button" type="submit" name="Modifica" value="Salva">
                        </form>
                    </div>

                    <div id="search-box"><input class="search" id="search-element" placeholder="Cerca">
                        <button id="sort-name-button" class="sort" data-sort="name_new_player">Ordina per Nome</button>
                        <button id="sort-value-button" class="sort" data-sort="value_new_player">Ordina per Costo</button>
                    </div>
         
                    <ul class="list" id="free-table" style="display: none;">
    		          <?php  foreach($players as $player){   ?>
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
        </div> <!-- end row -->

    <?php } ?>

    </div><!-- end container-->

<?php } ?>



<script src="js/jquery-1.11.0.min.js"></script>
<script src="js/ion.rangeSlider.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/list.js/1.1.1/list.min.js"></script>
<script>

    $(".old-player").click(function () {
        ol = this;
        change_market(this, "market-old");

    });

    $(".new-player").click(function () {
        ne = this;
        change_market(this, "market-new");

    });

</script>
<?php include('footer.php'); ?>