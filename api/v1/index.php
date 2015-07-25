<?php
require '../../vendor/autoload.php';
require '../../class/ConnectDatabase.php';
require '../../class/ConnectDatabaseUsers.php';
require '../../class/ConnectDatabasePlayers.php';
require '../../class/ConnectDatabaseMarkets.php';
require '../../class/ConnectDatabaseRounds.php';
require '../../class/ConnectDatabaseCompetitions.php';
require '../../class/ConnectDatabaseHandicaps.php';

require '../../class/RosterList.php';
require '../../class/RosterPlayer.php';
require '../../class/Player.php';
require '../../class/StatisticsCollection.php';
require '../../class/Statistic.php';
require '../../class/User.php';
require '../../class/Handicap.php';
require '../../class/HandicapRound.php';
require '../../class/HandicapStanding.php';



require '../../class/Team.php';

require '../../class/TeamPlayerList.php';
require '../../class/TeamPlayerRound.php';
require '../../class/PlayersList.php';
require '../../class/Market.php';
require '../../class/Transfer.php';
require '../../class/Competition.php';

require '../../functions.php';

require 'functions.handicaps.php';
require 'functions.competitions.php';

require_once('../../config.php');


$app = new \Slim\Slim(array(
    'debug' => true
));


$app->get('/hello/:name', function ($name) {
    $response["message"]="Hello, $name";
    echoRespnse(200, $response);
});


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
	    
	    error_log("current = ".$user->getPassword());
	    error_log("current written = ".$current_pass);
	    
	    
	    if($user->getPassword() == $current_pass){
		    
		    $pass = null;
		    $email = null;
		    $url_fb = null;
		    
			error_log($current_pass);
		    
		    if(isset($data["email"]) && $data["email"]!=""){
			    $email = $data["email"];
		    }
		    
		    if(isset($data["url_fb"]) && $data["url_fb"] != ""){
			    $url_fb = $data["url_fb"];
		    }
		    
		    if(isset($data["new_pass"]) && $data["new_pass"] != ""){
			    $pass = $data["new_pass"];
		    }
		    
			error_log($current_pass);

		    
		    $response["error"] = !$db_users->editUser($user->getId(),$pass,$email,$url_fb);
		    
		    
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



$app->post('/users', function () use ($app) {

	$apiKey = $app->request->headers->get('Token');

	$db_users = new ConnectDatabaseUsers(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);
    
    $json = $app->request->getBody();

    $data = json_decode($json, true); // parse the JSON into an assoc. array

    //verifyRequiredParams(array("id","name","first_round","num_rounds","users"),$app);
    
    
    $username = $data["username"];
    $name = $data["name"];
    $surname = $data["surname"];
    $password = $data["password"];
    $email = $data["email"];
    $balance = $data["balance"];
    $name_team = $data["name_team"];
    $telephone = $data["telephone"];
    $url_fb = $data["url_fb"];
    
    /*$username = "u";
    $name = "u";
    $surname = "u";
    $password = "u";
    $email = "u";
    $balance = "u";
    $name_team = "u";
    $telephone = "u";
    $url_fb = NULL;*/
    
    
    $response = null ;

    
    if($db_users->getUserByEmail($email) == null || $db_users->getUserByUsername($username) == null ){

        $ret=$db_users->signupUser(new User(-1,$username,$name,$surname,$password,$email,NULL,0,$balance,NULL,NULL,$name_team,$telephone,$url_fb,NULL));        
		
        if($ret){
	        $response["error"] = false;
        }else{
	        $response['error'] = true;
			$response['message'] = "Error Creating";
        }

    }else {
        // unknown error occurred
        $response['error'] = true;
        $response['message'] = "Username or Password Already Used";
    }
    

    echoRespnse(200, $response);


});


$app->get('/users', function () use ($app) {


    $db = new ConnectDatabaseUsers(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);

    $db_markets = new ConnectDatabaseMarkets($db->mysqli);

    $users = $db->getUsers();

    $result=array();

    foreach($users as $user){
        //$transfers=$db_markets->getTransfers($user);
        //$user->setTransfers($transfers);
        $temp=$user->mapBasic();
        $result[]=$temp;

    }

    if($result!=null){
        $response["error"] = false;
        $response["data"]=$result;
    }else {
        // unknown error occurred
        $response['error'] = true;
        $response['message'] = "Dump Teams Error";
    }

    echoRespnse(200, $response);


});

$app->get('/users/:id', function ($id) use ($app) {


    $db = new ConnectDatabaseUsers(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);
    $db_markets = new ConnectDatabaseMarkets($db->mysqli);

    $user = null;

    $orderByRole=false;

    if($app->request()->params('orderByRole')){
        $orderByRole=true;
    }

    if(intval($id)!=0){

    	$user = $db->getUserById($id);

	}else{
		$user = $db->getUserByUsername($id);
	}

    $result=null;

    if($user!=null){
        $transfers=$db_markets->getTransfers($user->getId());
        $user->setTransfers($transfers);
        $temp = null;

        if($orderByRole){
	        $temp=$user->mapTeamOrderedByRole();
        }else{
        	$temp=$user->mapTeam();
        }

        $result=$temp;
    }

    $json=$result;

    if($json!=null){
        $response["error"] = false;
        $response["data"]=$json;
    }else {
        // unknown error occurred
        $response['error'] = true;
        $response['message'] = "Dump Team ID:".$id." Error";
    }

    echoRespnse(200, $response);


});


$app->post('/users/roster', function () use ($app) {


    $apiKey = $app->request->headers->get('Token');

    $db = new ConnectDatabaseUsers(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);
    $db_players = new ConnectDatabasePlayers($db->mysqli);
    $db_markets = new ConnectDatabaseMarkets($db->mysqli);



	$json = $app->request->getBody();

	$data = json_decode($json,true);

	//var_dump($data);

	//verifyRequiredParams(array("ids","id_user"),$app);

    $id_user = $data["id_user"];
    $ids = $data["ids"];

    $user = $db->getUserByApiKey($apiKey);

    $error_code=null;

    if($db->checkApi($apiKey) && $user!=null && $id_user==$user->getId()){
        $response["error"] = false;

        $result = $db_markets->createRoster($id_user,$ids);

		//$response["error"] = !$result;


    }else {
        // unknown error occurred
        $response['error'] = true;
        $response['message'] = "Authentication Token is Wrong";
    }

    echoRespnse(200, $response);


});

$app->post('/teams', function () use ($app) {

    $apiKey = $app->request->headers->get('Token');

    $db = new ConnectDatabaseUsers(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);
    $db_players = new ConnectDatabasePlayers($db->mysqli);
    $db_markets = new ConnectDatabaseMarkets($db->mysqli);
    $db_rounds  = new ConnectDatabaseRounds($db->mysqli);


	$json = $app->request->getBody();

	$data = json_decode($json,true);


	//verifyRequiredParams(array("id_user","ids","reserves","round","tactic"),$app);

    $id_user = $data["id_user"];
    $ids  = $data["ids"];
    $reserves  = $data["reserves"];
    $round = $data["round"];
    $tactic = $data["tactic"];

    $user = $db->getUserByApiKey($apiKey);

	if($user==null){
		$response['error'] = true;
        $response['message'] = "Authentication Token is not a Valid Token";
	}else{

	    $error_code=null;

	    if($db->checkApi($apiKey) && $user!=null && $id_user==$user->getId()){
	        $response["error"] = false;
	        error_log("enter");

	        $ret=$db_rounds->insertTeam($id_user,$ids,$reserves,$round,$tactic);

	    }else {
	        // unknown error occurred
	        $response['error'] = true;
	        $response['message'] = "Authentication Token for this user is Wrong";
	    }

	}

    echoRespnse(200, $response);


});




$app->get('/team/:id_team/:round', function ($id,$round) use ($app) {

    $db = new ConnectDatabaseUsers(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);
    $db_rounds = new ConnectDatabaseRounds($db->mysqli);

    $orderByRole=false;
    $orderById = false;

    if($app->request()->params('orderByRole')){
        $orderByRole=true;
    }else if($app->request()->params('orderById')){
        $orderById=true;
    }

    $stat = $round;

    if($app->request()->params('stat')!=null){
        $stat=$app->request()->params('stat');
    }

    $result=null;

    $team=$db_rounds->getTeam($id,$round);

    $valid=$db_rounds->isValidFormation($id,$round);

    $response["valid_formation"]=$valid;

    if($team!=null && $team->getPlayers()!=null){
        if($orderByRole) {
            $temp = $team->mapOrderedByRole($stat);
        }else if($orderById){
	        $temp = $team->mapOrderById($stat);
	    }else{
            $temp=$team->map($stat);
        }

        $result=$temp;
    }else{
        $result=null;
    }


    if($result!=null && $db_rounds->isValidFormation($id,$round)){
        $response["error"] = false;
        $response["data"]=$result;
    }else {
        // unknown error occurred
        $response['error'] = true;
        $response['message'] = "Dump Team ID USER:".$id." and ROUND:".$round." Error";
    }

    echoRespnse(200, $response);


});

$app->post('/competitions/:id', function ($id) use ($app) {

	$apiKey = $app->request->headers->get('Token');

    $db_competitions = new ConnectDatabaseCompetitions(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);
	$db_users = new ConnectDatabaseUsers($db_competitions->mysqli);

    $user = $db_users->getUserByApiKey($apiKey);

    $json = $app->request->getBody();

    $data = json_decode($json, true); // parse the JSON into an assoc. array

    //verifyRequiredParams(array("id","name","first_round","num_rounds","users"),$app);

    $name = $data["name"];
    $first_round  = $data["first_round"];
    $num_rounds = $data["num_rounds"];

    if($db_users->checkApi($apiKey) && $user!=null ){
        $response["error"] = false;

        $ret = $db_competitions->createCompetition($name,$first_round,$num_rounds);
        
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
    

    if($standings!=null && $teams!=null && $competition!=null){
        $response["error"] = false;
        $response["data"]["standings"]=$standings;
        $response["data"]["handicaps"]=getHandicapsCompetitionById($id_competition);
        $response["data"]["competition"] =$competition;
        $response["data"]["teams"]=$teams;
        
        $ids = array();
        foreach($teams as $team){
		    $ids[]  = $team["id"];
	    }
	    
	    $response["data"]["ids"] = $ids;
        
    }else {
        // unknown error occurred
        $response['error'] = true;
        $response['message'] = "Dump ID COMP:".$id_competition." Error";
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
            
    foreach($teams as $team){
	    $ids[]  = $team["id"];
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
        $response["data"]["ids"]=$ids;
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

    for($i=0 ; $i<count($standings) ; $i++){
        $standings[$i]["team_info"]=$db->getUserById(intval($standings[$i]["id_user"]))->mapBasic();
    }

    if($standings!=null){
        $response["error"] = false;
        $response["data"]["standings"]=$standings;
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

	    for($i=0 ; $i<count($standings) ; $i++){
	        $standings[$i]["team_info"]=$db->getUserById(intval($standings[$i]["id_user"]))->mapBasic();
	    }
	    
	    $teams=$db_competitions->getUsersInCompetition($id_competition);

        $response["error"] = false;
        $response["data"]["standings"]=$standings;
        $response["data"]["handicaps"]=getHandicapsRoundByRoundId($round);
        $response["data"]["competition"] =$competition;
        $response["data"]["teams"] = $teams;
        
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
    }else{
	    $response["error"] = true;
	    $response["message"] = "Competition Not Found";
    }


    echoRespnse(200, $response);


});

/// ROUNDS

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
	        	        
        }else if($type == "CLOSE"){
	        	        
            $response["error"] = !$db_rounds->closeRound($round);
	        	        
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

$app->get('/rounds/:id', function ($id) use ($app) {

	// Round is Real Round

    $db = new ConnectDatabaseUsers(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);
    $db_rounds = new ConnectDatabaseRounds($db->mysqli);
    $db_competitions = new ConnectDatabaseCompetitions($db->mysqli);

    //$id_competition=$app->request()->params('competition');


    $result=null;

    if($db_rounds->roundExist($id)){
        $response["error"] = false;
        $open=$db_rounds->isOpenRound($id);
        $response["data"]["open"] = $open;
        $response["data"]["formations_editing"]=$db_rounds->isPossibleToEditFormation($id);
        if(!$open){
	        $response["data"]["results"] = $db_rounds->getInfoRound($id);
	    }
    }else{
	    $response["error"] = true;
	    $response["message"] = "Round Not Found";
    }


    echoRespnse(200, $response);


});


/// HANDICAPS


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

$app->get('/markets/:id/transfers/:user', function ($id_market,$id_user) use ($app) {


    $db = new ConnectDatabaseMarkets(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);

    $transfers = $db->getTransfers($id_user);
		
    $result=null;
    
    if($transfers!=null){
	    $arr = array();
	    foreach($transfers as $transfer){
		    if($transfer->getIdMarket() == $id_market){
		    	$arr[] = $transfer->map();
		    }
	    }
        $result=$arr;
    }


    if($result!=null){
        $response["error"] = false;
        $response["data"]=$result;
    }else {
        // unknown error occurred
        $response['error'] = true;
        $response['message'] = "Dump Market ID:".$id_market."  Transfers $id_user Error";
    }

    echoRespnse(200, $response);


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

    $id_user = $data["id_user"];
    $id_new  = $data["id_new"];
    $id_old  = $data["id_old"];
    $id_market = $data["id_market"];


    $user = $db->getUserByApiKey($apiKey);

    $error_code=null;

    if($db->checkApi($apiKey) && $user!=null && $id_user==$user->getId()){
        $response["error"] = false;

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



$app->get('/config', function () use ($app) {


    $db = new ConnectDatabase(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);
    $db_rounds = new ConnectDatabaseRounds($db->mysqli);

    $config = $db->dumpConfig();

    $json=$config;



    if($json!=null){
	    $json["last_stat_round"] = $db_rounds->getLastStatRound();
	    $json["seconds_to_closing_time"] = $db_rounds->secondsToClosingTime();
        $response["error"] = false;
        $response["data"]=$json;
    }else {
        // unknown error occurred
        $response['error'] = true;
        $response['message'] = "Config Dump Error";
    }

    echoRespnse(200, $response);


});

$app->post('/config', function () use ($app) {

    $apiKey = $app->request->headers->get('Token');

    $db = new ConnectDatabaseUsers(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);

    $json = $app->request->getBody();
    $data = json_decode($json, true); // parse the JSON into an assoc. array

    if($db->checkApi($apiKey) && $db->getUserByApiKey($apiKey)->getAuth()==1){
        $response["error"] = false;

        if($data!=null){
            foreach($data as $config){
                $db->editConfig($config['name'],$config['value']);
            }
            $response["error"] = false;
            $response["data"]  = $db->dumpConfig();
        }else{
            $response['error'] = true;
            $response['message'] = "Config JSON is Null";
        }
    }else {
        // unknown error occurred
        $response['error'] = true;
        $response['message'] = "Authentication Token is Wrong";
    }

    echoRespnse(200, $response);


});

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




/**
 * User Login
 * url - /login
 * method - POST
 * params - email, password
 */
$app->post('/login', function() use ($app) {
    // check for required params
    verifyRequiredParams(array('username', 'password'),$app);

    // reading post params

    $json = $app->request->getBody();
    $data = json_decode($json, true); // parse the JSON into an assoc. array

    $username = $data['username'];
    $password = $data['password'];

    $response = array();

    $response['apiKey'] = null;


    $db = new ConnectDatabaseUsers(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);
    // check for correct email and password
    if (checkLogin($username, $password)) {
        // get the user by email
        $user = $db->getUserByUsername($username);


        if ($user != NULL) {
            $response["error"] = false;

            $mapped=$user->map();

            $response['data']=$mapped;



            if(isset($mapped['apiKey'])){
                $apiKey = $mapped['apiKey'];
            }else{
                $apiKey=generateApiKey();
                $db->setApiKey($username,$apiKey);
            }

            $response['apiKey'] = $apiKey;

        } else {
            // unknown error occurred
            $response['error'] = true;
            $response['message'] = "An error occurred. Please try again";
        }
    } else {
        // user credentials are wrong
        $response['error'] = true;
        $response['message'] = 'Login failed. Incorrect credentials';
    }

    echoRespnse(200, $response);


});

function echoRespnse($status_code, $response) {
    $app = \Slim\Slim::getInstance();
    // Http response code
    $app->status($status_code);

    // setting response content type to json
    $app->contentType('application/json');

    echo json_encode($response);
}

function direct($status_code, $response) {
    $app = \Slim\Slim::getInstance();
    // Http response code
    $app->status($status_code);

    // setting response content type to json
    $app->contentType('application/json');

	var_dump($response);
}

function generateApiKey() {
    return md5(uniqid(rand(), true));
}

function verifyRequiredParams($required_fields,$app) {
    $error = false;
    $error_fields = "";
    $request_params = array();
    $request_params = $_REQUEST;


    $json = $app->request->getBody();


    $data = json_decode($json, true); // parse the JSON into an assoc. array


    if($request_params == null || count($request_params)==0){
        $request_params=$data;
    }

    // Handling PUT request params
    /*if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        $app = \Slim\Slim::getInstance();
        parse_str($app->request()->getBody(), $request_params);
    }*/
    foreach ($required_fields as $field) {
        if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
            $error = true;
            $error_fields .= $field . ', ';
        }
    }

    if ($error) {
        // Required field(s) are missing or empty
        // echo error json and stop the app
        $response = array();
        $app = \Slim\Slim::getInstance();
        $response["error"] = true;
        $response["message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
        echoRespnse(400, $response);
        $app->stop();
    }
}


function checkLogin($username,$password){

    $database_users = new ConnectDatabaseUsers(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);

    $user_data=$database_users->getUserByUsername($username);

    if($user_data!=null){
        if($password==$user_data->getPassword()){
            return true;
        }
    }

    return false;
}

function checkApi($username,$apiKey){

    $database_users = new ConnectDatabaseUsers(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);

    return $database_users->checkApi($username,$apiKey);

}

$app->run();

?>
