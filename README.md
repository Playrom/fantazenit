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

# Alpha 0.5 - 25 Luglio 2015

* New: Api JSON Create ed Implementate in tutto il sito
* Info: Riunificazione Brench con Brench API JSON

# REST API Compatible Brench - 25 Luglio 2015

* New: Login API
* Edit: Header modificato per login API

* Edit: Comment User Class
* New: Classe di accesso a API

* Edit: Settings Base con API
* Edit: Pagina Squadre

* Edit: Create Roster 
* Known Strange : In ConnectDatabaseMarkets->createRoster mi da problemi il $this->mysqli nella creazione di un oggetto database

* Edit: Make Team
* New : Config file with Define Values

* Edit: Add to /config info generali sul sito

* Edit: settings.php
* Edit: settings-competitions.php
* Edit: creation-competition.php
* Edit: settings-handicaps.php
* Edit: settings-market.php
* Edit: gestionegiornate.php
* Edit: signup.php

# 1.0 - 8 Agosto 2015

* Feature: Profile Page
* New API: JSON API Edit User
* New: Navigation Bar and Error Section on Top of the Page

* Feature: Edit Formation By Administrator
* Edit: Start to Implement CSS Media Query in Header
* New Method: Added in ConnectDatabaseUsers "checkAuthOverride" a method that return true if the api is an api valid for an Admin User

* New: Landing Page index.php , home page moved to home.php
* Feature: lista.php , display quotazioni
* Feature: Modificatore di difesa
* Edit: Css Improvements , ex. Footer
* Feature: in profile.php , edit Name Team. Modificata JSON Api e ConnectDatabaseUsers per implementare questa funzionalità

* New: in changeroster e createroster compare nome team accanto al nome del giocatore e quotazione

* New: privacypolicy  , google analytics, cookie law blocker

* Feature: Avatar Profilo, modificato profile.php , utilizzato framework cropper il funzionamento, modificato JSON API con "/me/avatar"
* New: Pagina Regolamento
* New: Nuovo Font per gli H1, boton-regular

* Edit: CSS Responsive

* New: Accettazione Privacy Policy alla registrazione

* Feature: Gestione dei giocatori non piu in serie A

# 1.1 - 20 Agosto 2015

* New: Search player with name team
* New: Filter team in changeroster e createroster
* Bugfix: Player removed from roster was displayed in free table also when it was not selected his role
* Bugfix: Module display in Make Team with "-"
* Bugfix: Modificatore Difesa
* New: Dopo 5 Giornate non si recupera piu giornata precedente
* New: Ora ogni volta che si cancella una giornata , se la formazione è stata recuperata essa sarà cancellata, per rendere più pulito e coerente il database
* New: Aggiunte colonne "recovered" e "modificatore" in tabella "tactics"
* Bugfix: Alert Success ora ha corretta classe css
* New: FAQ
* Edit: Nuovo menu riepilogo
* Bugfix: editformations.php
* New: minimum points per round
* New: Immagini Bonus e Malus
* Feature: Notizie
* New: Chiusura automatica mercato iniziale tramite api JSON
* New: Controllo ultima giornata tramite max round_result
* New: Modifica Rose Amministratore
* New: Bonus e Penalizzazione Fanta Milioni
* New: Box Informazioni sotto error-box con vincitore ultima giornata, e ultime notizie
* Feature: Newsletter
* New: Main Navbar Edited
* Bugfix: Content Bottom Padding

# 1.2 - 

* Edit: Nuovi Colori Ruolo
* Edit: Squadre con loghi in modifica formazione
* Bugfix : Home Page Standings and points last round
* Edit: Home Page moved back to index.php
* New : Pages are now News, editable
* New : Mercato da parte dell'admin

# Da Fare
* Handicap Round Compaiono in Classifiche anche se giocatori non inclusi