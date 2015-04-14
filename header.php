<?php

function __autoload($class_name) {
    require_once 'class/'.$class_name . '.php';
}

ob_start();
session_start();
setlocale(LC_ALL, 'it_IT.UTF-8'); 


$database = new ConnectDatabase("localhost","root","aicon07","fantacalcio",3306);
$database_competitions = new ConnectDatabaseCompetitions($database->mysqli);
$database_users = new ConnectDatabaseUsers($database->mysqli);
$database_rounds = new ConnectDatabaseRounds($database->mysqli);
$database_players = new ConnectDatabasePlayers($database->mysqli);
$database_markets = new ConnectDatabaseMarkets($database->mysqli);


$competitions=$database_competitions->getCompetitions();

$config=$database->dumpConfig();

require_once 'functions.php';

if(isset($_POST['competition_change'])){
	$new_comp=$_POST['competition_change'];
	$_SESSION['last_competition']=$new_comp;
}

if(!isset($_SESSION['last_competition'])){
    $_SESSION['last_competition']=$config['default_competition'];
}

$id_comp=$_SESSION['last_competition'];

$user=null;
$id_user=-1;

if(isset($_SESSION['username'])){
        $username=$_SESSION['username'];
        $user=$database_users->getUserByUsername($username);
        $id_user=$user->getId();
}

$round=1;

if(isset($config['current_round'])){
    $round=intval($config['current_round']);
}

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Fanta Zenit BETA <?php if(isset($title)){ echo " - ".$title; } ?></title>
        
        <link href="css/ion.rangeSlider.css" rel="stylesheet" />
        <link href="css/normalize.min.css" rel="stylesheet"/>
        <link href="css/ion.rangeSlider.skinFlat.css" rel="stylesheet" />
        <link href="css/style.css" rel="stylesheet" />
        <link href="css/jquery.datetimepicker.css" rel="stylesheet" />
        <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,300' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
		<link rel="stylesheet" href="css/county.css">
        <link href="css/footable.core.css" rel="stylesheet" type="text/css" />
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <!--<link rel="stylesheet" href="//cdn.jsdelivr.net/chartist.js/latest/chartist.min.css">-->
        
		<script src="//cdn.jsdelivr.net/chartist.js/latest/chartist.min.js"></script>
        <script src="js/jquery-1.11.0.min.js"></script>
        <script src="js/scripts-made.js"></script>
        <script src="js/scripts-validation-forms.js"></script>
        <script src="js/jquery.datetimepicker.js"></script>
		<script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
		<script src="js/ion.rangeSlider.min.js"></script>
		<script src="js/chart.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/list.js/1.1.1/list.min.js"></script>
		<script src="js/county.js"></script>
        <script src="js/footable.js" type="text/javascript"></script>
        <script src="js/bootstrap.min.js"></script>

        <script>
		  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

		  ga('create', 'UA-26133531-5', 'auto');
		  ga('send', 'pageview');

		</script>
        
        
		<script>
			var changecompetition=function(mo){
		        console.log(mo);
		        var id=mo.value;

		       var text='<form action="' + document.URL + '" method="post">';

		       text=text+'<input type="hidden" name="competition_change" value="'+id+'" />';


		        var form = $(text + '</form>');
		        console.log(form);

		        $('body').append(form);  // This line is not necessary
		        $(form).submit();
			};

			$(document).ready(function(){
				$(function() {
					$( "#datepicker" ).datetimepicker({ format: "d-m-Y" });
					$( "#datetimepicker" ).datetimepicker({ format: "d-m-Y H:i" , step:15 , minTime:"12:00",maxTime:"21:00"});
					$( "#datetimepicker_finish" ).datetimepicker({ format: "d-m-Y H:i" , step:15 , minTime:"12:00",maxTime:"21:00"});
				});

				$(".selection_round").change(function() {
					ele=$(this).find("option").filter(":selected");
				    val=$(ele).attr("value");
				    console.log($(this).parent());
				    arr=$(document).find("select.selection_round");
				    val=parseInt(val);
				    for(var i=0;i<arr.length;i++){
				    	console.log($(arr[i]));
				    	console.log($(this));
					   	if($(arr[i])!=$(this) && $(arr[i]).find("option").filter(":selected").attr("value")==val){
						   	console.log("enter");
						   	$(arr[i]).find("option").filter(":selected").removeProp("selected");
					   	}
				    }
				});

			});
			        </script>

    </head>

    <body>

        <div id="wrapper">
            <div id="header">

                <a href="/index.php"><div id="logo"></div></a>
                <div id="menu-top">
	                <ul>
		                <li><a href="index.php">Home</a></li>
	                	<li><a href="formations.php">Formazioni</a></li>
	                	<li><a href="teams.php">Squadre</a></li>
	                	<li><a href="standings.php">Classifiche</a></li>
		                <?php if($user!=null) { ?><li><a href="logout.php">Logout</a></li><?php } else { ?><li><a href="login.php">Login</a></li><?php } ?>
	                </ul>
                </div>
            </div>
            <!-- <div id="menu-settings"></div> -->
            <?php if($user!=null) { ?>
	            <div class="menu-info">
	                <li><a href="maketeam.php">Inserisci Formazione</a></li>
	                <li><a href="createroster.php">Crea Rosa</a></li>
	                <li><a href="changeroster.php">Mercato di Riparazione</a></li>
	            </div>
	        <?php } ?>

	        <?php if($user!=null && $user->getAuth()==1) { ?>
	            <div class="menu-info menu-settings">
	                <li><a href="gestionegiornate.php">Gestione Giornate</a></li>
	                <li><a href="loadfile.php">Carica Dati</a></li>
	                <li><a href="settings.php">Impostazioni</a></li>
	            </div>
	        <?php } ?>

            <div id="content">
                <div class="alert alert-danger error_display" role="alert">
					<span class="glyphicon glyphicon-alert" aria-hidden="true"></span>
					<span class="sr-only">Error:</span>Fanta Zenit è in BETA , per qualsiasi consiglio o errore contattare Giorgio
				</div>
				                
                <?php if($user!=null && !$database_rounds->isValidFormation(intval($user->getId()),intval($round))) { ?>
                    <div class="alert alert-danger error_display" role="alert">
						<span class="glyphicon glyphicon-alert" aria-hidden="true"></span>
						<span class="sr-only"></span>Attenzione , Hai modificato la tua rosa dall'ultima formazione inserita
					</div>
                <?php } ?>