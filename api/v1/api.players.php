<?php
	
	$app->get('/players', function () use ($app) {


    $db = new ConnectDatabasePlayers(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);

    $players = $db->dumpSingoliToList(null,null);

    $result=null;

    if($players!=null){
	    $arr = array();
	    foreach($players as $player){
		    $arr[] = $player->mapWithoutVotes();
	    }

        $result=$arr;
    }

    if($result!=null){
        $response["error"] = false;
        $response["data"]=$result;
    }else {
        // unknown error occurred
        $response['error'] = true;
        $response['message'] = "Dump Singoli Players Error";
    }

    echoRespnse(200, $response);


});

$app->get('/players/:id', function ($id) use ($app) {


    $db = new ConnectDatabasePlayers(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);

    $player = $db->dumpPlayerById($id);
    $stats = $db->dumpStats($id);

    $player->setStat($stats);

    $result=null;

    if($player!=null){
        $temp=$player->map();
        $result=$temp;
    }

    $json = $result;



    if($json!=null){
        $json["url_logo_team"]="http://www.associazionezenit.it/fantazenit/teamlogo/".$json["team"].".png";
        $json["role_name"]=role($json["role"]);
        $json["media"] = media($json["stat"],2);
        $json["presenze"] = presenze($json["stat"]);

        $response["error"] = false;
        $response["data"]=$json;
    }else {
        // unknown error occurred
        $response['error'] = true;
        $response['message'] = "Dump Player ID:".$id." Error";
    }

    echoRespnse(200, $response);


});

$app->get('/players/:id/values', function ($id) use ($app) {


    $db = new ConnectDatabasePlayers(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);

    $json = $db->getValuesOfPlayer($id);

    $result=null;

    if($json!=null){
        $response["error"] = false;
        $response["data"]=$json;
    }else {
        // unknown error occurred
        $response['error'] = true;
        $response['message'] = "Dump Values Player ID:".$id." Error";
    }

    echoRespnse(200, $response);


});

$app->get('/seriea/teams', function () use ($app) {

    $db = new ConnectDatabasePlayers(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);

    $result = $db->getSerieaTeams();


    if($result!=null){
        $response["error"] = false;
        $response["data"]=$result;
    }else {
        // unknown error occurred
        $response['error'] = true;
        $response['message'] = "Dump Serie A Teams Error";
    }

    echoRespnse(200, $response);


});

?>