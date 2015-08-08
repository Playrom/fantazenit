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

$json = $apiAccess->accessApi("/rounds/".($round-1),"GET");

$points = null;

if($json["error"]==false && $userId!=null){
	$points = $json["data"]["results"][$userId]["points"];
}

?>

<div class="container-fluid">
    <div class="row">
        <ul class="slide">
		  <li><img src="slides/slide2.jpg" alt=""></li>
		  <!-- <li><img src="slides/2.jpg" alt=""></li>
		  <li><img src="slides/3.jpg" alt=""></li> -->
		</ul>
    </div>
    
    <div class="row presentation join">
	    <div class="col-md-12">
		    <?php if($userId!=null){ ?>
			    <a href="home.php">ACCEDI</a>
			<?php }else{ ?>
				<a href="signup.php">ISCRIVITI</a>
			<?php } ?>
	    </div>
    </div>
    
    <div class="row presentation">
	    <div class="col-md-12">
		    <h1>Ecco il nuovo Fanta Zenit</h1>
		    
		    <p>Benvenuto nella piattaforma di gioco Fanta Zenit!</p>

			<p>Partecipa con noi e tanti altri ragazzi di Messina e di tutti Italia alla quinta edizione 2015-2016.</p>
			
			<p>Concorri alla vittoria finale dei fantastici premi in palio o alle conquiste settimanali o mensili.</p>
	    </div>
    </div>
    
    <div class="row presentation motivi">
	    <div class="col-md-12">
		    <h1>Perché il Fanta Zenit?</h1>

			<p>Chi vuole sfidare la fortuna gioca ad asta, chi vuole sfidare se stesso e le sue capacità manageriali calcistiche sceglie il Fanta Zenit</p>
			
			<p>7 motivi:</p>
						
			<ol>
			
	            <li><p>Crei la tua rosa con 250 milioni. Ogni 5 giornate il mercato di riparazione esalta le tua capacità e crei plusvalenze per migliorare la tua squadra.</p></li>
	
	            <li><p>Doppia sfida: Somma punti e scontri diretti con diversi campionati e coppe con retrocessioni e promozioni.</p></li>
	
	            <li><p>Iscriviti gioca e gestisci la tua rosa Online su PC, Smartphone e Tablet.</p></li>
	
	            <li><p>Anticipazioni voti ogni domenica sera.</p></li>
	
	            <li><p>Il focus MANAGER of the WEEK ogni giornata sui social network.</p></li>
	
	            <li><p>Tutte le news sulla serie A, i consigli mercato, le probabili formazioni e l’avatar personale in cui mostrare le proprie vittorie. Tutto sulla nostra piattaforma.</p></li>
	
	            <li><p>Premi settimanali e mensili + Super premi finali con premiazione conclusiva.</p></li>
	
	        </ol>

			<p>Vuoi vincere l’iscrizione?</p>
			
			<p>Semplicissimo. Manda un tuo selfie (foto o video) in cui ci racconti la tua passione per il fantacalcio con un episodio divertente. (es. manda un selfie con una figurina del tuo giocatore acquistato a 1 per completare la rosa poi rivelatosi decisivo).</p>
			
			
			<p>Con l'iscrizone di 30€ diventi Membro Sostenitore dell'Associazione Culturale Zenit, e potrai sostenerci nelle nostre attività</p>
			
			<p>Iscriviti e scopri tutte le novità su:<br>
			<a href="www.fantazenit.it">www.fantazenit.it</a><br>
			<a href="www.facebook.com/fantazenit">www.facebook.com/fantazenit</a></p>
	    </div>
    </div>
    
</div>

<script>
    <?php echo "countTo(\"".$seconds."\");"; ?>
</script>

<script src="responsiveslides.min.js"></script>

<script>
  $(function() {
    $(".slide").responsiveSlides();
  });
</script>

<?php include('footer.php'); ?>