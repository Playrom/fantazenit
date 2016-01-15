<?php
	
	$app->get('/markets', function () use ($app) {


    $db = new ConnectDatabaseMarkets(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);

    $markets = $db->getMarkets();

    $result=null;

    if($markets!=null){
	    $arr = array();
        foreach($markets as $market){
	        $arr[] = $market->map();
        }
        $result = $arr;
    }


    if($result!=null){
        $response["error"] = false;
        $response["data"]=$result;
    }else {
        // unknown error occurred
        $response['error'] = true;
        $response['message'] = "Dump Markets Error";
    }

    echoRespnse(200, $response);


});

$app->post('/markets', function () use ($app) {

	$apiKey = $app->request->headers->get('Token');

    $db_markets = new ConnectDatabaseMarkets(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);
	$db_users = new ConnectDatabaseUsers($db_markets->mysqli);

    $user = $db_users->getUserByApiKey($apiKey);
    
    $json = $app->request->getBody();

    $data = json_decode($json, true); // parse the JSON into an assoc. array

    //verifyRequiredParams(array("id","name","first_round","num_rounds","users"),$app);

    $name = $data["name"];
    $start_date  = $data["start_date"];
    $finish_date = $data["finish_date"];
    $max_change = $data["max_change"];

    if($db_users->checkApi($apiKey) && $user!=null ){
        $response["error"] = !$db_markets->createMarket($name,$max_change,$start_date,$finish_date);

    }else {
        // unknown error occurred
        $response['error'] = true;
        $response['message'] = "Authentication Token is Wrong";
    }

    echoRespnse(200, $response);


});



$app->get('/markets/open', function () use ($app) {


    $db = new ConnectDatabaseMarkets(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);

    $markets = $db->getOpenMarkets();

    $result=null;

    if($markets!=null){
	    $arr = array();
        foreach($markets as $market){
	        $arr[] = $market->map();
        }
        $result = $arr;
    }

    if($result!=null){
        $response["error"] = false;
        $response["data"]=$result;
    }else {
        // unknown error occurred
        $response['error'] = true;
        $response['message'] = "Dump Markets Open Error";
    }

    echoRespnse(200, $response);


});



$app->get('/markets/:id', function ($id) use ($app) {


    $db = new ConnectDatabaseMarkets(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);

    $market = $db->getMarketById($id);

    $result=null;

    if($market!=null){
        $temp=$market->map();
        $result=$temp;
    }


    if($result!=null){
        $response["error"] = false;
        $response["data"]=$result;
    }else {
        // unknown error occurred
        $response['error'] = true;
        $response['message'] = "Dump Market ID:".$id." Error";
    }

    echoRespnse(200, $response);


});

$app->delete('/markets/:id', function ($id) use ($app) {

	$apiKey = $app->request->headers->get('Token');

    $db_markets = new ConnectDatabaseMarkets(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);
	$db_users = new ConnectDatabaseUsers($db_markets->mysqli);

    $user = $db_users->getUserByApiKey($apiKey);

    if($db_users->checkApi($apiKey) && $user!=null ){
        $response["error"] = !$db_markets->deleteMarket($id);

    }else {
        // unknown error occurred
        $response['error'] = true;
        $response['message'] = "Authentication Token is Wrong";
    }

    echoRespnse(200, $response);


});

$app->put('/markets/:id', function ($id) use ($app) {

	$apiKey = $app->request->headers->get('Token');

    $db_markets = new ConnectDatabaseMarkets(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);
	$db_users = new ConnectDatabaseUsers($db_markets->mysqli);

    $user = $db_users->getUserByApiKey($apiKey);
    
    $json = $app->request->getBody();

    $data = json_decode($json, true); // parse the JSON into an assoc. array

    //verifyRequiredParams(array("id","name","first_round","num_rounds","users"),$app);

    $name = $data["name"];
    $start_date  = $data["start_date"];
    $finish_date = $data["finish_date"];
    $max_change = $data["max_change"];
    

    if($db_users->checkApi($apiKey) && $user!=null ){
        $response["error"] = !$db_markets->editMarket($id,$name,$max_change,$start_date,$finish_date);

    }else {
        // unknown error occurred
        $response['error'] = true;
        $response['message'] = "Authentication Token is Wrong";
    }

    echoRespnse(200, $response);


});

$app->get('/markets/:id/transfers/:user', function ($id_market,$id_user) use ($app) {


    $db = new ConnectDatabaseMarkets(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);

    $transfers = $db->getTransfers($id_user);    
    
		
    $result=array();
        
    if(count($transfers) == 0){
	    $result = array();
	    
	    $response["error"] = false;
        $response["data"]=$result;
	    
	    echoRespnse(200, $response);
    }else if($transfers!=null){
	    $arr = array();
	    foreach($transfers as $transfer){
		    if($transfer->getIdMarket() == $id_market){
		    	$arr[] = $transfer->map();
		    }
	    }
        $result=$arr;
        
        $response["error"] = false;
        $response["data"]=$result;
	    
	    echoRespnse(200, $response);
    }else{
	    
        // unknown error occurred
        $response['error'] = true;
        $response['message'] = "Dump Market ID:".$id_market."  Transfers $id_user Error";
    }

    


});





/* Transfers

	id_user
	id_new
	id_old
	id_market

*/


$app->post('/markets/transfers', function () use ($app) {

    $apiKey = $app->request->headers->get('Token');

    $db = new ConnectDatabaseUsers(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);
    $db_players = new ConnectDatabasePlayers($db->mysqli);
    $db_markets = new ConnectDatabaseMarkets($db->mysqli);


	$json = $app->request->getBody();

	$data = json_decode($json);

	verifyRequiredParams(array("id_user","id_new","id_old","id_market"),$app);


    $data = json_decode($json, true); // parse the JSON into an assoc. array
    
    error_log(print_r($data,true));

    $id_user = $data["id_user"];
    $id_new  = $data["id_new"];
    $id_old  = $data["id_old"];
    $id_market = $data["id_market"];


    $user = $db->getUserByApiKey($apiKey);

    $error_code=null;
    

    if($db->checkApi($apiKey) && $user!=null && ( $id_user==$user->getId() || $db->checkAuthOverride($apiKey)) ){
        $response["error"] = false;

        $user = $db->getUserById($id_user);
        
        $roster = $user->getPlayers();


        $old_player = $roster->searchPlayer($id_old);
        $new_player = $db_players->dumpPlayerById($id_new);


        if($old_player!=null && ($user->getBalance() + $old_player->getValue() - $new_player->getValue())>=0){
	        $error_code=$db_markets->changePlayer($old_player,$new_player,$user,$id_market);
	    }else{
		    $response["error"] = true;
		    $response["message"] = "Errore Old Player or Balance";
	    }

	    /*if($error_code!=null){
		    $response["error"] = true;
		    $response["message"] = "Error Code";
		    $response["error_code"] = $error_code;
	    }else{
		    $response["error"] = false;
	    }*/

    }else {
        // unknown error occurred
        $response['error'] = true;
        $response['message'] = "Authentication Token is Wrong";
    }

    echoRespnse(200, $response);


});

?>