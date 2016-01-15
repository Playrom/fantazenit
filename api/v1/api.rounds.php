<?php
	
	$app->get('/rounds', function () use ($app) {

	// Round is Real Round

    $db = new ConnectDatabaseUsers(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);
    $db_rounds = new ConnectDatabaseRounds($db->mysqli);
    $db_competitions = new ConnectDatabaseCompetitions($db->mysqli);

    //$id_competition=$app->request()->params('competition');
    
    $result=$db_rounds->getRounds();
    

    if($result!=null){
        $response["error"] = false;
        $response["data"] = $result;
    }else{
	    $response["error"] = true;
	    $response["message"] = "No Rounds Created";
    }


    echoRespnse(200, $response);


});

$app->post('/rounds', function () use ($app) {

	$apiKey = $app->request->headers->get('Token');

	$db_users = new ConnectDatabaseUsers(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);
	$db_rounds = new ConnectDatabaseRounds($db_users->mysqli);

    $user = $db_users->getUserByApiKey($apiKey);

    $json = $app->request->getBody();

    $data = json_decode($json, true); // parse the JSON into an assoc. array

    //verifyRequiredParams(array("id","name","first_round","num_rounds","users"),$app);

    $round= $data["round"];
    $type = $data["type"];
    
    
        
    
    if($db_users->checkApi($apiKey) && $user!=null ){
        $response["error"] = false;
        
        
        if($type == "ADD"){
	        	        
            $response["error"] = !$db_rounds->addRound($round);
                        
        }else if($type == "SET_CURRENT"){
	        
	        if(isset($data["additional"])){
	        	
	        	$date_finish = $data["additional"];
	        	$response["error"] = !$db_rounds->setCurrentRound($round,$date_finish);
	        }    
	            
        }else if($type == "OPEN"){
	        
            $response["error"] = !$db_rounds->openRound($round);
            
            $filename = "cache/users/**/teams/*".$round."*";

	        foreach (glob($filename) as $file) {
			    error_log("$file size " . filesize($file));
			    unlink($file);
			}

	        	        
        }else if($type == "CLOSE"){
	        	        
            $response["error"] = !$db_rounds->closeRound($round);
            $filename = "cache/users/**/teams/*".$round."*";

	        foreach (glob($filename) as $file) {
			    error_log("$file size " . filesize($file));
			    unlink($file);
			}
            
	        	        
        }else{
	        
	        $response["error"] = true;
	        $response["message"] = "Config Rounds Type Not Found";
	        
        }

    }else {
        // unknown error occurred
        $response['error'] = true;
        $response['message'] = "Authentication Token is Wrong";
    }

    echoRespnse(200, $response);


});



$app->get('/rounds/recap', function () use ($app) {

	// Round is Real Round

    $db = new ConnectDatabaseUsers(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);
    $db_rounds = new ConnectDatabaseRounds($db->mysqli);
    $db_competitions = new ConnectDatabaseCompetitions($db->mysqli);

    //$id_competition=$app->request()->params('competition');
    
	$id = $db_rounds->getLastCalcRound();


    $result=null;

    if($db_rounds->roundExist($id)){
        $response["error"] = false;
        $open=$db_rounds->isOpenRound($id);
        $response["data"]["open"] = $open;
        $response["data"]["id"] = $id;
        $response["data"]["formations_editing"]=$db_rounds->isPossibleToEditFormation($id);
        if(!$open){
	        $response["data"]["recap"] = $db_rounds->getRecap($id);
	    }
    }else{
	    $response["error"] = true;
	    $response["message"] = "Round Not Found";
    }


    echoRespnse(200, $response);


});

$app->get('/rounds/:id', function ($id) use ($app) {

	// Round is Real Round

    $db = new ConnectDatabaseUsers(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);
    $db_rounds = new ConnectDatabaseRounds($db->mysqli);
    $db_competitions = new ConnectDatabaseCompetitions($db->mysqli);

    //$id_competition=$app->request()->params('competition');
    
    if($id=="last"){
	    $id = $db_rounds->getLastCalcRound();
    }
    
    $id_user=null;

    if($app->request()->params('user')!=null){
        $id_user = intval($app->request()->params('user'));
    }


    $result=null;
	if($db_rounds->roundExist($id) && $id_user!=null){
        $response["error"] = false;
        $open=$db_rounds->isOpenRound($id);
        
        if(!$open){
	        $res = $db_rounds->getInfoRoundUser($id,$id_user);
	        if($res!=null){
		        $response["data"]["results"] = $res;
	        }else{
		        $response["error"] = true;
		        $response["message"] = "Results Not Found";
	        }
	    }else{
		    $response["error"] = true;
		    $response["message"] = "Round Not Calc";
	    }
    }else if($db_rounds->roundExist($id)){
        $response["error"] = false;
        $open=$db_rounds->isOpenRound($id);
        $response["data"]["open"] = $open;
        $response["data"]["id"] = $id;
        $response["data"]["formations_editing"]=$db_rounds->isPossibleToEditFormation($id);
        if(!$open){
	        $response["data"]["results"] = $db_rounds->getInfoRound($id);
	        $response["data"]["recap"] = $db_rounds->getRecap($id);
	    }
    }else{
	    $response["error"] = true;
	    $response["message"] = "Round Not Found";
    }


    echoRespnse(200, $response);


});

?>