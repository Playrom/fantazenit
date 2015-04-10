<?php
$title="Prova Handicap";
include('header.php');

$database_handicap = new ConnectDatabaseHandicaps($database->mysqli);

var_dump($database_handicap->getHandicapsStandings());
var_dump($database_handicap->getHandicapsStandingsByUserId(1));

$database_handicap->setHandicapStanding(1,43,"provissima",-10);

?>