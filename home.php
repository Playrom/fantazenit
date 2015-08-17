<?php
include('header.php');

$round;
$competition;


$seconds=$config["seconds_to_closing_time"];

$players = null;
$round = $config['current_round'];
$json = $apiAccess->accessApi("/team/$userId/$round","GET");

if($json["error"]==false){
	$players = $json["data"]["players"];
}

$json = $apiAccess->accessApi("/rounds/last","GET");

$points = null;
$id = null;

if($json["error"]==false && $userId!=null){
	$points = $json["data"]["results"][$userId]["points"];
	$id = $json["data"]["id"];
}

include('error-box.php');

?>

<div class="home_page container-fluid">
    <div class="row">
        <div class="first_row_home">
            <div class="col-md-8">
            <?php if($userId!=null){ ?>
            	<div class="box_home">
	                <div class="welcome three_quarter" <?php if($players==null) echo "onclick=\"javascript:location.href='maketeam.php'\""; ?> >
	                    
	                    Benvenuto <?php echo $username; ?><br>
	                    
	                    <?php 
		                if($config["last-round"] != 0){ ?>
	                    
	                    	<span class="minor">Hai totalizzato <span class="punti_highlight"><?php echo $points; ?></span> punti nella <?php echo $id; ?>° Giornata<br>
	                    
	                    <?php 
		                }
		                
		                if($players!=null){ ?>
	                        
	                        Hai già inserito la Formazione per la <?php echo $config['current_round'] ?>° Giornata
	                    
	                    <?php }else{ ?>
	                        
	                        Devi inserire la Formazione per la <?php echo $config['current_round'] ?>° Giornata
	                    
	                    <?php } ?>
	                    
	                    </span>
	                </div>
	            </div>
            <?php }else{ //if non loggato ?>
                <div class="welcome not_logged three_quarter box_home" onclick="javascript:location.href='signup.php'">
                    Non sei ancora iscritto al Fanta Zenit?<br><span class="click_to_reg">Clicca qui per farlo!</span>
                </div>
            <?php } ?>
            </div>
            <div class="col-md-4 margin-10-when-resize">
	            <div class="box_home">
	                <div class="count_closing_time one_quarter">
	                    <div class="name_market">Termine Inserimento Formazioni</div>
	                    <div id="clock"></div>
	                </div>
	            </div>
            </div>
        </div>
    </div>
    
    <?php if($id!=null && $id!=0){ ?>
    
    <div class="row">
        <div class="third_row_home row_home">
            <div class="col-md-6">
                <div class="standings_last_round box_home">
                    <div class="name_market">Classifica della <?php echo $id; ?>° Giornata</div>
                    <?php echo getStandingsRoundByIdUser($id_competition,$id,$userId); ?>
                </div>
            </div>	
            <div class="col-md-6">
                <div class="standings_general box_home">
                    <div class="name_market">Classifica Generale del Fanta Zenit</div>
                    <?php echo getStandingsByIdUser($id_competition,$userId); ?>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
</div>

<script>
    <?php echo "countTo(\"".$seconds."\");"; ?>
</script>

<?php include('footer.php'); ?>