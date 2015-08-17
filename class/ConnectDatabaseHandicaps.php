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
    
    function getMoneyBonuses(){

		$data_user = new ConnectDatabaseUsers($this->mysqli);
		
		try{
			$tempQuery="SELECT * FROM bonus_money ";

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
				$description=$row['description'];
				$points=$row['points'];

				$user=$data_user->getUserById($id_user);
				//$competition=$this->data_competition->getCompetition($id_competition);

				$arr[] = new Handicap($id,$user,$description,$points);

			}

			return $arr;

		}catch(exception $e) {
			echo "\nERRORE Get Bonuses: ".$e;
			return null;
		}
	}
	
	function getMoneyBonusesByUser($user){

		$data_user = new ConnectDatabaseUsers($this->mysqli);
		
		$id_user = $user->getId();
		
		try{
			$tempQuery="SELECT * FROM bonus_money where id_user=? ";

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

				//$competition=$this->data_competition->getCompetition($id_competition);

				$arr[] = new Handicap($id,$user,$description,$points);

			}

			return $arr;

		}catch(exception $e) {
			echo "\nERRORE Get Bonuses By User ID: ".$e;
			return null;
		}
	}
	
	function getMoneyBonusesById($id){

		$data_user = new ConnectDatabaseUsers($this->mysqli);
		
		try{
			$tempQuery="SELECT * FROM bonus_money where id=? ";

			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt->bind_param("i", $id)) {
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
				$id_user = $row["id_user"];

				$user = $data_user->getUserById($id_user);
				//$competition=$this->data_competition->getCompetition($id_competition);

				$arr[] = new Handicap($id,$user,$description,$points);

			}

			return $arr;

		}catch(exception $e) {
			echo "\nERRORE Get Bonuses By ID: ".$e;
			return null;
		}
	}
	
	
	function setMoneyBonus($id_user,$description,$points){
		
		$data_user = new ConnectDatabaseUsers($this->mysqli);
		
		try{
			$tempQuery="INSERT INTO `bonus_money`( `id_user`,  `description`, `points`) VALUES (?,?,?)";

			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt->bind_param("isi", $id_user,$description,$points)) {
			    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			return true;

		}catch(exception $e) {
			echo "\nERRORE Set Bonus: ".$e;
			return false;
		}
	}

	function deleteMoneyBonus($id){

		try{
			$tempQuery="DELETE FROM `bonus_money` WHERE id=?";

			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt->bind_param("i", $id)) {
			    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			return true;

		}catch(exception $e) {
			echo "\nERRORE DELETE Bonus: ".$e;
			return false;
		}
	}



	function getHandicapsRounds(){

		$data_user = new ConnectDatabaseUsers($this->mysqli);
		
		try{
			$tempQuery="SELECT * FROM handicaps_rounds order by id_round,id_user ";

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

	function getHandicapsCompetitions(){
		
		$data_user = new ConnectDatabaseUsers($this->mysqli);
		$data_competition = new ConnectDatabaseCompetitions($this->mysqli);
		
		try{
			$tempQuery="SELECT * FROM handicaps_competitions order by id_competition,id_user  ";

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

	function getHandicapsCompetitionsByUserId($id_user){

		$data_user = new ConnectDatabaseUsers($this->mysqli);
		$data_competition = new ConnectDatabaseCompetitions($this->mysqli);

		try{
			$tempQuery="SELECT * FROM handicaps_competitions where id_user=? order by id_competition ";

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
				//$competition=$data_competition->getCompetition($id_competition);

				$arr[] = new HandicapStanding($id,$user,$description,$points,null);

			}

			return $arr;

		}catch(exception $e) {
			echo "\nERRORE Get Handicaps Standings By User ID: ".$e;
			return null;
		}
	}

	function getHandicapsRoundsByRoundId($id_round){

		$data_user = new ConnectDatabaseUsers($this->mysqli);
		
		try{
			$tempQuery="SELECT * FROM handicaps_rounds where id_round=? order by id_user ";

			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt->bind_param("i", $id_round)) {
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
				$id_user=$row['id_user'];
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

	function getHandicapsCompetitionsByCompetitionId($id_competition){

		$data_user = new ConnectDatabaseUsers($this->mysqli);
		$data_competition = new ConnectDatabaseCompetitions($this->mysqli);

		try{
			$tempQuery="SELECT * FROM handicaps_competitions where id_competition=? order by id_user ";

			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt->bind_param("i", $id_competition)) {
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
				$id_user=$row['id_user'];
				$description=$row['description'];
				$points=$row['points'];
				$id_competition=$row['id_competition'];

				$user = $data_user->getUserById($id_user);

				$competition=$data_competition->getCompetition($id_competition);

				$arr[] = new HandicapStanding($id,$user,$description,$points,$competition);

			}

			return $arr;

		}catch(exception $e) {
			echo "\nERRORE Get Handicaps Standings By Competition ID: ".$e;
			return null;
		}
	}
	
	function getHandicapsRoundsByUserIdAndRound($id_user,$round){

		$data_user = new ConnectDatabaseUsers($this->mysqli);
		
		try{
			$tempQuery="SELECT * FROM handicaps_rounds where id_user=? and id_round=? ";

			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt->bind_param("ii", $id_user,$round)) {
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
	
	function getHandicapsCompetitionsByUserIdAndCompetition($id_user,$id_competition){

		$data_user = new ConnectDatabaseUsers($this->mysqli);
		$data_competition = new ConnectDatabaseCompetitions($this->mysqli);
		

		try{
			$tempQuery="SELECT * FROM handicaps_competitions where id_user=? and id_competition=? ";

			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt->bind_param("ii", $id_user,$id_competition)) {
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
				//$competition=$data_competition->getCompetition($id_competition);

				$arr[] = new HandicapStanding($id,$user,$description,$points,null);

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

	function setHandicapCompetition($id_user,$id_competition,$description,$points){
		
		$data_user = new ConnectDatabaseUsers($this->mysqli);
		$data_competition = new ConnectDatabaseCompetitions($this->mysqli);
		
		try{
			$tempQuery="INSERT INTO `handicaps_competitions`( `id_user`, `id_competition`, `description`, `points`) VALUES (?,?,?,?)";

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

	function deleteHandicapRound($id_handicap){

		try{
			$tempQuery="DELETE FROM `handicaps_rounds` WHERE id=?";

			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt->bind_param("i", $id_handicap)) {
			    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			return true;

		}catch(exception $e) {
			echo "\nERRORE DELETE Handicaps Rounds: ".$e;
			return false;
		}
	}

	function deleteHandicapCompetition($id_handicap){

		try{
			$tempQuery="DELETE FROM `handicaps_competitions` WHERE id=?";

			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt->bind_param("i", $id_handicap)) {
			    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			return true;

		}catch(exception $e) {
			echo "\nERRORE DELETE Handicaps Competitions: ".$e;
			return false;
		}
	}


}

?>