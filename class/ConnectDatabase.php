<?php

class ConnectDatabase {

	public $username;
	public $password;
	public $ip;
	public $port;
	public $database_name;

	public $mysqli;

	function __construct5($ip,$username,$password,$database_name,$port){
		$this->username=$username;
		$this->password=$password;
		$this->ip=$ip;
		$this->port=$port;
		$this->database_name=$database_name;

		$this->mysqli = new mysqli($ip,$username,$password,$database_name,$port);
		if ($this->mysqli->connect_errno) {
		    echo "Failed to connect to MySQL: (" . $this->mysqli->connect_errno . ") " . $this->mysqli->connect_error;
		}


	}

	public function __construct() {
        $get_arguments       = func_get_args();
        $number_of_arguments = func_num_args();

        if (method_exists($this, $method_name = '__construct'.$number_of_arguments)) {
            call_user_func_array(array($this, $method_name), $get_arguments);
        }
    }


	function __construct1($mysqli){
		$this->mysqli = $mysqli;
		if ($this->mysqli->connect_errno) {
		    echo "Failed to connect to MySQL: (" . $this->mysqli->connect_errno . ") " . $this->mysqli->connect_error;
		}
	}


	
	

	function close(){
		$this->mysqli->close();
	}

	
	function dumpConfig(){
		$query="select * from `settings`";
		try{
			if (!($stmt = $this->mysqli->prepare($query))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
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
			if (!($stmt = $this->mysqli->prepare($query))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
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
			if (!($stmt = $this->mysqli->prepare($query))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}

            $query="UPDATE `settings` SET value='".$round."' WHERE name='current_round' ";

            if (!($stmt2 = $this->mysqli->prepare($query))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt2->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}
			
			$settings=$this->dumpConfig();

			$tempQuery="UPDATE `settings` SET value=? where name='last-round' ";
			
			$round=$round-1;

			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt->bind_param("s", $round)) {
			    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			return true;


		}catch(exception $e) {
			echo "\nERRORE DUMP CONFIG: ".$e;
			return false;
		}


	}

}

function is_num($number) {
  if(!is_numeric($number)) {
    throw new Exception("Value is not number");
  }
  return true;
}

?>