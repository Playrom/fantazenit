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

# Alpha 0.4 -  9 Giugno 2015

* Feature: Bonus e Malus per le giornate, e per le classifiche generali
* New: Nuove Classi Base : Handicap.php e figlie , servono a gestire i bonus e malus
* New: Nuova Classe di Connessione al Database : ConnectDatabaseHandicaps
* New: Nuova pagina standings-handicaps.php per impostare i bonus e malus
* Edit: Modificate Classi di Connessione al Database per utilizzare le nuove classi create

* Edit: Le Classifiche in standings.php ora tengono conto dei bonus e malus sia per giornate sia di classifica generale
* Edit: Le Formazioni calcolate tengono ora conto di bonus e malus di giornata

* New: Modificate alcune classi per poter supportare il login tramite API che arriverà in una nuova brench

# REST API Compatible Brench - 

* New: Login API
* Edit: Header modificato per login API

* Edit: Comment User Class
* New: Classe di accesso a API

* Edit: Settings Base con API
* Edit: Pagina Squadre


# Da Fare

## DATA MERCATO E MERCATO TO JSON IN PAGINA SQUADRE
 
* pagina aggiunta crediti