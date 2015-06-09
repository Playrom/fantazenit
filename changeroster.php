<?php
$title="Mercato di Riparazione";
include('header.php');

$error_code=0;

if(isset($_SESSION['username']) && $_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['old']) && isset($_POST['new'])){
    
    $user=$database_users->getUserByUsername($_SESSION['username']);

    $players=$database_players->dumpSingoliToList(null,null);
    $roster=$user->getPlayers();

    $id_old=$_POST['old'];
    $id_new=$_POST['new'];
    $id_market=$_POST['id_market'];

    $old_player=$roster->searchPlayer($id_old);
    $new_player=$players[$id_new];

    if($old_player!=null && ($user->getBalance() + $old_player->getValue() - $new_player->getValue())>=0){
        $error_code=$database_markets->changePlayer($old_player,$new_player,$user,$players,$id_market);
    }

    $market_pre=$id_market;

}


if(!isset($_SESSION['username'])) {
    $_SESSION['old_url']=$_SERVER['REQUEST_URI'];
    header("Location:login.php");
}else if(isset($_SESSION['username'])){

    $username=$_SESSION['username'];
    $user=$database_users->getUserByUsername($username);

    $players=$database_players->dumpSingoliToList(null, null);
    $roster=$user->getPlayers();

    $markets=$database_markets->getOpenMarkets();

    $roster=$roster->orderByRole();

    $market=null;
    $now_max=null;
    $selected=false;
    $finish=false;


    if(isset($_GET['market']) || count($markets)==1 || isset($market_pre)){

        $id_market=0;
        $market=null;

        if(isset($_GET['market'])){

            $id_market=$_GET['market'];
            $market=$databas_markets->getMarketById($id_market);

        }else if(count($markets)==1){

            $market=$markets[0];
            $id_market=$market->getId();

        }else{

            $id_market=$market_pre;
            $market=$database_markets->getMarketById($id_market);

        }

        $trans=$database_markets->getTransfersByIdMarket($user,$players,$id_market);
        $already_transfer=count($trans);
        
        $selected=true;
        $now_max=$markets[0]->getMaxChange()-$already_transfer;

        if($now_max==0){
            $finish=true;
        }

    }else{
        $selected=false;
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
	                <div class="name-team"><?php echo $user->getNameTeam(); ?></div>
	                <div class="balance">Sessione di Mercato: <?php echo $market->getName(); ?></div>
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
                                echo "<option value=\"".$mar->getId()."\" >".$mar->getName()."</option>";
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
                    	unset($players[$player->getPlayer()->getId()]);

    	
    	            ?>
    		            <div class="old-player" <?php echo "id=\"".$player->getPlayer()->getId()."\" "; ?>
    		                <?php echo "data-value=\"".$player->getPlayer()->getValue()."\" "; ?>
    		                <?php echo "name=\"".$player->getPlayer()->getName()."\" "; ?>  
    			        >
    		                <div class="role-icon"><span <?php echo "class=\"".strtolower($player->getPlayer()->getRole())."-but\" "; ?> ><?php echo strtoupper($player->getPlayer()->getRole()); ?></span></div>
    		                <div class="name-player-item"><?php echo $player->getPlayer()->getName(); ?></div>
    		                <div class="info-player-item">
    		                	<div class="value-player-item"><?php echo $player->getPlayer()->getValue(); ?></div>
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
                            <div id="market-cost">Milioni dopo lo scambio:<span id="market-cost-value" <?php echo "data-value=\"".$user->getBalance()."\" "; ?>><?php echo $user->getBalance(); ?></span></div>
                            <input type="hidden" id="old-form" name="old" value="">
                            <input type="hidden" id="new-form" name="new" value="">
                            <input type="hidden" id="market-id-form" name="id_market" <?php echo "value=\"".$market->getId()."\" "; ?> >
                            <input id="mod-button" type="submit" name="Modifica" value="Salva">
                        </form>
                    </div>

                    <div id="search-box"><input class="search" id="search-element" placeholder="Cerca">
                        <button id="sort-name-button" class="sort" data-sort="name_new_player">Ordina per Nome</button>
                        <button id="sort-value-button" class="sort" data-sort="value_new_player">Ordina per Costo</button>
                    </div>
         
                    <ul class="list" id="free-table" style="display: none;">
    		          <?php  foreach($players as $player){   ?>
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