			</div>
           <div id="footer">
	           
	           <div class="menu-top">
	                <ul>
		                <li><a href="home.php">Home</a></li>
	                	<li><a href="lista.php">Quotazioni</a></li>
	                	<li><a href="regolamento.php">Regolamento</a></li>
	                	<li><a href="faq.php">Faq</a></li>
	                	<li><a href="privacypolicy.php">Privacy Policy</a></li>
	                </ul>
                </div>
	           
	           <div class="logozenit">
		           <img src="img/logozenit.png">
	           </div>
	           
           </div>
        </div>
    </div>
    
    <script type="text/javascript">
	    $(document).ready(function() {
		    
		    function cookieController(){
			    $.cookiesDirective.loadScript({
	                uri:'js/google.js',
	                appendTo: 'header'
	            });
		    }
		    
	        $.cookiesDirective({
	            privacyPolicyUri: 'privacypolicy.php',
	            cookieScripts: 'Google Analytics', 
	            explicitConsent: false ,
				scriptWrapper: cookieController,
				message: "Questo sito utilizza i Cookie per poter funzionare correttamente : <br/> a) si avverte che il sito fa uso di cookie tecnici per ricordare la scelta dell'utente, b) si avverte che il sito consente anche l'invio di cookie di terze parti c) si richiede il consenso nella qualità di intermendiari tecnici, d) si può trovare l'informativa completa nella pagina della Privacy Policy,  e) si comunica che la prosecuzione della navigazione mediante accesso ad altra area del sito o selezione di un elemento dello stesso comporta la prestazione del consenso all'uso dei cookie."
	        });
	    });
	</script>
	
</body>
