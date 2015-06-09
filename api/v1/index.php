<?php
require '../../vendor/autoload.php';
require '../../class/ConnectDatabase.php';
require '../../class/ConnectDatabaseUsers.php';
require '../../class/ConnectDatabasePlayers.php';
require '../../class/RosterList.php';
require '../../class/RosterPlayer.php';
require '../../class/Player.php';
require '../../class/StatisticsCollection.php';
require '../../class/Statistic.php';
require '../../class/User.php';

$app = new \Slim\Slim();

$app->get('/hello/:name', function ($name) {
    $response["message"]="Hello, $name";
    echoRespnse(200, $response);
});

$app->get('/me', function () use ($app) {
    
    $apiKey = $app->request->headers->get('Token');

    $db = new ConnectDatabaseUsers("localhost","root","aicon07","fantacalcio",3306);
    
    if($db->checkApi($apiKey)){
        $response["error"] = false;
        $response["message"]=$db->getUserByApiKey($apiKey)->map();
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


    $db = new ConnectDatabaseUsers("localhost","root","aicon07","fantacalcio",3306);
    // check for correct email and password
    if (checkLogin($username, $password)) {
        // get the user by email
        $user = $db->getUserByUsername($username);


        if ($user != NULL) {
            $response["error"] = false;
            
            $mapped=$user->map();

            $response['response']=$mapped;
            
            
            
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
    
    $database_users = new ConnectDatabaseUsers("localhost","root","aicon07","fantacalcio",3306);
       
    $user_data=$database_users->getUserByUsername($username);

    if($user_data!=null){
        if($password==$user_data->getPassword()){
            return true;
        }
    }
    
    return false;
}

function checkApi($username,$apiKey){
    
    $database_users = new ConnectDatabaseUsers("localhost","root","aicon07","fantacalcio",3306);
       
    return $database_users->checkApi($username,$apiKey);

}

$app->run();

?>
