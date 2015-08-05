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
		  <li><img src="slides/slide1.jpg" alt=""></li>
		  <!-- <li><img src="slides/2.jpg" alt=""></li>
		  <li><img src="slides/3.jpg" alt=""></li> -->
		</ul>
    </div>
    
    <div class="row presentation join">
	    <div class="col-md-12">
		    <?php if($userId!=null){ ?>
			    <a href="home.php">ENTRA</a>
			<?php }else{ ?>
				<a href="signup.php">ISCRIVITI</a>
			<?php } ?>
	    </div>
    </div>
    
    <div class="row presentation">
	    <div class="col-md-12">
		    <h1>Torna la Serie A e torna il Fanta Zenit 2015/2016</h3>
	
			<p>Ogni anno tantissimi Fanta Manager si sfidano a trovare con 250 crediti la squadra da battere e le migliori plusvalenze </p>
			
			<ul>
			<li><p>Iscriviti, gioca e gestisci la rosa ONLINE su pc, smartphone e tablet</p></li>
			<li><p>Competizioni a somma punti e a scontri diretti con retrocessioni e promozioni</p></li>
			<li><p>Statistiche, consigli e anticipazioni VOTI sui social network</p></li>
			<li><p>Premi Giornata, premi Mensili e Campionato </p></li>
			<li><p>Scopri tutte le novità e ISCRIVITI al sito www.fantazenit.it (anche per i vecchi iscritti)</p></li>
			<li><p>Consegna della quota di iscrizione (30 euro) entro 22 Agosto</p></li>
			</ul>
			
			
			<p>Fanta Zenit 2015/2016</p>
			
			<p>Il Fantacalcio per veri esperti</p>
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
			<li><p>Giochi Online su PC, Smartphone e Tablet.</p></li>
			<li><p>Anticipazioni voti ogni domenica sera.</p></li>
			<li><p>Il focus MANAGER of the WEEK ogni giornata sui social network.</p></li>
			<li><p>Ogni mercato interviste e consigli ai TOP fantamanager.</p></li>
			<li><p>Premi settimanali e mensili + Super premi finali con premiazione conclusiva.</p></li>
			</ol>
			
			
			<p>Vuoi vincere l’iscrizione?</p>
			
			<p>Semplicissimo. Manda un tuo selfie (foto o video) in cui ci racconti la tua passione per il fantacalcio con un episodio divertente. (es. manda un selfie con una figurina del tuo giocatore acquistato a 1 per completare la rosa poi rivelatosi decisivo).</p>
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