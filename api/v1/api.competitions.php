<?php
	
	$app->get('/competitions', function () use ($app) {


    $db_competitions = new ConnectDatabaseCompetitions(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);

    $db_rounds = new ConnectDatabaseRounds($db_competitions->mysqli);

    $competitions = $db_competitions->getCompetitions();

    $array = false;

    if($app->request()->params('array')){
        $array=true;
    }

    $result = null;

    if($competitions!=null){
        for($i=0;$i<count($competitions);$i++){
            $id = $competitions[$i]->getId();
            $compe = $competitions[$i]->map();
            $compe["real_rounds"] = $db_rounds->getRoundsByCompetition($id);
            $compe["competition_rounds"] = $db_rounds->getRoundsOfCompetition($id);

            if($array){
                $result[] = $compe;
            }else{
                $result[$id] = $compe;
            }

        }

    }


    if($competitions!=null){
        $response["error"] = false;
        $response["data"]=$result;
    }else {
        // unknown error occurred
        $response['error'] = true;
        $response['message'] = "Dump Competitions Error";
    }

    echoRespnse(200, $response);


});

$app->post('/competitions', function () use ($app) {

	$apiKey = $app->request->headers->get('Token');

    $db_competitions = new ConnectDatabaseCompetitions(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);
	$db_users = new ConnectDatabaseUsers($db_competitions->mysqli);

    $user = $db_users->getUserByApiKey($apiKey);

    $json = $app->request->getBody();

    $data = json_decode($json, true); // parse the JSON into an assoc. array	

    //verifyRequiredParams(array("id","name","first_round","num_rounds","users"),$app);
    
    $type = $data["type"];
    
	if($type == "DIRECT"){
		
		$name = $data["name"];
		$phase = $data["phase"];
		
		error_log(print_r($phase,true));
	
		$name_phase = $phase["name"];
		$users_in_competition = $data["users"];
		$name_groups = $phase["name_groups"];
		$type_phase = $phase["type_phase"];

		$rounds = $phase["rounds"];
		
		error_log($type_phase);
		error_log(print_r($users_in_competition,true));
		
		
			    
	    if($db_users->checkApi($apiKey) && $user!=null ){
	        $response["error"] = false;
			
	        $ret = $db_competitions->createCompetition($name,null,null,$type);
	        
	        $ss = array();
	        
	        if($type_phase == "ROUND_ROBIN"){
		        
		        $ss = $users_in_competition;
		        
	        }else if($type_phase == "ROUND_ROBIN_SEEDED"){
		        
		        foreach($users_in_competition as $tt){
			        $ss = array_merge($ss,$tt);
		        }
		        
	        }
	        
	        $db_competitions->setUsersInCompetition($ret,$ss);
	        
	        if($ret != null){
		        $db_competitions->addPhase($name_phase,$ret,0,$type_phase,$users_in_competition,$name_groups,$rounds);
	        }else{
		        $response["error"] = true;
		        $response["message"] = "Errore Creazione";
	        }
	
	    }else {
	        // unknown error occurred
	        $response['error'] = true;
	        $response['message'] = "Authentication Token is Wrong";
	    }
		
	}else{
		$name = $data["name"];
	    $first_round  = $data["first_round"];
	    $num_rounds = $data["num_rounds"];
	    
	    if($db_users->checkApi($apiKey) && $user!=null ){
	        $response["error"] = false;
			
	        $ret = $db_competitions->createCompetition($name,$first_round,$num_rounds,$type);
	        
	        if($ret != null){
		        $response["data"] = $ret;
	        }else{
		        $response["error"] = true;
		        $response["message"] = "Errore Creazione";
	        }
	
	    }else {
	        // unknown error occurred
	        $response['error'] = true;
	        $response['message'] = "Authentication Token is Wrong";
	    }
	}
    

    echoRespnse(200, $response);
    

});

$app->get('/competitions/:id', function ($id_competition) use ($app) {

    //verifyRequiredParams(array('round', 'competition'),$app);

    $db = new ConnectDatabaseUsers(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);
    $db_rounds = new ConnectDatabaseRounds($db->mysqli);
    $db_competitions = new ConnectDatabaseCompetitions($db->mysqli);

    //$id_competition=$app->request()->params('competition');


    $result=null;

    $competition = getCompetition($id_competition);
    
    $teams=$db_competitions->getUsersInCompetition($id_competition);

    $standings=$db_competitions->getStandings($id_competition);

    for($i=0 ; $i<count($standings) ; $i++){
        $standings[$i]["team_info"]=$db->getUserById(intval($standings[$i]["id_user"]))->mapBasic();
    }


    if($competition!=null){
        $response["error"] = false;
        $response["data"]["standings"]=$standings;
        $response["data"]["handicaps"]=getHandicapsCompetitionById($id_competition);

        $response["data"]["competition"] =$competition;
        $response["data"]["teams"]=$teams;
        
        $ids = array();
        foreach($teams as $team){
		    $ids[]  = $team["id"];
	    }
	    
	    if($competition["type"] == "DIRECT"){

		   $phases = $db_competitions->getPhases($id_competition);

		   foreach($phases as $phase){
		   		$response["data"]["phases"][] = $phase->map();

		   		foreach($phase->getGroups() as $group){
			   		foreach($group->getMatches() as $match){
				   		
			   		}
		   		}
		   }
	    }
	    
	    $response["data"]["ids"] = $ids;
        
    }else {
        // unknown error occurred
        $response['error'] = true;
        $response['message'] = "Dump ID COMP:".$id_competition." Error";
    }


    echoRespnse(200, $response);


});

$app->put('/competitions/:id', function ($id) use ($app) {

	$apiKey = $app->request->headers->get('Token');

    $db_competitions = new ConnectDatabaseCompetitions(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);
	$db_users = new ConnectDatabaseUsers($db_competitions->mysqli);

    $user = $db_users->getUserByApiKey($apiKey);

    $json = $app->request->getBody();

    $data = json_decode($json, true); // parse the JSON into an assoc. array

    //verifyRequiredParams(array("id","name","first_round","num_rounds","users"),$app);

    $id = $data["id"];
    $name = $data["name"];
    $first_round  = $data["first_round"];
    $num_rounds = $data["num_rounds"];
    $users = $data["users"];

    if($db_users->checkApi($apiKey) && $user!=null ){
        $response["error"] = false;

        $db_competitions->editCompetition($id,$name,$first_round,$num_rounds);
        $db_competitions->setUsersInCompetition($id,$users);
        
        if(isset($data["rounds"])){
	        $rounds = $data["rounds"];
	        $db_competitions->setRoundsCompetition($id,$rounds);
        }
        

    }else {
        // unknown error occurred
        $response['error'] = true;
        $response['message'] = "Authentication Token is Wrong";
    }

    echoRespnse(200, $response);


});

$app->delete('/competitions/:id', function ($id) use ($app) {

	$apiKey = $app->request->headers->get('Token');

    $db_competitions = new ConnectDatabaseCompetitions(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);
	$db_users = new ConnectDatabaseUsers($db_competitions->mysqli);

    $user = $db_users->getUserByApiKey($apiKey);

    if($db_users->checkApi($apiKey) && $user!=null ){
        $response["error"] = false;

        $db_competitions->deleteCompetition($id);

    }else {
        // unknown error occurred
        $response['error'] = true;
        $response['message'] = "Authentication Token is Wrong";
    }

    echoRespnse(200, $response);


});

$app->get('/competitions/:id/teams', function ($id_competition) use ($app) {

    //verifyRequiredParams(array('round', 'competition'),$app);

    $db = new ConnectDatabaseUsers(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);
    $db_rounds = new ConnectDatabaseRounds($db->mysqli);
    $db_competitions = new ConnectDatabaseCompetitions($db->mysqli);

    $round=$app->request()->params('round');
    //$id_competition=$app->request()->params('competition');


    $result=null;

    $teams=$db_competitions->getUsersInCompetition($id_competition);

    $orderByRole=false;
    
    $ids = array();
    $users = array();
            
    foreach($teams as $team){
	    $ids[]  = $team["id"];
	    $users[intval($team["id"])] = $db->getUserById(intval($team["id"]))->mapBasic();
    }

    /*if($app->request()->params('orderByRole')){
        $orderByRole=true;
    }*/

    /*if($teams!=null){
        for($i=0;$i<count($teams);$i++){
            if($orderByRole) {
                if($teams[$i]["team"]->getPlayers()!=null) {
                   $teams[$i]["team"] = $teams[$i]["team"]->mapOrderedByRole();
                }
            }else {
                $teams[$i]["team"] = $teams[$i]["team"]->map();
            }
        }
    }*/

    if($teams!=null){
        $response["error"] = false;
        $response["data"]["teams"]=$teams;
        $response["data"]["ids"] = $ids;
        $response["data"]["users"] = $users;
    }else {
        // unknown error occurred
        $response['error'] = true;
        $response['message'] = "Dump Teams ID COMP:".$id_competition." and ROUND:".$round." Error";
    }


    echoRespnse(200, $response);


});

$app->get('/competitions/:id/standings', function ($id_competition) use ($app) {

    //verifyRequiredParams(array('round', 'competition'),$app);
    

    $db = new ConnectDatabaseUsers(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);
    $db_rounds = new ConnectDatabaseRounds($db->mysqli);
    $db_competitions = new ConnectDatabaseCompetitions($db->mysqli);

    //$id_competition=$app->request()->params('competition');


    $result=null;

    $standings=$db_competitions->getStandings($id_competition);

    $orderByRole=false;

    /*if($app->request()->params('orderByRole')){
        $orderByRole=true;
    }*/

    /*if($teams!=null){
        for($i=0;$i<count($teams);$i++){
            if($orderByRole) {
                if($teams[$i]["team"]->getPlayers()!=null) {
                   $teams[$i]["team"] = $teams[$i]["team"]->mapOrderedByRole();
                }
            }else {
                $teams[$i]["team"] = $teams[$i]["team"]->map();
            }
        }
    }*/
    
    $standings_by_user = array();

    for($i=0 ; $i<count($standings) ; $i++){
	    
	    $temp_user = $db->getUserById(intval($standings[$i]["id_user"]));
	    
        $standings[$i]["team_info"]=$temp_user->mapBasic();
		
        $standings_by_user[$temp_user->getId()] = $i+1; 
    }

    if($standings!=null){
        $response["error"] = false;
        $response["data"]["standings"]=$standings;
        $response["data"]["standings_by_user"]=$standings_by_user;
        $response["data"]["handicaps"]=getHandicapsCompetitionById($id_competition);
        $response["data"]["competition"] = getCompetition($id_competition);
    }else {
        // unknown error occurred
        $response['error'] = true;
        $response['message'] = "Dump Classifica ID COMP:".$id_competition." Error";
    }
	
	

    echoRespnse(200, $response);


});

//COMPETITION ROUND

$app->get('/competitions/:id/standings/:round', function ($id_competition,$round) use ($app) {

    //verifyRequiredParams(array('round', 'competition'),$app);

    $db = new ConnectDatabaseUsers(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);
    $db_rounds = new ConnectDatabaseRounds($db->mysqli);
    $db_competitions = new ConnectDatabaseCompetitions($db->mysqli);

    //$id_competition=$app->request()->params('competition');


    $result=null;

    $competition=getCompetition($id_competition);
    
    if($round=="last"){
	    $round = $db_rounds->getLastCalcRound();
    }
    

    $standings=$db_rounds->getRoundStandings($id_competition,$round);

    $orderByRole=false;
    
    

    /*if($app->request()->params('orderByRole')){
        $orderByRole=true;
    }*/

    /*if($teams!=null){
        for($i=0;$i<count($teams);$i++){
            if($orderByRole) {
                if($teams[$i]["team"]->getPlayers()!=null) {
                   $teams[$i]["team"] = $teams[$i]["team"]->mapOrderedByRole();
                }
            }else {
                $teams[$i]["team"] = $teams[$i]["team"]->map();
            }
        }
    }*/



    if($standings!=null){
	    
	    $standings_by_user = array();

	    for($i=0 ; $i<count($standings) ; $i++){
		   	$temp_user = $db->getUserById(intval($standings[$i]["id_user"]));
	        $standings[$i]["team_info"]=$temp_user->mapBasic();
	        $standings_by_user[$temp_user->getId()] = $i+1; 
	    }
	    

	    
	    $teams=$db_competitions->getUsersInCompetition($id_competition);

        $response["error"] = false;
        $response["data"]["standings"]=$standings;
        $response["data"]["handicaps"]=getHandicapsRoundByRoundId($round);
        $response["data"]["competition"] =$competition;
        $response["data"]["teams"] = $teams;
        $response["data"]["standings_by_user"] = $standings_by_user;
        $response["data"]["round"] = $round;
        
        $ids = array();
        foreach($teams as $team){
		    $ids[]  = $team["id"];
	    }
	    
	    $response["data"]["ids"] = $ids;

        
    }else if($competition!=null){
        // unknown error occurred
        $response['error'] = true;
        $response['message'] = "Giornata Non Calcolata";
        $response["error_data"] = $competition;
        $response["error_data"]["round"] = $round;
    }else{
	    $response["error"] = true;
	    $response["message"] = "Competition Not Found";
    }


    echoRespnse(200, $response);


});

?>