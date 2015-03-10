<?php

function getTeamsByRoundAndCompetition($round,$id_competition,$players){
		$arr=array();
		try{
			$tempUser="SELECT users.* FROM users LEFT OUTER JOIN users_in_competitions ON users_in_competitions.id_user=users.id WHERE users_in_competitions.id_competition=?";
			$tempQuery="SELECT teams.* , users.username FROM teams LEFT OUTER JOIN users ON users.id=teams.id_user WHERE teams.id_user=? ";

			if(!($stmt = $mysqli->prepare($tempUser))) {
			    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
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
				$id_user=$row['id'];
				$username=$row['username'];
				$name_team=$row['name_team'];
				$arr[]=$arrayName = array('username' => $username , 'name_team' => $name_team, 'id_user' => $id_user , 'team' => getTeam($id_user,$round));

			}

			return $arr;

		}catch(exception $e) {
			echo "\nERRORE GET MARKET: ".$e;
			return null;
		}
	}

function getCompetition($id_competition){
		$tempQuery="SELECT * FROM competitions  WHERE id=? ";

		try{
			if(!($stmt = $mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
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
				return new Competition($id_competition,$row['name'],$row['first_round'],$row['num_rounds']);
			}

			return null;

		}catch(exception $e) {
			echo "ex: ".$e;
			return null;

		}
	}

	function getCompetitions(){
		$tempQuery="SELECT * FROM competitions ";

		try{
			if(!($stmt = $mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
			}



			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			$res=$stmt->get_result();
			$res->data_seek(0);

			$ret=array();

			while ($row = $res->fetch_assoc()) {
				$id_competition=$row['id'];
				$name=$row['name'];
				$ret[]=new Competition($id_competition,$name,$row['first_round'],$row['num_rounds']);
			}

			return $ret;

		}catch(exception $e) {
			echo "ex: ".$e;
			return false;

		}
	}

	function createCompetition($name,$first_round,$num_rounds){
		try{
			$tempQuery="INSERT INTO `competitions` (`name`,`first_round`,`num_rounds`) VALUES (?,?,?)";

			if(!($stmt = $mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
			}

			if (!$stmt->bind_param("sii", $name,$first_round,$num_rounds)) {
			    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			return $mysqli->insert_id;

		}catch(exception $e) {
			echo "\nERRORE INSERT COMPETITION: ".$e;
			return -1;
		}
	}

	function deleteCompetition($id){
		try{
			$tempQuery="DELETE FROM `competitions` WHERE id=? ";

			if(!($stmt = $mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
			}

			if (!$stmt->bind_param("i", $id)) {
			    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			$tempQuery="DELETE FROM competitions_in_rounds  WHERE id_competition=? ";

			if(!($stmt = $mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
			}

			if (!$stmt->bind_param("i", $id)) {
			    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			return true;

		}catch(exception $e) {
			echo "\nERRORE DELETE COMPETITION: ".$e;
			return false;
		}
	}

	function editCompetition($id,$name,$first_round,$num_rounds){
		try{

			is_num($first_round);
			is_num($num_rounds);

			$mysqli->autocommit(FALSE);

			$tempQuery="REPLACE INTO `competitions` (`id`,`name`,`first_round`,`num_rounds`) VALUES (?,?,?,?)";

			if(!($stmt = $mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
			}

			if (!$stmt->bind_param("isii", $id,$name,$first_round,$num_rounds)) {
			    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			$mysqli->commit();




		}catch (exception $exception) {
		  $mysqli->rollback();
		  $mysqli->autocommit(TRUE);
		  return false;
		}finally{
		  isset($stmt) && $stmt->close();
		  $mysqli->autocommit(true);
		}
	}

	function setRoundsCompetition($id,$rounds){
		$tempQuery="DELETE FROM competitions_in_rounds  WHERE id_competition=? ";

		try{
			if(!($stmt = $mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
			}

			if (!$stmt->bind_param("i", $id)) {
			    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}
			$round_competition=1;
			$tempQuery="INSERT INTO competitions_in_rounds (`id_competition`,`round`,`round_competition`) VALUES (?,?,?)";


			for($i=0;$i<sizeof($rounds);$i++){

				if(isset($rounds[$i])){

					if(!($stmt = $mysqli->prepare($tempQuery))) {
					    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
					}

					if (!$stmt->bind_param("iii", $id,$rounds[$i],$round_competition)){
					    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
					}

					if (!$stmt->execute()) {
					    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
					}

				}

				$round_competition++;

			}

		}catch(exception $e) {
			echo "ex: ".$e;
			return null;

		}
	}



	function isUserInCompetition($id_user,$id_competition){
		$tempQuery="SELECT * FROM users_in_competitions  WHERE id_user=? and id_competition=? ";

		try{
			if(!($stmt = $mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
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
				return true;
			}

			return false;

		}catch(exception $e) {
			echo "ex: ".$e;
			return false;

		}
	}

	function getUsersInCompetition($id_competition){
		$tempQuery="SELECT * FROM users_in_competitions  WHERE id_competition=? ";

		try{
			if(!($stmt = $mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
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
				$arr[]=$row['id_user'];
			}

			return $arr;

		}catch(exception $e) {
			echo "ex: ".$e;
			return null;

		}
	}

	function getStandings($id_competition){
		$rounds=getRoundsByCompetition($id_competition);
		$results=array();


		foreach($rounds as $round){
			$tempQuery="SELECT * FROM rounds_result  WHERE round=? ";

			try{
				if(!($stmt = $mysqli->prepare($tempQuery))) {
				    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
				}

				if (!$stmt->bind_param("i", $round)) {
				    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
				}

				if (!$stmt->execute()) {
				    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
				}

				$res=$stmt->get_result();
				$res->data_seek(0);

				$round_result=array();

				while ($row = $res->fetch_assoc()) {
					$id_user=$row['id_user'];
					$round_result[$id_user]['points']=$row['points'];
					$round_result[$id_user]['gol']=$row['gol'];
					$round_result[$id_user]['user']=$row['id_user'];
				}

				$results[]=$round_result;


			}catch(exception $e) {
				echo "ex: ".$e;
				return true;

			}
		}
		$classifica=array();
		$users=getUsersInCompetition($id_competition);

		foreach($users as $user){
			$gols=0;
			$points=0;
			foreach($results as $round){
				$res=$round[$user];
				$gols+=$res['gol'];
				$points+=$res['points'];
			}
			$temp['id_user']=$user;
			$temp['gol']=$gols;
			$temp['points']=$points;
			$classifica[]=$temp;
		}

		usort($classifica, function($a, $b) { // SORT DESC ONLY BY POINTS
			$diff=$b['points'] - $a['points'];
		    return $diff;
		});

		return $classifica;

	}
    
    ?>