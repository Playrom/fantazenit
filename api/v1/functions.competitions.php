<?php


function getCompetition($id_competition){

    $db = new ConnectDatabaseUsers("localhost","root","aicon07","fantacalcio",3306);
    $db_competitions = new ConnectDatabaseCompetitions($db->mysqli);
    $db_rounds = new ConnectDatabaseRounds($db->mysqli);


    $temp=$db_competitions->getCompetition($id_competition);

    if($temp!=null) {
        $competition=$temp->map();
        $competition["real_rounds"] = $db_rounds->getRoundsByCompetition($id_competition);
        $competition["competition_rounds"] = $db_rounds->getRoundsOfCompetition($id_competition);
        return $competition;

    }
    return null;
}
?>