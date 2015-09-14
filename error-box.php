<div class="container-fluid alert-box">
    <!--<div class="alert alert-danger error_display" role="alert">
		<span class="glyphicon glyphicon-alert" aria-hidden="true"></span>
		<span class="sr-only">Error:</span>Fanta Zenit è in BETA , per qualsiasi consiglio o errore contattare Giorgio
	</div>
	
	-->
	
	<div class="alert alert-warning error_display" role="alert">
		<span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span>
		<span class="sr-only"></span>Mettete mi piace sulla nostra <a href="http://www.facebook.com/fantazenit">Pagina Facebook</a> per rimanere aggiornati costantemente con notizie ed approfondimenti.
	</div>
	                
    <?php if($userId!=null && !$json_team["valid_formation"]) { ?>
        <div class="alert alert-danger error_display" role="alert">
			<span class="glyphicon glyphicon-alert" aria-hidden="true"></span>
			<span class="sr-only"></span>Attenzione , Hai modificato la tua rosa dall'ultima formazione inserita
		</div>
    <?php } ?>
    
    <!--<?php if($userId!=null) { ?>
        <div class="alert alert-success error_display" role="alert">
			<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
			<span class="sr-only"></span>Il Primo Mercato di Riparazione è aperto
		</div>
    <?php } ?>-->
    
    <?php if($error_json!=null){
	    foreach($error_json as $error){ ?>
		    <div class="alert alert-danger error_display" role="alert">
				<span class="glyphicon glyphicon-alert" aria-hidden="true"></span>
				<span class="sr-only"></span><?php echo $error["message"]; ?>
			</div>
	    <?php
		}
    } ?>
    
    <?php if($error_messages!=null){
	    foreach($error_messages as $error){ ?>
		    <div class="alert alert-danger error_display" role="alert">
				<span class="glyphicon glyphicon-alert" aria-hidden="true"></span>
				<span class="sr-only"></span>Attenzione , <?php echo $error; ?>
			</div>
	    <?php
		}
    } ?>
    
    <?php if($valid_messages!=null){
	    foreach($valid_messages as $error){ ?>
		    <div class="alert alert-success error_display" role="alert">
				<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
				<span class="sr-only"></span><?php echo $error; ?>
			</div>
	    <?php
		}
    } ?>
</div>

<?php
include('infobox.php');
	?>