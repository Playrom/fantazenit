<?php
$title="Prova Handicap";
include('header.php');

$database_handicap = new ConnectDatabaseHandicaps($database->mysqli);

var_dump($database_handicap->getHandicapsCompetitions());
var_dump($database_handicap->getHandicapsCompetitionsByUserId(1));

$database_handicap->setHandicapCompetition(1,43,"provissima",-10);

?>