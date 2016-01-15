<?php
	
	
$app->post('/handicaps', function () use ($app) {

	$apiKey = $app->request->headers->get('Token');

	$db_users = new ConnectDatabaseUsers(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);
	$db_handicaps = new ConnectDatabaseHandicaps($db_users->mysqli);

    $user = $db_users->getUserByApiKey($apiKey);

    $json = $app->request->getBody();

    $data = json_decode($json, true); // parse the JSON into an assoc. array

    //verifyRequiredParams(array("id","name","first_round","num_rounds","users"),$app);

    $id_team = $data["id_team"];
    $description = $data["description"];
    $points = $data["points"];
    $type = $data["type"];
        
    
    if($db_users->checkApi($apiKey) && $user!=null ){
        $response["error"] = false;
        
        if($type == "COMPETITION"){
	        
	        
	        $id_competition = $data["id_type"];
            $response["error"] = !$db_handicaps->setHandicapCompetition($id_team,$id_competition,$description,$points);
                        
        }else if($type == "ROUND"){
	        
	        $id_round = $data["id_type"];
	        $response["error"] = !$db_handicaps->setHandicapRound($id_team,$id_round,$description,$points);
	        	        
        }else if($type == "BONUS"){
	        
	        $response["error"] = !$db_handicaps->setMoneyBonus($id_team,$description,$points);
	        	        
        }else{
	        
	        $response["error"] = true;
	        $response["message"] = "Handicap Type Not Found";
	        
        }

    }else {
        // unknown error occurred
        $response['error'] = true;
        $response['message'] = "Authentication Token is Wrong";
    }

    echoRespnse(200, $response);


});


$app->get('/handicaps/bonuses', function () use ($app) {

    $result = getBonuses();



    if($result!=null){
        $response["error"] = false;
        $response["data"]=$result;
    }else {
        // unknown error occurred
        $response['error'] = true;
        $response['message'] = "Dump Bonuses Null";
    }


    echoRespnse(200, $response);


});

$app->get('/handicaps/bonuses/:id', function ($id) use ($app) {

    $result = getBonusByUserId($id);



    if($result!=null){
        $response["error"] = false;
        $response["data"]=$result;
    }else {
        // unknown error occurred
        $response['error'] = true;
        $response['message'] = "Dump Bonus ID:$id Null";
    }


    echoRespnse(200, $response);


});

$app->delete('/handicaps/bonuses/:id', function ($id) use ($app) {

	$apiKey = $app->request->headers->get('Token');
	
	$id = intval($id);

    $db_handicaps = new ConnectDatabaseHandicaps(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);
	$db_users = new ConnectDatabaseUsers($db_handicaps->mysqli);

    $user = $db_users->getUserByApiKey($apiKey);

    if($db_users->checkApi($apiKey) && $user!=null ){
        $response["error"] = false;

        $db_handicaps->deleteMoneyBonus($id);

    }else {
        // unknown error occurred
        $response['error'] = true;
        $response['message'] = "Authentication Token is Wrong";
    }

    echoRespnse(200, $response);


});




$app->get('/handicaps/competitions', function () use ($app) {

    $result = getHandicapsCompetitions();



    if($result!=null){
        $response["error"] = false;
        $response["data"]=$result;
    }else {
        // unknown error occurred
        $response['error'] = true;
        $response['message'] = "Dump Handicaps Competitions Null";
    }


    echoRespnse(200, $response);


});

$app->get('/handicaps/competitions/:id', function ($id_competition) use ($app) {

    $result = getHandicapsCompetitionById($id_competition);



    if($result!=null){
        $response["error"] = false;
        $response["data"]=$result;
    }else {
        // unknown error occurred
        $response['error'] = true;
        $response['message'] = "Dump Handicaps Competitions ID:$id_competition Null";
    }


    echoRespnse(200, $response);


});

$app->delete('/handicaps/competitions/:id', function ($id) use ($app) {

	$apiKey = $app->request->headers->get('Token');

    $db_handicaps = new ConnectDatabaseHandicaps(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);
	$db_users = new ConnectDatabaseUsers($db_handicaps->mysqli);

    $user = $db_users->getUserByApiKey($apiKey);

    if($db_users->checkApi($apiKey) && $user!=null ){
        $response["error"] = false;

        $db_handicaps->deleteHandicapCompetition($id);

    }else {
        // unknown error occurred
        $response['error'] = true;
        $response['message'] = "Authentication Token is Wrong";
    }

    echoRespnse(200, $response);


});

$app->get('/handicaps/rounds', function () use ($app) {


    $result = getHandicapsRounds();
    $response=null;

    if($result!=null){
        $response["error"] = false;
        $response["data"]=$result;
    }else {
        // unknown error occurred
        $response['error'] = true;
        $response['message'] = "Dump Handicaps Rounds Null";
    }

    echoRespnse(200, $response);


});


$app->get('/handicaps/rounds/:round', function ($round) use ($app) {

    $result = getHandicapsRoundByRoundId($round);



    if($result!=null){
        $response["error"] = false;
        $response["data"]=$result;
    }else {
        // unknown error occurred
        $response['error'] = true;
        $response['message'] = "Dump Handicaps Round : $round Null";
    }


    echoRespnse(200, $response);


});

$app->delete('/handicaps/rounds/:id', function ($id) use ($app) {

	$apiKey = $app->request->headers->get('Token');

    $db_handicaps = new ConnectDatabaseHandicaps(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);
	$db_users = new ConnectDatabaseUsers($db_handicaps->mysqli);

    $user = $db_users->getUserByApiKey($apiKey);

    if($db_users->checkApi($apiKey) && $user!=null ){
        $response["error"] = false;

        $db_handicaps->deleteHandicapRound($id);

    }else {
        // unknown error occurred
        $response['error'] = true;
        $response['message'] = "Authentication Token is Wrong";
    }

    echoRespnse(200, $response);


});

?>