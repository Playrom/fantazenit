<?php
include('header.php');

$round;
$competition;

$config=$database->dumpConfig();
$id_competition=$config['default_competition'];   

$seconds=$database_rounds->secondsToClosingTime();

?>

<div class="home_page container-fluid">
    <div class="row">
        <div class="first_row_home">
            <div class="col-md-8">
            <?php if($userId!=null){ ?>
                <div class="welcome three_quarter box_home" <?php if($database_rounds->getTeam($userId,$config['current_round'])->getPlayers()==null) echo "onclick=\"javascript:location.href='maketeam.php'\""; ?> >
                    
                    Benvenuto <?php echo $username; ?><br>
                    <span class="minor">Hai totalizzato <span class="punti_highlight"><?php echo $database_rounds->getInfoRound($config['last-round'])[$userId]['points']; ?></span> punti nella <?php echo $config['last-round']; ?>° Giornata<br>
                    <?php if($database_rounds->getTeam($userId,$config['current_round'])->getPlayers()!=null){ ?>
                        Hai già inserito la Formazione per la <?php echo $config['current_round'] ?>° Giornata
                    <?php }else{ ?>
                        Devi inserire la Formazione per la <?php echo $config['current_round'] ?>° Giornata
                    <?php } ?>
                    
                    </span>
                </div>
            <?php }else{ //if non loggato ?>
                <div class="welcome not_logged three_quarter box_home" onclick="javascript:location.href='signup.php'">
                    Non sei ancora iscritto al Fanta Zenit?<br><span class="click_to_reg">Clicca qui per farlo!</span>
                </div>
            <?php } ?>
            </div>
            <div class="col-md-4">
                <div class="count_closing_time one_quarter box_home">
                    <div class="name_market">Termine Inserimento Formazioni</div>
                    <div id="clock"></div>
                </div>
            </div>
        </div>
    </div>
    
    <?php if($config['last-round']!=null){ ?>
    
    <div class="row">
        <div class="third_row_home row_home">
            <div class="col-md-6">
                <div class="standings_last_round box_home">
                    <div class="name_market">Classifica della <?php echo $config['last-round']; ?>° Giornata</div>
                    <?php echo getStandingsRoundByIdUser($id_competition,-1,$userId); ?>
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