<?php



	private	$username="root";
	private $password="aicon07";
	private $ip="localhost";
	private $port=3306;
	private $database_name="fantacalcio";

	private $mysqli= new mysqli($ip,$username,$password,$database_name,$port);
    
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }


	require_once('ConnectDatabasePlayers.php');
	require_once('ConnectDatabaseUsers.php');
	require_once('ConnectDatabaseMarket.php');
	require_once('ConnectDatabaseRounds.php');
	require_once('ConnectDatabaseCompetitions.php');
	require_once('ConnectDatabaseFiles.php');


	public function __call($method_name , $parameter){
		if($method_name == "dumpPlayer"){ //Function overloading logic for function name overlodedFunction
			$count = count($parameter);
			if(is_string($parameter[0])) return dumpPlayerByName($parameter[0]);
			else if(is_int($parameter[0])) return dumpPlayerById($parameter[0]);
			else throw new exception("Function $method_name with type=$parameter , does not exists ");
		}
	}

	

	function close(){
		$mysqli->close();
	}

    

	
	function dumpConfig(){
		$query="select * from `settings`";
		try{
			if (!($stmt = $mysqli->prepare($query))) {
			    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			$res=$stmt->get_result();
			$res->data_seek(0);

			$config=array();

			while ($row = $res->fetch_assoc()) {
				$id=$row['id'];
				$name=$row['name'];
				$value=$row['value'];
				$config[$name]=$value;
			}


		}catch(exception $e) {
			echo "\nERRORE DUMP CONFIG: ".$e;
			return null;
		}

		return $config;
	}

    function editConfig($name,$value){
        $query="UPDATE settings SET value='".$value."' WHERE name='".$name."' ";
        try{
			if (!($stmt = $mysqli->prepare($query))) {
			    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}

		}catch(exception $e) {
			echo "\nERRORE EDIT CONFIG: ".$e;
			return false;
		}
        return true;
	}


	function setCurrentRound($round,$date_in){
		$date=DateTime::createFromFormat('d-m-Y H:i', $date_in);
		$query="UPDATE `rounds` SET closetime='".$date->format("Y-m-d H:i:00")."' WHERE round=".$round;

		try{
			if (!($stmt = $mysqli->prepare($query))) {
			    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}

            $query="UPDATE `settings` SET value='".$round."' WHERE name='current_round' ";

            if (!($stmt2 = $mysqli->prepare($query))) {
			    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
			}

			if (!$stmt2->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			return true;


		}catch(exception $e) {
			echo "\nERRORE DUMP CONFIG: ".$e;
			return false;
		}


	}



    function is_num($number) {
      if(!is_numeric($number)) {
        throw new Exception("Value is not number");
      }
      return true;
    }

?>