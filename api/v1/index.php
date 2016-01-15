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

require '../../class/Phase.php';
require '../../class/Group.php';
require '../../class/Match.php';

require '../../class/Team.php';

require '../../class/News.php';


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


function is_field($stringa,$array){
	if(count(preg_grep( "/".$stringa."/i",$array)) > 0){
		return true;
	}
	
	return false;
}


$app = new \Slim\Slim(array(
    'debug' => true
));

require 'api.users.php';
require 'api.account.php';
require 'api.players.php';
require 'api.competitions.php';
require 'api.handicaps.php';
require 'api.markets.php';
require 'api.news.php';
require 'api.rounds.php';




$app->get('/config', function () use ($app) {


    $db = new ConnectDatabase(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);
    $db_rounds = new ConnectDatabaseRounds($db->mysqli);

    $config = $db->dumpConfig();

    $json=$config;



    if($json!=null){
	    $json["last_stat_round"] = $db_rounds->getLastStatRound();
	    $json["seconds_to_closing_time"] = $db_rounds->secondsToClosingTime();
	    
	    
	    $datetemp = date ("Y-m-d H:i:s", strtotime("2015-08-22 17:00:00"));
		$date=new DateTime($datetemp);
		
		$now=new DateTime("now");
							
		$stamp_now=$now->getTimestamp();
		$stamp_finish=$date->getTimestamp();

		$diff=$stamp_finish-$stamp_now;  
		        
        if($diff>0){
			$json["creation_market"] = 1;
		}else{
			$json["creation_market"] = 0;
		}
		
	    $json["color_p"] = "#cc0000";
	    $json["color_d"] = "#008000";
	    $json["color_c"] = "#ddae36";
	    $json["color_a"] = "#003366";

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

    echo json_encode($response,JSON_PRETTY_PRINT);
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
