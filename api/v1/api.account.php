<?php
	
	$app->post('/me', function () use ($app) {

	$apiKey = $app->request->headers->get('Token');

	$db_users = new ConnectDatabaseUsers(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);
    
    $json = $app->request->getBody();

    $data = json_decode($json, true); // parse the JSON into an assoc. array

    //verifyRequiredParams(array("id","name","first_round","num_rounds","users"),$app);
    
    $response = null;
    
    if(isset($data["current_pass"])){
	    $user = $db_users->getUserByApiKey($apiKey);
	    
	    $current_pass = $data["current_pass"];
	    

	    
	    
	    if($user->getPassword() == $current_pass){
		    
		    $pass = null;
		    $email = null;
		    $url_fb = null;
		    $name_team = null;
		    		    
		    if(isset($data["email"]) && $data["email"]!=""){
			    $email = $data["email"];
		    }
		    
		    if(isset($data["name_team"]) && $data["name_team"]!=""){
			    $name_team = $data["name_team"];
		    }
		    
		    if(isset($data["url_fb"]) && $data["url_fb"] != ""){
			    $url_fb = $data["url_fb"];
		    }
		    
		    if(isset($data["new_pass"]) && $data["new_pass"] != ""){
			    $pass = $data["new_pass"];
		    }
		    
		    
		    $response["error"] = !$db_users->editUser($user->getId(),$pass,$email,$url_fb,$name_team,null);
		    
		    
	    }
    
    }else {
        // unknown error occurred
        $response['error'] = true;
        $response['message'] = "Old Password Not Correct";
    }
    

    echoRespnse(200, $response);


});


$app->post('/me/avatar' , function () use ($app) {

	$apiKey = $app->request->headers->get('Token');

	$db = new ConnectDatabaseUsers(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);
    
    $json = $app->request->getBody();

    $data = json_decode($json, true); // parse the JSON into an assoc. array

    //verifyRequiredParams(array("id","name","first_round","num_rounds","users"),$app);
    
    $response = null;
    
    if(isset($data["avatar"])){
	    $user = $db->getUserByApiKey($apiKey);
	    
	    $url = $data["avatar"];
	    
	    if($db->checkApi($apiKey) && $user!=null){
		    $result = $db->editUser($user->getId(),null,null,null,null,$url);
		    
		    $response["error"] = !$result;
	    }
    
    }else {
        // unknown error occurred
        $response['error'] = true;
        $response['message'] = "Old Password Not Correct";
    }
    

    echoRespnse(200, $response);


});




$app->get('/me', function () use ($app) {

    $apiKey = $app->request->headers->get('Token');

    $db = new ConnectDatabaseUsers(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);

    if($db->checkApi($apiKey)){
        $response["error"] = false;

        $user=$db->getUserById($db->getUserByApiKey($apiKey))->map();

        $response["data"]=$user->map();
    }else {
        // unknown error occurred
        $response['error'] = true;
        $response['message'] = "Authentication Token is Wrong";
    }

    echoRespnse(200, $response);


});

$app->get('/me/basic', function () use ($app) {

    $apiKey = $app->request->headers->get('Token');

    $db = new ConnectDatabaseUsers(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);

    if($db->checkApi($apiKey)){
        $response["error"] = false;
        $response["data"]=$db->getUserByApiKey($apiKey)->mapBasic();
    }else {
        // unknown error occurred
        $response['error'] = true;
        $response['message'] = "Authentication Token is Wrong";
    }

    echoRespnse(200, $response);


});

?>