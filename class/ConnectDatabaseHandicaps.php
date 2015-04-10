<?php

class ConnectDatabaseHandicaps extends ConnectDatabase {

	/*function __construct1($mysqli){
		parent::__construct1($mysqli);
		$data_user = new ConnectDatabaseUsers($mysqli);
		$data_competition = new ConnectDatabaseCompetitions($mysqli);
	}

	function __construct5($ip,$username,$password,$database_name,$port){
		parent::__construct5($ip,$username,$password,$database_name,$port);
		$data_user = new ConnectDatabaseUsers($mysqli);
		$data_competition = new ConnectDatabaseCompetitions($mysqli);
	}

	public function __construct() {
        $get_arguments       = func_get_args();
        $number_of_arguments = func_num_args();

        if (method_exists($this, $method_name = '__construct'.$number_of_arguments)) {
            call_user_func_array(array($this, $method_name), $get_arguments);
        }
    }*/


	function getHandicapsRounds(){

		$data_user = new ConnectDatabaseUsers($this->mysqli);
		
		try{
			$tempQuery="SELECT * FROM handicaps_rounds order by id_user ";

			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			/*if (!$stmt->bind_param("i", $id_competition)) {
			    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}*/

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			$res=$stmt->get_result();
			$res->data_seek(0);
			$arr=array();

			while ($row = $res->fetch_assoc()) {
				$id=$row['id'];
				$id_user=$row['id_user'];
				$id_round=$row['id_round'];
				$description=$row['description'];
				$points=$row['points'];

				$user=$data_user->getUserById($id_user);
				//$competition=$this->data_competition->getCompetition($id_competition);

				$arr[] = new HandicapRound($id,$user,$description,$points,$id_round);

			}

			return $arr;

		}catch(exception $e) {
			echo "\nERRORE Get Handicaps Rounds: ".$e;
			return null;
		}
	}

	function getHandicapsStandings(){
		
		$data_user = new ConnectDatabaseUsers($this->mysqli);
		$data_competition = new ConnectDatabaseCompetitions($this->mysqli);
		
		try{
			$tempQuery="SELECT * FROM handicaps_standings order by id_user,id_competition  ";

			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			/*if (!$stmt->bind_param("i", $id_competition)) {
			    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}*/

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			$res=$stmt->get_result();
			$res->data_seek(0);
			$arr=array();

			while ($row = $res->fetch_assoc()) {
				$id=$row['id'];
				$id_user=$row['id_user'];
				$id_competition=$row['id_competition'];
				$description=$row['description'];
				$points=$row['points'];

				$user = $data_user->getUserById($id_user);
				$competition = $data_competition->getCompetition($id_competition);

				$arr[] = new HandicapStanding($id,$user,$description,$points,$competition);

			}

			return $arr;

		}catch(exception $e) {
			echo "\nERRORE Get Handicaps Standings: ".$e;
			return null;
		}
	}

	function getHandicapsRoundsByUserId($id_user){

		$data_user = new ConnectDatabaseUsers($this->mysqli);
		
		try{
			$tempQuery="SELECT * FROM handicaps_rounds where id_user=? order by id_round ";

			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt->bind_param("i", $id_user)) {
			    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			$res=$stmt->get_result();
			$res->data_seek(0);
			$arr=array();

			while ($row = $res->fetch_assoc()) {
				$id=$row['id'];
				$id_round=$row['id_round'];
				$description=$row['description'];
				$points=$row['points'];

				$user = $data_user->getUserById($id_user);
				//$competition=$this->data_competition->getCompetition($id_competition);

				$arr[] = new HandicapRound($id,$user,$description,$points,$id_round);

			}

			return $arr;

		}catch(exception $e) {
			echo "\nERRORE Get Handicaps Rounds By User ID: ".$e;
			return null;
		}
	}

	function getHandicapsStandingsByUserId($id_user){

		$data_user = new ConnectDatabaseUsers($this->mysqli);
		$data_competition = new ConnectDatabaseCompetitions($this->mysqli);

		try{
			$tempQuery="SELECT * FROM handicaps_standings where id_user=? order by id_competition ";

			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt->bind_param("i", $id_user)) {
			    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			$res=$stmt->get_result();
			$res->data_seek(0);
			$arr=array();

			while ($row = $res->fetch_assoc()) {
				$id=$row['id'];
				$description=$row['description'];
				$points=$row['points'];
				$id_competition=$row['id_competition'];

				$user = $data_user->getUserById($id_user);
				$competition=$data_competition->getCompetition($id_competition);

				$arr[] = new HandicapStanding($id,$user,$description,$points,$competition);

			}

			return $arr;

		}catch(exception $e) {
			echo "\nERRORE Get Handicaps Standings By User ID: ".$e;
			return null;
		}
	}

	function setHandicapRound($id_user,$id_round,$description,$points){

		$data_user = new ConnectDatabaseUsers($this->mysqli);
		$data_competition = new ConnectDatabaseCompetitions($this->mysqli);

		try{
			$tempQuery="INSERT INTO `handicaps_rounds`( `id_user`, `id_round`, `description`, `points`) VALUES (?,?,?,?)";

			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt->bind_param("iisi", $id_user,$id_round,$description,$points)) {
			    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			return true;

		}catch(exception $e) {
			echo "\nERRORE Set Handicaps Rounds: ".$e;
			return false;
		}
	}

	function setHandicapStanding($id_user,$id_competition,$description,$points){

		$data_user = new ConnectDatabaseUsers($this->mysqli);
		$data_competition = new ConnectDatabaseCompetitions($this->mysqli);

		try{
			$tempQuery="INSERT INTO `handicaps_standings`( `id_user`, `id_competition`, `description`, `points`) VALUES (?,?,?,?)";

			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt->bind_param("iisi", $id_user,$id_competition,$description,$points)) {
			    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			return true;

		}catch(exception $e) {
			echo "\nERRORE Set Handicaps Rounds: ".$e;
			return false;
		}
	}


}

?>