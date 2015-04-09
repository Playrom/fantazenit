# Alpha 0.3 - 31 Marzo 2015

* Mercato di Riparazione
* Mercato di Apertura
* Competizioni a somma punti
* Home page
* Classifiche Generali e di Giornata

# Alpha 0.3.1 - 7 Aprile 2015 

* Bootstraped Inserimento Formazione
* Bootstraped Mercato di ripazione
* Bootstraped Impostazioni Mercati
* Aggiunta funzione getLastStat che segnala la giornata dei ultimi voti disponibili
* Limitato calcolo a solo giornate con statistiche in database

# Alpha 0.3.2 - 9 Aprile 2015

Modificato l'approccio alla connessione al database, e quindi di conseguenza sono state modificate praticamente tutte le pagine dinamiche del sito.
Contestualmente si è provveduto ad una pulizia generale del codice

* Bugfix: standings.php
* Edit: Pulizia dei file e delle cartelle
* Edit: Rimosso getLastStat , ridondante con getLastStatRound in ConnectDatabaseRounds
* Edit: Modificati classi ConnectDatabase, parente unico con classi differenziate
* Edit _ConnectDatabase_: gestionegiornate.php
* Edit _ConnectDatabase_: header.php
* Edit _ConnectDatabase_: login.php
* Edit _ConnectDatabase_: functions.php
* Edit _ConnectDatabase_: standings.php

* Edit _ConnectDatabase_: changeroster.php
* Edit _ConnectDatabase_: createroster.php
* Edit _ConnectDatabase_: creation-competition.php
* Edit _ConnectDatabase_: cron.php
* Edit _ConnectDatabase_: formations.php

* Edit _ConnectDatabase_: index.php
* Edit: Moficato maketeam.php - Bootstraped e reso compatibile con il nuovo sistema di Database
* Edit: Spostate in functions.php funzioni scritte dentro alcune pagine
* Edit _ConnectDatabase_:playersinfo.php
* Edit: style.css aggiunta voce ".value_tr td"
* Edit _ConnectDatabase_: settings-competitions.php
* Edit _ConnectDatabase_: settings-market.php
* Edit _ConnectDatabase_: settings.php
* Edit _ConnectDatabase_: signup.php
* Edit _ConnectDatabase_: teams.php

# Da Fare
 
* Modificare colori alert a seconda del tema
* Sei Politico