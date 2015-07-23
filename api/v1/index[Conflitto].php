<?php
require '../../vendor/autoload.php';
require '../../class/ConnectDatabase.php';
require '../../class/ConnectDatabaseUsers.php';
require '../../class/ConnectDatabasePlayers.php';
require '../../class/ConnectDatabaseMarkets.php';
require '../../class/ConnectDatabaseRounds.php';
require '../../class/ConnectDatabaseCompetitions.php';
require '../../class/RosterList.php';
require '../../class/RosterPlayer.php';
require '../../class/Player.php';
require '../../class/StatisticsCollection.php';
require '../../class/Statistic.php';
require '../../class/User.php';

require '../../class/Team.php';

require '../../class/TeamPlayerList.php';
require '../../class/TeamPlayerRound.php';
require '../../class/PlayersList.php';
require '../../class/Market.php';
require '../../class/Transfer.php';
require '../../class/Competition.php';

$app = new \Slim\Slim(array(
    'debug' => true
));


$app->get('/hello/:name', function ($name) {
    $response["message"]="Hello, $name";
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

$app->get('/users', function () use ($app) {


    $db = new ConnectDatabaseUsers(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);

    $db_markets = new ConnectDatabaseMarkets($db->mysqli);

    $users = $db->getUsers();

    $result=array();

    foreach($users as $user){
        $transfers=$db_markets->getTransfers($user);
        $user->setTransfers($transfers);
        $temp=$user->mapTeam();
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

    $user = $db->getUserById($id);

    $result=null;

    if($user!=null){
        $transfers=$db_markets->getTransfers($user);
        $user->setTransfers($transfers);
        $temp=$user->mapTeam();
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

$app->get('/teams', function () use ($app) {

    verifyRequiredParams(array('round', 'competition'),$app);

    $db = new ConnectDatabaseUsers(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);
    $db_rounds = new ConnectDatabaseRounds($db->mysqli);

    $round=$app->request()->params('round');
    $id_competition=$app->request()->params('competition');


    $result=null;

    $teams=$db_rounds->getTeamsByRoundAndCompetition($round,$id_competition);

    $orderByRole=false;

    if($app->request()->params('orderByRole')){
        $orderByRole=true;
    }

    if($teams!=null){
        for($i=0;$i<count($teams);$i++){
            if($orderByRole) {
                if($teams[$i]["team"]->getPlayers()!=null) {
                   $teams[$i]["team"] = $teams[$i]["team"]->mapOrderedByRole();
                }
            }else {
                $teams[$i]["team"] = $teams[$i]["team"]->map();
            }
        }
    }
    
    if($teams!=null){
        $response["error"] = false;
        $response["data"]=$teams;
    }else {
        // unknown error occurred
        $response['error'] = true;
        $response['message'] = "Dump Teams ID COMP:".$id_competition." and ROUND:".$round." Error";
    }


    echoRespnse(200, $response);


});

$app->get('/team/:id_team/:round', function ($id,$round) use ($app) {



    $db = new ConnectDatabaseUsers(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);
    $db_rounds = new ConnectDatabaseRounds($db->mysqli);

    $orderByRole=false;

    if($app->request()->params('orderByRole')){
        $orderByRole=true;
    }

    $result=null;

    $team=$db_rounds->getTeam($id,$round);

    $valid=$db_rounds->isValidFormation($id,$round);

    $response["valid_formation"]=$valid;

    if($team!=null && $team->getPlayers()!=null){
        if($orderByRole) {
            $temp = $team->mapOrderedByRole();
        }else{
            $temp=$team->map();
        }

        $result=$temp;
    }else{
        $result=null;
    }


    if($result!=null){
        $response["error"] = false;
        $response["data"]=$result;
    }else {
        // unknown error occurred
        $response['error'] = true;
        $response['message'] = "Dump Team ID USER:".$id." and ROUND:".$round." Error";
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


$app->get('/market/:id', function ($id) use ($app) {


    $db = new ConnectDatabaseMarkets(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);

    $market = $db->getMarketById($id);

    $result=null;

    if($market!=null){
        $temp=$market->map();
        $result=$temp;
    }

    $json=json_encode($result,true);

    if($json!=null){
        $response["error"] = false;
        $response["data"]=$json;
    }else {
        // unknown error occurred
        $response['error'] = true;
        $response['message'] = "Dump Market ID:".$id." Error";
    }

    echoRespnse(200, $response);


});

$app->get('/config', function () use ($app) {


    $db = new ConnectDatabase(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);

    $config = $db->dumpConfig();

    $json=json_encode($config,true);

    if($json!=null){
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
 
    echo json_encode($response);
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
    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        $app = \Slim\Slim::getInstance();
        parse_str($app->request()->getBody(), $request_params);
    }
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
