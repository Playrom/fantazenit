<?php


$app->post('/users', function () use ($app) {

	$apiKey = $app->request->headers->get('Token');

	$db_users = new ConnectDatabaseUsers(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);
    
    $json = $app->request->getBody();

    $data = json_decode($json, true); // parse the JSON into an assoc. array

    //verifyRequiredParams(array("id","name","first_round","num_rounds","users"),$app);
    
    $original_password = $data["password"];
    
    $username = $data["username"];
    $name = $data["name"];
    $surname = $data["surname"];
    $password = md5($data["password"]);
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
	        
	        $headers = "From: info@associazionezenit.it \r\n";
			$headers .= "Reply-To: info@associazionezenit.it \r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
			
			$message = "<h1>Iscrizione Fanta Zenit</h1>

						<p>Ciao $name,</p>
						
						<p>Ti ringraziamo per esserti iscritto al <strong>Fanta Zenit 2015/16</strong>, ora puoi creare la tua squadra, inserire la formazione, e cominciare questo fantastico campionato.</p>
						
						
						
						<p>Per completare la tua iscrizione contatta uno degli nostri amministratori, a <a href=\"mailto:info@associazionezenit.it\">info@associazionezenit.it</a> , oppure attraverso la nostra <a href=\"http://www.facebook.com/fantazenit\">Pagina Facebook</a>, oppure puoi consegnare i soldi dell'iscrizione presso<br>Narciso Cafè, Piazza Santa Caterina , Messina<br>Goldbet , Via Calabria 26, Messina , Tra Discoteca Koko e Ristorante Porta Messina , pressi Stazione Marittima</p>
						
						<p>I Tuoi dati di accesso sono:</p>
						
						<table>
						<tbody>
						<tr>
							<td>Username</td>
							<td>$username</td>
						</tr>
						
						<tr>
							<td>Password</td>
							<td>$original_password</td>
						</tr>
						</tbody>
						</table>
						";
	        
	        mail($email, "Iscrizione Fanta Zenit 2015/16", $message , $headers);
	        
	        $us = $db_users->getUserByEmail($email);
	        
	        if($us!=null){
		        $response["data"]["id"] = $us->getId();
		        $response["error"] = false;
	        }else{
		        $response["error"] = true;
		        $response["message"] = "Non Ritorna Utente con quella email";
	        }
	        
	        
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



$app->post('/users/:id/avatar' , function ($id) use ($app) {
	


	$db = new ConnectDatabaseUsers(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);
    
    $json = $app->request->getBody();

    $data = json_decode($json, true); // parse the JSON into an assoc. array

    //verifyRequiredParams(array("id","name","first_round","num_rounds","users"),$app);
    
    $response = null;
    
    
    if(isset($data["avatar"])){
	    
	    $user = $db->getUserById($id);
	    
	    $url = $data["avatar"];
	    
	    if( $user!=null){
		    $result = $db->editUser($user->getId(),null,null,null,null,$url);
		    
		    $response["error"] = !$result;
		    $response["message"] = "Avatar Non Caricato Correttamente";
	    }
    
    }else {
        // unknown error occurred
        $response['error'] = true;
        $response['message'] = "Old Password Not Correct";
    }
    

    echoRespnse(200, $response);


});



$app->get('/users', function () use ($app) {


    $db = new ConnectDatabaseUsers(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);

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
	    
	    
	    if($app->request()->params('fields')){
	        
	        $fields = explode(",",$app->request()->params('fields'));
	        
	        if(is_field("roster",$fields)){
		        
		        $user->setPlayers($db->getUserRosterById($user->getId()));
		        
	        }
	        
	        if(is_field("transfers",$fields)){
		        
		        $transfers=$db_markets->getTransfers($user->getId());
		        $user->setTransfers($transfers);
		        		        
	        }
	        
	        
	    }

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

$app->get('/users/:id_team/teams/:round', function ($id,$round) use ($app) {

    $db = new ConnectDatabaseUsers(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);
    $db_rounds = new ConnectDatabaseRounds($db->mysqli);

    $orderByRole=false;
    $orderById = false;
    
    
    // Get request object
	$req = $app->request;
	
	//Get resource URI
	$resourceUri = $req->getResourceUri();
	    
    if($app->request()->params('orderByRole')){
        $orderByRole=true;
    }else if($app->request()->params('orderById')){
        $orderById=true;
    }
    
    $filename = "cache/".preg_replace('/[^A-Za-z0-9_\/\\\-]/', '_', $resourceUri);
    
    if($_SERVER['QUERY_STRING'] != null && $_SERVER['QUERY_STRING'] != ""){
	    $filename = $filename."-".preg_replace('/[^A-Za-z0-9_\/\\\-]/', '_', $_SERVER['QUERY_STRING']);
    }
    
	$filename = $filename.".json";
    
    $result=null;
        
    $response = null;
    
    $valid=$db_rounds->isValidFormation($id,$round);
    
    if(is_file($filename) > 0 && $valid){
	    $fp = fopen($filename, "r");
	    $response = json_decode(fread($fp, filesize($filename)));
	    fclose($fp);
    }else{
	    
	    $stat = $round;
	
	    if($app->request()->params('stat')!=null){
	        $stat=$app->request()->params('stat');
	    }
	
	    
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
    	
    	try{
	    
		    $dirname = dirname($filename);
			if(!is_dir($dirname)){
			    mkdir($dirname, 0755, true);
			}
		    
		    $fp = fopen($filename, "w");
		    fwrite($fp, json_encode($response,JSON_PRETTY_PRINT));
		    fclose($fp);
		    
		}catch(exception $e){
			error_log($resouceUri." - ".$e);
		}

	}

    
	
	//$end = microtime(true) - $start;
	//error_log($end);
	
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


    if($db->checkApi($apiKey) && $user!=null && ( $id_user==$user->getId() || $db->checkAuthOverride($apiKey) ) ){
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

?>