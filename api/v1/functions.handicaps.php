<?php

function getHandicapsRounds(){
    //verifyRequiredParams(array('round', 'competition'),$app);

    $db = new ConnectDatabaseUsers("localhost","root","aicon07","fantacalcio",3306);
    $db_handicaps = new ConnectDatabaseHandicaps($db->mysqli);

    //$id_competition=$app->request()->params('competition');

    $handicaps=$db_handicaps->getHandicapsRounds();


    $result=null;


    $orderByRole=false;

    /*if($app->request()->params('orderByRole')){
    $orderByRole=true;
    }*/

    if($handicaps!=null){
        for($i=0;$i<count($handicaps);$i++){
            $result[] = $handicaps[$i]->map();
        }
    }



    return $result;
}

function getHandicapsRoundByRoundId($round){
    //verifyRequiredParams(array('round', 'competition'),$app);

    $db = new ConnectDatabaseUsers("localhost","root","aicon07","fantacalcio",3306);
    $db_rounds = new ConnectDatabaseRounds($db->mysqli);
    $db_competitions = new ConnectDatabaseCompetitions($db->mysqli);
    $db_handicaps = new ConnectDatabaseHandicaps($db->mysqli);

    //$id_competition=$app->request()->params('competition');

    $handicaps=$db_handicaps->getHandicapsRoundsByRoundId($round);


    $result=null;


    $orderByRole=false;

    /*if($app->request()->params('orderByRole')){
        $orderByRole=true;
    }*/

    if($handicaps!=null){
        for($i=0;$i<count($handicaps);$i++){
            $result[] = $handicaps[$i]->map();
        }
    }

    return $result;
}

function getHandicapsCompetitions(){
    //verifyRequiredParams(array('round', 'competition'),$app);

    $db = new ConnectDatabaseUsers("localhost","root","aicon07","fantacalcio",3306);
    $db_rounds = new ConnectDatabaseRounds($db->mysqli);
    $db_competitions = new ConnectDatabaseCompetitions($db->mysqli);
    $db_handicaps = new ConnectDatabaseHandicaps($db->mysqli);

    //$id_competition=$app->request()->params('competition');

    $handicaps=$db_handicaps->getHandicapsCompetitions();


    $result=null;


    $orderByRole=false;

    /*if($app->request()->params('orderByRole')){
        $orderByRole=true;
    }*/

    if($handicaps!=null){
        for($i=0;$i<count($handicaps);$i++){
            $result[] = $handicaps[$i]->map();
        }
    }

    return $result;
}

function getHandicapsCompetitionById($id_competition){
    //verifyRequiredParams(array('round', 'competition'),$app);

    $db = new ConnectDatabaseUsers("localhost","root","aicon07","fantacalcio",3306);
    $db_rounds = new ConnectDatabaseRounds($db->mysqli);
    $db_competitions = new ConnectDatabaseCompetitions($db->mysqli);
    $db_handicaps = new ConnectDatabaseHandicaps($db->mysqli);

    //$id_competition=$app->request()->params('competition');

    $handicaps=$db_handicaps->getHandicapsCompetitionsByCompetitionId($id_competition);


    $result=null;


    $orderByRole=false;

    /*if($app->request()->params('orderByRole')){
        $orderByRole=true;
    }*/

    if($handicaps!=null){
        for($i=0;$i<count($handicaps);$i++){
            $result[] = $handicaps[$i]->map();
        }
    }

    return $result;
}

?>
