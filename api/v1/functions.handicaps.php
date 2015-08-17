<?php
	

function getHandicapsRounds(){
    //verifyRequiredParams(array('round', 'competition'),$app);

    $db = new ConnectDatabaseUsers(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);
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

    $db = new ConnectDatabaseUsers(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);
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

    $db = new ConnectDatabaseUsers(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);
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

    $db = new ConnectDatabaseUsers(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);
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

function getBonuses(){
    //verifyRequiredParams(array('round', 'competition'),$app);

    $db = new ConnectDatabaseUsers(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);
    $db_handicaps = new ConnectDatabaseHandicaps($db->mysqli);

    //$id_competition=$app->request()->params('competition');

    $bonuses=$db_handicaps->getMoneyBonuses();


    $result=null;



    /*if($app->request()->params('orderByRole')){
        $orderByRole=true;
    }*/

    if($bonuses!=null){
        for($i=0;$i<count($bonuses);$i++){
            $result[] = $bonuses[$i]->map();
        }
    }

    return $result;
}

function getBonusByUserId($id){
    //verifyRequiredParams(array('round', 'competition'),$app);

    $db = new ConnectDatabaseUsers(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);
    $db_handicaps = new ConnectDatabaseHandicaps($db->mysqli);

    //$id_competition=$app->request()->params('competition');
    
    $user = null;
    
    $user = $db->getUserById($id);
    
    if($user!=null){

    	$bonuses=$db_handicaps->getMoneyBonusesByUser($user);
    	
    }

    $result=null;



    /*if($app->request()->params('orderByRole')){
        $orderByRole=true;
    }*/

    if($bonuses!=null){
        for($i=0;$i<count($bonuses);$i++){
            $result[] = $bonuses[$i]->map();
        }
    }

    return $result;
}


?>
