<?php
class ConnectDatabaseCompetitions extends ConnectDatabase {
    
    function getTeamsByRoundAndCompetition($round,$id_competition,$players){
		$arr=array();
		try{
			$tempUser="SELECT users.* FROM users LEFT OUTER JOIN users_in_competitions ON users_in_competitions.id_user=users.id WHERE users_in_competitions.id_competition=?";
			$tempQuery="SELECT teams.* , users.username FROM teams LEFT OUTER JOIN users ON users.id=teams.id_user WHERE teams.id_user=? ";

			if(!($stmt = $this->mysqli->prepare($tempUser))) {
			    error_log("Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error);
			}

			if (!$stmt->bind_param("i", $id_competition)) {
			    error_log("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
			}

			if (!$stmt->execute()) {
			    error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
			}

			$res=$stmt->get_result();
			$res->data_seek(0);
			$arr=array();

			while ($row = $res->fetch_assoc()) {
				$id_user=$row['id'];
				$username=$row['username'];
				$name_team=$row['name_team'];
				$arr[]=$arrayName = array('username' => $username , 'name_team' => $name_team, 'id_user' => $id_user , 'team' => $this->getTeam($id_user,$round));

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
			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    error_log("Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error);
			}

			if (!$stmt->bind_param("i", $id_competition)) {
			    error_log("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
			}

			if (!$stmt->execute()) {
			    error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
			}

			$res=$stmt->get_result();
			$res->data_seek(0);

			$arr=array();

			while ($row = $res->fetch_assoc()) {
				return new Competition($id_competition,$row['name'],$row['first_round'],$row['num_rounds'],$row['type']);
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
			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    error_log("Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error);
			}



			if (!$stmt->execute()) {
			    error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
			}

			$res=$stmt->get_result();
			$res->data_seek(0);

			$ret=array();

			while ($row = $res->fetch_assoc()) {
				$id_competition=$row['id'];
				$name=$row['name'];
				$ret[]=new Competition($id_competition,$name,$row['first_round'],$row['num_rounds'],$row['type']);
			}

			return $ret;

		}catch(exception $e) {
			echo "ex: ".$e;
			return false;

		}
	}

	function createCompetition($name,$first_round,$num_rounds,$type){
				
		try{
			$tempQuery="INSERT INTO `competitions` (`name`,`first_round`,`num_rounds` , `type`) VALUES (?,?,?,?)";

			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    error_log("Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error);
			}

			if (!$stmt->bind_param("siis", $name,$first_round,$num_rounds,$type)) {
			    error_log("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
			}

			if (!$stmt->execute()) {
			    error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
			}
			
			$id = $this->mysqli->insert_id;
			return $id;
		}catch(exception $e) {
			echo "\nERRORE INSERT COMPETITION: ".$e;
			return -1;
		}
	}

	function deleteCompetition($id){
		try{
			$tempQuery="DELETE FROM `competitions` WHERE id=? ";

			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    error_log("Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error);
			}

			if (!$stmt->bind_param("i", $id)) {
			    error_log("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
			}

			if (!$stmt->execute()) {
			    error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
			}

			$tempQuery="DELETE FROM competitions_in_rounds  WHERE id_competition=? ";

			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    error_log("Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error);
			}

			if (!$stmt->bind_param("i", $id)) {
			    error_log("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
			}

			if (!$stmt->execute()) {
			    error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
			}
            
            $tempQuery="DELETE FROM users_in_competitions  WHERE id_competition=? ";

			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    error_log("Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error);
			}

			if (!$stmt->bind_param("i", $id)) {
			    error_log("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
			}

			if (!$stmt->execute()) {
			    error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
			}
			
			$tempQuery="DELETE FROM round_robin  WHERE id_competition=? ";

			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    error_log("Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error);
			}

			if (!$stmt->bind_param("i", $id)) {
			    error_log("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
			}

			if (!$stmt->execute()) {
			    error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
			}
			
			$tempQuery="DELETE FROM phases  WHERE id_competition=? ";

			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    error_log("Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error);
			}

			if (!$stmt->bind_param("i", $id)) {
			    error_log("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
			}

			if (!$stmt->execute()) {
			    error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
			}
			
			$tempQuery="DELETE FROM matches WHERE id_group IN ( SELECT * FROM ( SELECT id_group FROM groups WHERE id_competition=? ) AS p )";

			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    error_log("Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error);
			}

			if (!$stmt->bind_param("i", $id)) {
			    error_log("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
			}

			if (!$stmt->execute()) {
			    error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
			}
			
			$tempQuery="DELETE FROM groups WHERE id_competition=? ";

			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    error_log("Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error);
			}

			if (!$stmt->bind_param("i", $id)) {
			    error_log("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
			}

			if (!$stmt->execute()) {
			    error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
			}

			return true;

		}catch(exception $e) {
			error_log("\nERRORE DELETE COMPETITION: ".$e);
			return false;
		}
	}

	function editCompetition($id,$name,$first_round,$num_rounds){
		
		
		try{

			is_num($first_round);
			is_num($num_rounds);
			

			$this->mysqli->autocommit(FALSE);

			$tempQuery="UPDATE `competitions` SET name=? , first_round=? , num_rounds=? where id=?";

			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    error_log("Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error);
			}

			if (!$stmt->bind_param("siii",$name,$first_round,$num_rounds,$id)) {
			    error_log("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
			}

			if (!$stmt->execute()) {
			    error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
			}

			$this->mysqli->commit();




		}catch (exception $exception) {
		  $this->mysqli->rollback();
		  $this->mysqli->autocommit(TRUE);
		  return false;
		}finally{
		  isset($stmt) && $stmt->close();
		  $this->mysqli->autocommit(true);
            return true;
		}
	}

	function setRoundsCompetition($id,$rounds){
		$tempQuery="DELETE FROM competitions_in_rounds  WHERE id_competition=? ";

		try{
			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    error_log("Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error);
			}

			if (!$stmt->bind_param("i", $id)) {
			    error_log("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
			}

			if (!$stmt->execute()) {
			    error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
			}
			$round_competition=1;
			$tempQuery="INSERT INTO competitions_in_rounds (`id_competition`,`round`,`round_competition`) VALUES (?,?,?)";


			for($i=0;$i<sizeof($rounds);$i++){

				if(isset($rounds[$i])){

					if(!($stmt = $this->mysqli->prepare($tempQuery))) {
					    error_log("Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error);
					}

					if (!$stmt->bind_param("iii", $id,$rounds[$i],$round_competition)){
					    error_log("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
					}

					if (!$stmt->execute()) {
					    error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
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
			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    error_log("Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error);
			}

			if (!$stmt->bind_param("ii", $id_user,$id_competition)) {
			    error_log("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
			}

			if (!$stmt->execute()) {
			    error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
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
	
	function isUserInCompetitionGroup($id_user , $id_group){
		$tempQuery="SELECT * FROM round_robin  WHERE id_user=? and id_group=? ";

		try{
			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    error_log("Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error);
			}

			if (!$stmt->bind_param("ii", $id_user,$id_group)) {
			    error_log("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
			}

			if (!$stmt->execute()) {
			    error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
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
		$tempQuery="SELECT users_in_competitions.* ,users.* FROM users_in_competitions LEFT OUTER JOIN users ON users.id = users_in_competitions.id_user WHERE id_competition=? order by users.name_team ASC";

		try{
			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    error_log("Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error);
			}

			if (!$stmt->bind_param("i", $id_competition)) {
			    error_log("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
			}

			if (!$stmt->execute()) {
			    error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
			}

			$res=$stmt->get_result();
			$res->data_seek(0);

			$arr=array();


			while ($row = $res->fetch_assoc()) {
				$ret["id"]=$row['id_user'];
                $ret["name_team"] = $row["name_team"];
                $ret["username"] = $row["username"];
                $ret["name"] = $row["name"];
                $ret["surname"] = $row["surname"];


                $arr[] = $ret;
			}

			return $arr;

		}catch(exception $e) {
			echo "ex: ".$e;
			return null;

		}
	}
    
    function setUsersInCompetition($id_competition,$users){
        $deleteQuery="DELETE FROM `users_in_competitions` WHERE id_competition=".$id_competition;
        $tempQuery="REPLACE INTO `users_in_competitions` (`id_competition`,`id_user`) VALUES (?,?)";

		try{
			if(!($stmt = $this->mysqli->prepare($deleteQuery))) {
			    error_log("Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error);
			}

			if (!$stmt->execute()) {
			    error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
			}
            
            foreach($users as $id_user){
            
                if(!($stmt = $this->mysqli->prepare($tempQuery))) {
                    error_log("Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error);
                }

                if (!$stmt->bind_param("ii", $id_competition,$id_user)) {
                    error_log("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
                }

                if (!$stmt->execute()) {
                    error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
                }
                
            }

			return true;

		}catch(exception $e) {
			echo "errore insermento utenti in competizioni ".$e;
			return false;

		}
    }
    
    function getUsersInCompetitionGroup($id_group){
		$tempQuery="SELECT round_robin.* ,users.* FROM round_robin LEFT OUTER JOIN users ON users.id = round_robin.id_user WHERE id_group=?";

		try{
			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    error_log("Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error);
			}

			if (!$stmt->bind_param("i", $id_group)) {
			    error_log("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
			}

			if (!$stmt->execute()) {
			    error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
			}

			$res=$stmt->get_result();
			$res->data_seek(0);

			$arr=array();


			while ($row = $res->fetch_assoc()) {
				$ret["id"]=$row['id_user'];
                $ret["name_team"] = $row["name_team"];
                $ret["username"] = $row["username"];
                $ret["name"] = $row["name"];
                $ret["surname"] = $row["surname"];


                $arr[] = $ret;
			}

			return $arr;

		}catch(exception $e) {
			echo "ex: ".$e;
			return null;

		}
	}

	function getStandings($id_competition){

		$data_rounds=new ConnectDatabaseRounds($this->mysqli);
		$data_handicaps = new ConnectDatabaseHandicaps($this->mysqli);

		$rounds=$data_rounds->getRoundsByCompetition($id_competition);
		$results=array();
		
		$type = $this->getCompetition($id_competition)->getTypeCompetition();
		
		
		if($type=="CHAMPIONSHIP"){
			
			return $this->getStandingsChampionship($id_competition);
			
		}else if($type=="DIRECT"){
			
			return $this->getStandingsDirect($id_competition);
			
		}else{
			return null;
		}

	}
	
	private function getStandingsChampionship($id_competition){

		$data_rounds=new ConnectDatabaseRounds($this->mysqli);
		$data_handicaps = new ConnectDatabaseHandicaps($this->mysqli);

		$rounds=$data_rounds->getRoundsByCompetition($id_competition);
		$results=array();
		
		
		foreach($rounds as $round){
			$tempQuery="SELECT * FROM rounds_result  WHERE round=? ";
			

			try{
				if(!($stmt = $this->mysqli->prepare($tempQuery))) {
				    error_log("Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error);
				}

				if (!$stmt->bind_param("i", $round)) {
				    error_log("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
				}
				

				if (!$stmt->execute()) {
				    error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
				}

				$res=$stmt->get_result();
				$res->data_seek(0);

				$round_result=array();
				

				while ($row = $res->fetch_assoc()) {
					
					
					$id_user=$row['id_user'];

					$handicaps=$data_handicaps->getHandicapsRoundsByUserIdAndRound($id_user,$round);
					

					$result=$row['points'];
					$gol=$row['gol'];

					foreach($handicaps as $handicap){
						
						
						if(intval($handicap->getRound())==intval($round)){
							$round_handicap=$handicap->getPoints();
							$result=$result+$round_handicap;
							if($result>=66){
								$gol=floor(($result-66)/6)+1;
							}
						}
					}

					

					$round_result[$id_user]['points']=$result;
					$round_result[$id_user]['gol']=$gol;
					$round_result[$id_user]['user']=$row['id_user'];
				}

				$results[]=$round_result;


			}catch(exception $e) {
				echo "ex: ".$e;
				return true;

			}
		}
		
		
		
		$classifica=array();
		$users=$this->getUsersInCompetition($id_competition);
		

		foreach($users as $user){
						
			
			
			
			$gols=0;
			$points=0;
			foreach($results as $round){
				if(isset($round[intval($user["id"])])){
                    $res=$round[intval($user["id"])];
                    $gols+=$res['gol'];
                    $points+=$res['points'];
                }
			}
			
			$handicaps_competitions=$data_handicaps->getHandicapsCompetitionsByUserIdAndCompetition($user["id"],$id_competition);
					

			foreach($handicaps_competitions as $handicap){
					$points_handicap=$handicap->getPoints();
					$points=$points+$points_handicap;
			}
			
			

			
			
			
			$temp['id_user']=$user["id"];
			$temp['gol']=$gols;
			$temp['points']=$points;
			$classifica[]=$temp;
		}

		usort($classifica, function($a, $b) { // SORT DESC ONLY BY POINTS
			$diff=$b['points'] - $a['points'];
			
			if($diff>0){
				return true;
			}else{
				return false;
			}
		});
		
		return $classifica;

	}
	
	function getPhases($id_competition){
		
		
		$comp = $this->getCompetition($id_competition);
		
		if($comp!=null && $comp->getTypeCompetition()=="DIRECT"){
			
			
			$tempQuery="SELECT * FROM phases  WHERE id_competition=? ";
			

			try{
				if(!($stmt = $this->mysqli->prepare($tempQuery))) {
				    error_log("Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error);
				}

				if (!$stmt->bind_param("i", $id_competition)) {
				    error_log("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
				}
				

				if (!$stmt->execute()) {
				    error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
				}

				$res=$stmt->get_result();
				$res->data_seek(0);

				$phases = array();
				
				while ($row = $res->fetch_assoc()) {
					
					$id_phase = $row["id_phase"];
					$type = $row["type"];
					
					$groups = null;
										
					if($type == "ROUND_ROBIN" || $type == "ROUND_ROBIN_SEEDED"){
						 $groups = $this->getGroups($id_competition,$id_phase);
					}
					
					$phases[] = new Phase($id_phase , $row["id_competition"] , $row["name"] , $type , $row["settings"] , $groups);
					
				}
				
				return $phases;


			}catch(exception $e) {
				echo "ex: ".$e;
				return true;

			}
			
			
		}
	}
	
	function addPhase($name_phase,$id_competition,$id_phase,$type,$users_in_competition,$name_groups,$rounds){
		try{
						
			$tempQuery="INSERT INTO `phases` (`name`,`id_phase`,`id_competition`, `type`) VALUES (?,?,?,?)";

			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    error_log("Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error);
			}

			if (!$stmt->bind_param("siis", $name_phase,$id_phase,$id_competition,$type)) {
			    error_log("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
			}

			if (!$stmt->execute()) {
			    error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
			}
						
			if($type == "ROUND_ROBIN"){
				$groups = $this->generateGroups($id_phase,$id_competition,$users_in_competition,$name_groups,$rounds,false);
			}else if($type == "ROUND_ROBIN_SEEDED"){
				$groups = $this->generateGroups($id_phase,$id_competition,$users_in_competition,$name_groups,$rounds,true);
			}
			


		}catch(exception $e) {
			echo "\nERRORE INSERT COMPETITION: ".$e;
			return -1;
		}
	}
	
	private function generateGroups($id_phase,$id_competition,$users_in_competition,$name_groups,$rounds,$seeds){
		$num_groups = count($name_groups);
		$groups = array_fill(0, $num_groups, array("users" => array()));
		
		if($seeds == false){
			$num_users = count($users_in_competition);
			$gen_rounds=range(0,$num_users-1);
			
			shuffle($users_in_competition);
	
			$num_rounds = count($rounds);
			
			
			$ar = floor($num_rounds / ( ceil(intval($num_users) / $num_groups )  - 1 ));
						
			for($i=0;$i<$num_users;$i++){
				$key = $i % $num_groups;
				$groups[$key]["users"][] = $users_in_competition[$i];
			}
			
		}else{
			
			$num_users = 0;
			
			
			for($i = 0 ; $i<count($users_in_competition); $i++){
				$num_users = $num_users + count($users_in_competition[$i]);
				shuffle($users_in_competition[$i]);
			}
			
			
			$gen_rounds=range(0,$num_users-1);
			
			$num_rounds = count($rounds);
			
			$ar = floor($num_rounds / ( ceil(intval($num_users) / $num_groups )  - 1 ));
			
			for($k=0;$k<count($users_in_competition);$k++){
				for($i = 0 ; $i<count($users_in_competition[$k]) ; $i++){
					$groups[$i]["users"][] = $users_in_competition[$k][$i];
				}
			}
						
		}
		
		
		
		
		

		
		
		
		for($i=0 ; $i<$num_groups;$i++){
			$groups[$i]["name"] = $name_groups[$i];
		}
		

						
		foreach($groups as $group){

			try{
								
				$name_group = $group["name"];
			
				$tempQuery="INSERT INTO `groups` (`name`,`id_phase`,`id_competition`) VALUES (?,?,?)";
	
				if(!($stmt = $this->mysqli->prepare($tempQuery))) {
				    error_log("Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error);
				}
	
				if (!$stmt->bind_param("sii", $name_group,$id_phase,$id_competition)) {
				    error_log("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
				}
	
				if (!$stmt->execute()) {
				    error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
				}
				
				$id_group = $this->mysqli->insert_id;
								
				$giornate = $this->combinations($group["users"], 0);
								
				foreach($group["users"] as $us){
					$us = intval($us);
					
					$tempQuery="INSERT INTO `round_robin` (`id_group`,`id_phase`,`id_competition`,`id_user`) VALUES (?,?,?,?)";
	
					if(!($stmt = $this->mysqli->prepare($tempQuery))) {
					    error_log("Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error);
					}
		
					if (!$stmt->bind_param("iiii", $id_group,$id_phase,$id_competition,$us)) {
					    error_log("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
					}
		
					if (!$stmt->execute()) {
					    error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
					}
				}
				
				
				
				$cursor = 0;
				
				for($t = 0 ; $t < $ar ; $t++){
					
					
																				
					foreach($giornate as $giornata){
												
						$round_giornata = $rounds[$cursor];

						$cursor++;
						foreach($giornata as $partita){
														
							$id_one = $partita[0];
							$id_two = $partita[1];
							
							
														
							$tempQuery="INSERT INTO `matches` (`id_group`,`id_phase`,`id_competition`,`id_one`,`id_two`,`round`) VALUES (?,?,?,?,?,?)";
	
							if(!($stmt = $this->mysqli->prepare($tempQuery))) {
							    error_log("Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error);
							}
				
							if (!$stmt->bind_param("iiiiii", $id_group,$id_phase,$id_competition,$id_one,$id_two,$round_giornata)) {
							    error_log("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
							}
				
							if (!$stmt->execute()) {
							    error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
							}
							
							
						}

						
						
					}


					
				}
				

				
	
			}catch(exception $e) {
				error_log("\nERRORE INSERT GROUP: ".$e);
				return -1;
			}
		}	
					
		/*try{
			
			$tempQuery="INSERT INTO `phases` (`name`,`id_phase`,`id_competition`, `type`) VALUES (?,?,?,?)";

			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    error_log("Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error);
			}

			if (!$stmt->bind_param("siis", $name_phase,$id_phase,$id_competition,$type)) {
			    error_log("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
			}

			if (!$stmt->execute()) {
			    error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
			}
			
			$groups[] = $this->generateGroups($id_phase,$users_in_competition,$name_groups,$rounds);


		}catch(exception $e) {
			echo "\nERRORE INSERT COMPETITION: ".$e;
			return -1;
		}*/
	}
	
	private function combinations($arrSquadre) {
		
	    $numero_squadre = count($arrSquadre);
	    
	    // USIAMO ALGORITMO DI BERGER
	    
	    $calendario = array();
	    
	    if ($numero_squadre % 2 == 1) {
	    	    $arrSquadre[]=null;   // numero giocatori dispari? aggiungere un riposo (BYE)!
	    	    $numero_squadre++;
	    }
	    
	    $giornate = $numero_squadre - 1;
	    
	    /* crea gli array per le due liste in casa e fuori */
	    
	    for ($i = 0; $i < $numero_squadre /2; $i++){
	        
	        $casa[$i] = $arrSquadre[$i]; 
	        $trasferta[$i] = $arrSquadre[$numero_squadre - 1 - $i]; 
	
	    }
	 
	    for ($i = 0; $i < $giornate; $i++) {
	 
	        /* alterna le partite in casa e fuori */
	        if (($i % 2) == 0) 
	        {
	            for ($j = 0; $j < $numero_squadre /2 ; $j++)
	            {
	                $calendario[$i][] = array($trasferta[$j],$casa[$j]);
	            }
	        }
	        else 
	        {
	            for ($j = 0; $j < $numero_squadre /2 ; $j++) 
	            {
	                 $calendario[$i][] = array($casa[$j],$trasferta[$j]);
	            }
	                 
	        }
	 
	        // Ruota in gli elementi delle liste, tenendo fisso il primo elemento
	        // Salva l'elemento fisso
	        $pivot = $casa[0];
	 
	        /* sposta in avanti gli elementi di "trasferta" inserendo 
	           all'inizio l'elemento casa[1] e salva l'elemento uscente in "riporto" */
			if(count($casa)>1){
		        array_unshift($trasferta, $casa[1]);
		        $riporto = array_pop($trasferta);
		               
		 
		        /* sposta a sinistra gli elementi di "casa" inserendo all'ultimo 
		           posto l'elemento "riporto" */
		        array_shift($casa);
		        array_push($casa, $riporto);
		    }
	 
	        // ripristina l'elemento fisso
	        $casa[0] = $pivot ;
	    } 
	    
	    return $calendario;
		
	}
	
	
	function getGroups($id_competition,$id_phase){
		
		
		$comp = $this->getCompetition($id_competition);
		
		if($comp!=null && $comp->getTypeCompetition()=="DIRECT"){
			
			
			$tempQuery="SELECT * FROM groups  WHERE id_competition=?  and id_phase=?";
			

			try{
				if(!($stmt = $this->mysqli->prepare($tempQuery))) {
				    error_log("Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error);
				}

				if (!$stmt->bind_param("ii", $id_competition,$id_phase)) {
				    error_log("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
				}
				

				if (!$stmt->execute()) {
				    error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
				}

				$res=$stmt->get_result();
				$res->data_seek(0);

				$groups = array();
				
				while ($row = $res->fetch_assoc()) {
					$id_group = $row["id_group"] ;
					$groups[] = new Group($row["id_phase"] , $row["id_competition"] , $row["name"] , $id_group , $this->getMatchesByGroup($id_competition,$id_phase, $id_group));
					
				}
				
				return $groups;


			}catch(exception $e) {
				echo "ex: ".$e;
				return true;

			}
			
			
		}
	}
	
	function getGroup($id_group){
		
		
			
			
		$tempQuery="SELECT * FROM groups  WHERE id_group=?";
		

		try{
			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    error_log("Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error);
			}

			if (!$stmt->bind_param("i", $id_group)) {
			    error_log("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
			}
			

			if (!$stmt->execute()) {
			    error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
			}

			$res=$stmt->get_result();
			$res->data_seek(0);

			$groups = array();
			
			while ($row = $res->fetch_assoc()) {
				$id_group = $row["id_group"] ;
				return  new Group($row["id_phase"] , $row["id_competition"] , $row["name"] , $id_group , $this->getMatchesByGroup($row["id_competition"],$row["id_phase"], $id_group));
				
			}
			
			return $groups;


		}catch(exception $e) {
			echo "ex: ".$e;
			return true;

		}
			
			
		
	}
	
	function getMatches($id_competition,$id_phase){
		
		
		$comp = $this->getCompetition($id_competition);
		
		if($comp!=null && $comp->getTypeCompetition()=="DIRECT"){
			
			
			$tempQuery="SELECT * FROM matches WHERE id_competition=?  and id_phase=?";
			

			try{
				if(!($stmt = $this->mysqli->prepare($tempQuery))) {
				    error_log("Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error);
				}

				if (!$stmt->bind_param("ii", $id_competition,$id_phase)) {
				    error_log("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
				}
				

				if (!$stmt->execute()) {
				    error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
				}

				$res=$stmt->get_result();
				$res->data_seek(0);

				$matchess = array();
				
				while ($row = $res->fetch_assoc()) {
					
					$matches[] = new Match($row["id_phase"] , $row["id_match"] , $row["id_competition"] , $row["id_one"] ,$row["id_two"] , $row["round"] , $row["result"] , $row["id_group"]);
					
				}
				
				return $matches;


			}catch(exception $e) {
				echo "ex: ".$e;
				return true;

			}
			
			
		}
	}
	
	function getMatchesByGroup($id_competition,$id_phase , $id_group){
		
		
		$comp = $this->getCompetition($id_competition);
		
		
		if($comp!=null && $comp->getTypeCompetition()=="DIRECT"){
			
			
			$tempQuery="SELECT * FROM matches WHERE id_competition=?  and id_phase=? and id_group=?";
			

			try{
				if(!($stmt = $this->mysqli->prepare($tempQuery))) {
				    error_log("Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error);
				}

				if (!$stmt->bind_param("iii", $id_competition,$id_phase,$id_group)) {
				    error_log("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
				}
				

				if (!$stmt->execute()) {
				    error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
				}

				$res=$stmt->get_result();
				$res->data_seek(0);

				$matches = array();
				
				while ($row = $res->fetch_assoc()) {
					
					$matches[] = new Match($row["id_phase"] , $row["id_match"] , $row["id_competition"] , $row["id_one"] ,$row["id_two"] , $row["round"] , $row["result"] , $row["id_group"]);
					
				}
				
				return $matches;


			}catch(exception $e) {
				echo "ex: ".$e;
				return true;

			}
			
			
		}
	}
	
	function getMatchesByRound($round){
		
		
			
		
		$tempQuery="SELECT * FROM matches WHERE round=?";
		

		try{
			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    error_log("Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error);
			}

			if (!$stmt->bind_param("i", $round)) {
			    error_log("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
			}
			

			if (!$stmt->execute()) {
			    error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
			}

			$res=$stmt->get_result();
			$res->data_seek(0);

			$matchess = array();
			
			while ($row = $res->fetch_assoc()) {
				
				$matches[] = new Match($row["id_phase"] , $row["id_match"] , $row["id_competition"] , $row["id_one"] ,$row["id_two"] , $row["round"] , $row["result"] , $row["id_group"]);				
				
			}
			
			return $matches;


		}catch(exception $e) {
			echo "ex: ".$e;
			return true;

		}
			
			
	}
	
	function getMatch($id_match){
					
		$tempQuery="SELECT * FROM matches WHERE id_match=?";
		

		try{
			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    error_log("Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error);
			}

			if (!$stmt->bind_param("i", $id_match)) {
			    error_log("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
			}
			

			if (!$stmt->execute()) {
			    error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
			}

			$res=$stmt->get_result();
			$res->data_seek(0);

			
			while ($row = $res->fetch_assoc()) {
				
				return new Match($row["id_phase"] , $row["id_match"] , $row["id_competition"] , $row["id_one"] ,$row["id_two"] , $row["round"] , $row["result"] , $row["id_group"]);
				
			}
			

		}catch(exception $e) {
			echo "ex: ".$e;
			return true;

		}
		
			
	}
	
	
	private function getStandingsDirect($id_competition){

		$data_rounds=new ConnectDatabaseRounds($this->mysqli);
		$data_handicaps = new ConnectDatabaseHandicaps($this->mysqli);

		$rounds=$data_rounds->getRoundsByCompetition($id_competition);
		$results=array();
		
		$competition = $this->getCompetition($id_competition);
		
		
		
		foreach($rounds as $round){
			$tempQuery="SELECT * FROM rounds_result  WHERE round=? ";
			

			try{
				if(!($stmt = $this->mysqli->prepare($tempQuery))) {
				    error_log("Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error);
				}

				if (!$stmt->bind_param("i", $round)) {
				    error_log("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
				}
				

				if (!$stmt->execute()) {
				    error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
				}

				$res=$stmt->get_result();
				$res->data_seek(0);

				$round_result=array();
				

				while ($row = $res->fetch_assoc()) {
					
					
					$id_user=$row['id_user'];

					$handicaps=$data_handicaps->getHandicapsRoundsByUserIdAndRound($id_user,$round);
					

					$result=$row['points'];
					$gol=$row['gol'];

					foreach($handicaps as $handicap){
						
						
						if(intval($handicap->getRound())==intval($round)){
							$round_handicap=$handicap->getPoints();
							$result=$result+$round_handicap;
							if($result>=66){
								$gol=floor(($result-66)/6)+1;
							}
						}
					}

					

					$round_result[$id_user]['points']=$result;
					$round_result[$id_user]['gol']=$gol;
					$round_result[$id_user]['user']=$row['id_user'];
				}

				$results[]=$round_result;


			}catch(exception $e) {
				echo "ex: ".$e;
				return true;

			}
		}
		
		
		
		$classifica=array();
		$users=$this->getUsersInCompetition($id_competition);
		

		foreach($users as $user){
						
			
			
			
			$gols=0;
			$points=0;
			foreach($results as $round){
				if(isset($round[intval($user["id"])])){
                    $res=$round[intval($user["id"])];
                    $gols+=$res['gol'];
                    $points+=$res['points'];
                }
			}
			
			$handicaps_competitions=$data_handicaps->getHandicapsCompetitionsByUserIdAndCompetition($user["id"],$id_competition);
					

			foreach($handicaps_competitions as $handicap){
				
				if($handicap->getCompetition()!=null){
				
					if(intval($handicap->getCompetition()->getId())==intval($id_competition)){
						$points_handicap=$handicap->getPoints();
						$points=$points+$points_handicap;
					}
				}
			}
			
			$temp['id_user']=$user["id"];
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
	
	function getStandingsGroup($id_group){
		
		$data_handicaps = new ConnectDatabaseHandicaps($this->mysqli);
		$data_users = new ConnectDatabaseUsers($this->mysqli);

		$group = $this->getGroup($id_group);
		$users = $this->getUsersInCompetitionGroup($id_group);
		

		$matches = $group->getMatches();
				
		$results = array();
		
		$id_competition = $group->getIdCompetition();
		
		
		foreach($matches as $match){
			if($match->getResult() != NULL){
				$round_result = array();
									
				$result = $match->getResult();
				
				if($result=="1"){
					$round_result[$match->getIdOne()]['points'] = 3;
					$round_result[$match->getIdTwo()]['points'] = 0;
					
					$round_result[$match->getIdOne()]['user'] = $match->getIdOne();
					$round_result[$match->getIdTwo()]['user'] = $match->getIdTwo();
				}else if($result=="X"){
					$round_result[$match->getIdOne()]['points'] = 1;
					$round_result[$match->getIdTwo()]['points'] = 1;
					
					$round_result[$match->getIdOne()]['user'] = $match->getIdOne();
					$round_result[$match->getIdTwo()]['user'] = $match->getIdTwo();
				}else if($result=="2"){
					$round_result[$match->getIdOne()]['points'] = 0;
					$round_result[$match->getIdTwo()]['points'] = 3;
					
					$round_result[$match->getIdOne()]['user'] = $match->getIdOne();
					$round_result[$match->getIdTwo()]['user'] = $match->getIdTwo();
				}
				
				$results[]=$round_result;
			}
		}
		
		
		
		$classifica=array();
		
		

		foreach($users as $user){
			
			$gols=0;
			$points=0;
			foreach($results as $round){
				if(isset($round[intval($user["id"])])){
                    $res=$round[intval($user["id"])];
                    $points+=$res['points'];
                }
			}
			
			$handicaps_competitions=$data_handicaps->getHandicapsCompetitionsByUserIdAndCompetition($user["id"],$id_competition);
					

			foreach($handicaps_competitions as $handicap){
				
				if($handicap->getCompetition()!=null){
				
					if(intval($handicap->getCompetition()->getId())==intval($id_competition)){
						$points_handicap=$handicap->getPoints();
						$points=$points+$points_handicap;
					}
				}
			}
			
			$temp['id_user']=intval($user["id"]);
			$temp["team_info"] = $data_users->getUserById(intval($user["id"]))->mapBasic();
			$temp['points']=$points;
			$classifica[]=$temp;
		}
		

		usort($classifica, function($a, $b) { // SORT DESC ONLY BY POINTS
			$diff=$b['points'] - $a['points'];
		    return $diff;
		});
		

		return $classifica;

	}
    
}
    
    ?>