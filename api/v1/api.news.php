<?php
	$app->get('/news', function () use ($app) {


    $db = new ConnectDatabase(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);

    $news = null;    
    $json = $app->request->getBody();

	$data = json_decode($json,true);
	
	
	if($data!=null && isset($data["toEdit"])){
		$news = $db->getNewsToEdit();
	}else{
		$news = $db->getNews();
	}
	    
    if($news!=null){
	    $arr = array();
	    foreach($news as $item){
		    $arr[] = $item->map();
	    }
	    
	    
        $response["error"] = false;
        $response["data"]=$arr;
    }else {
        // unknown error occurred
        $response['error'] = true;
        $response['message'] = "News Dump Error";
    }

    echoRespnse(200, $response);


});

$app->post('/news', function () use ($app) {

    $apiKey = $app->request->headers->get('Token');

    $db_users = new ConnectDatabaseUsers(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);
    $db = new ConnectDatabase($db_users->mysqli);

    $json = $app->request->getBody();
    $data = json_decode($json, true); // parse the JSON into an assoc. array

    if($db_users->checkApi($apiKey) && $db_users->getUserByApiKey($apiKey)->getAuth()>=1){
        $response["error"] = false;

        if($data!=null){
            $db->setNews($data["title"],$data["html"]);
            $response["error"] = false;
        }else{
            $response['error'] = true;
            $response['message'] = "News Create JSON is Null";
        }
    }else {
        // unknown error occurred
        $response['error'] = true;
        $response['message'] = "Authentication Token is Wrong";
    }

    echoRespnse(200, $response);


});

$app->get('/news/:id', function ($id) use ($app) {


    $db = new ConnectDatabase(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);

    $news = $db->getNewsById($id);

    
    if($news!=null){
        $response["error"] = false;
        $response["data"]=$news->map();
    }else {
        // unknown error occurred
        $response['error'] = true;
        $response['message'] = "News Dump Error";
    }

    echoRespnse(200, $response);


});

$app->delete('/news/:id', function ($id) use ($app) {
	
	$apiKey = $app->request->headers->get('Token');

    $db_users = new ConnectDatabaseUsers(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);
    $db = new ConnectDatabase($db_users->mysqli);


    if($db_users->checkApi($apiKey) && $db_users->getUserByApiKey($apiKey)->getAuth()>=1){
        $response["error"] = false;
        
		$db->deleteNews($id);
        $response["error"] = false;
    
    }else {
        // unknown error occurred
        $response['error'] = true;
        $response['message'] = "Authentication Token is Wrong";
    }

    echoRespnse(200, $response);


});

$app->put('/news/:id', function ($id) use ($app) {

    $apiKey = $app->request->headers->get('Token');

    $db_users = new ConnectDatabaseUsers(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);
    $db = new ConnectDatabase($db_users->mysqli);

    $json = $app->request->getBody();
    $data = json_decode($json, true); // parse the JSON into an assoc. array
    
    
    if($db_users->checkApi($apiKey) && $db_users->getUserByApiKey($apiKey)->getAuth()>=1){
        $response["error"] = false;

        if($data!=null){
            $db->editNews($id , $data["title"],$data["html"]);
            $response["error"] = false;
        }else{
            $response['error'] = true;
            $response['message'] = "News Create JSON is Null";
        }
    }else {
        // unknown error occurred
        $response['error'] = true;
        $response['message'] = "Authentication Token is Wrong";
    }

    echoRespnse(200, $response);


});


$app->post('/newsletters', function () use ($app) {

	$apiKey = $app->request->headers->get('Token');

	$db_users = new ConnectDatabaseUsers(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);
    
    $json = $app->request->getBody();

    $data = json_decode($json, true); // parse the JSON into an assoc. array

    //verifyRequiredParams(array("id","name","first_round","num_rounds","users"),$app);
    $text = $data["text"];
    $title = $data["title"];
    
    $user = $db_users->getUserByApiKey($apiKey);
    
        
    if($db_users->checkApi($apiKey) && $user!=null && $user->getAuth()>0){
	    
        $ret=$db_users->getUsers();     
		
        if($ret!=null){
	        
	        foreach($ret as $item){
		        
		        
		        $email = $item->getEmail();
		        		        
		        $headers = "From: info@associazionezenit.it \r\n";
				$headers .= "MIME-Version: 1.0\r\n";
				$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
				
				$message = $text;
		        
		        mail($email, $title, $message , $headers);
		    }
	        
	        
        }else{
	        $response['error'] = true;
			$response['message'] = "Error No Users";
        }

    }else {
        // unknown error occurred
        $response['error'] = true;
        $response['message'] = "Auth Not Valid";
    }
    

    echoRespnse(200, $response);


});
?>