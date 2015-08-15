<?php
class ConnectDatabaseCompetitions extends ConnectDatabase {
    
    function getTeamsByRoundAndCompetition($round,$id_competition,$players){
		$arr=array();
		try{
			$tempUser="SELECT users.* FROM users LEFT OUTER JOIN users_in_competitions ON users_in_competitions.id_user=users.id WHERE users_in_competitions.id_competition=?";
			$tempQuery="SELECT teams.* , users.username FROM teams LEFT OUTER JOIN users ON users.id=teams.id_user WHERE teams.id_user=? ";

			if(!($stmt = $this->mysqli->prepare($tempUser))) {
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
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
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
				$ret[]=new Competition($id_competition,$name,$row['first_round'],$row['num_rounds'],$row['type']);
			}

			return $ret;

		}catch(exception $e) {
			echo "ex: ".$e;
			return false;

		}
	}

	function createCompetition($name,$first_round,$num_rounds,$type){
		
		error_log($type);
		
		try{
			$tempQuery="INSERT INTO `competitions` (`name`,`first_round`,`num_rounds` , `type`) VALUES (?,?,?,?)";

			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt->bind_param("siis", $name,$first_round,$num_rounds,$type)) {
			    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			return $this->mysqli->insert_id;

		}catch(exception $e) {
			echo "\nERRORE INSERT COMPETITION: ".$e;
			return -1;
		}
	}

	function deleteCompetition($id){
		try{
			$tempQuery="DELETE FROM `competitions` WHERE id=? ";

			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt->bind_param("i", $id)) {
			    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			$tempQuery="DELETE FROM competitions_in_rounds  WHERE id_competition=? ";

			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt->bind_param("i", $id)) {
			    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}
            
            $tempQuery="DELETE FROM users_in_competitions  WHERE id_competition=? ";

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
			echo "\nERRORE DELETE COMPETITION: ".$e;
			return false;
		}
	}

	function editCompetition($id,$name,$first_round,$num_rounds){
		
		
		try{

			is_num($first_round);
			is_num($num_rounds);
			
			error_log($id);
			error_log($name);

			$this->mysqli->autocommit(FALSE);

			$tempQuery="UPDATE `competitions` SET name=? , first_round=? , num_rounds=? where id=?";

			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt->bind_param("siii",$name,$first_round,$num_rounds,$id)) {
			    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
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
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
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

					if(!($stmt = $this->mysqli->prepare($tempQuery))) {
					    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
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
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt->bind_param("ii", $id_user,$id_group)) {
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
		$tempQuery="SELECT users_in_competitions.* ,users.* FROM users_in_competitions LEFT OUTER JOIN users ON users.id = users_in_competitions.id_user WHERE id_competition=?";

		try{
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
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}
            
            foreach($users as $id_user){
            
                if(!($stmt = $this->mysqli->prepare($tempQuery))) {
                    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
                }

                if (!$stmt->bind_param("ii", $id_competition,$id_user)) {
                    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
                }

                if (!$stmt->execute()) {
                    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
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
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt->bind_param("i", $id_group)) {
			    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
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
				    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
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

				$phases = array();
				
				while ($row = $res->fetch_assoc()) {
					
					$id_phase = $row["id_phase"];
					$type = $row["type"];
					
					$groups = null;
					
					if($type == "ROUND_ROBIN"){
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
	
	function getGroups($id_competition,$id_phase){
		
		
		$comp = $this->getCompetition($id_competition);
		
		if($comp!=null && $comp->getTypeCompetition()=="DIRECT"){
			
			
			$tempQuery="SELECT * FROM groups  WHERE id_competition=?  and id_phase=?";
			

			try{
				if(!($stmt = $this->mysqli->prepare($tempQuery))) {
				    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
				}

				if (!$stmt->bind_param("ii", $id_competition,$id_phase)) {
				    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
				}
				

				if (!$stmt->execute()) {
				    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
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
	
	function getMatches($id_competition,$id_phase){
		
		
		$comp = $this->getCompetition($id_competition);
		
		if($comp!=null && $comp->getTypeCompetition()=="DIRECT"){
			
			
			$tempQuery="SELECT * FROM matches WHERE id_competition=?  and id_phase=?";
			

			try{
				if(!($stmt = $this->mysqli->prepare($tempQuery))) {
				    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
				}

				if (!$stmt->bind_param("ii", $id_competition,$id_phase)) {
				    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
				}
				

				if (!$stmt->execute()) {
				    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
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
		
		error_log($id_competition);
		error_log($id_phase);
		error_log($id_group);
		
		
		$comp = $this->getCompetition($id_competition);
		
		$users = $this->getUsersInCompetitionGroup($id_group);
		
		
		if($comp!=null && $comp->getTypeCompetition()=="DIRECT" && $users != null){
			
			
			$tempQuery="SELECT * FROM matches WHERE id_competition=?  and id_phase=?";
			

			try{
				if(!($stmt = $this->mysqli->prepare($tempQuery))) {
				    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
				}

				if (!$stmt->bind_param("ii", $id_competition,$id_phase)) {
				    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
				}
				

				if (!$stmt->execute()) {
				    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
				}

				$res=$stmt->get_result();
				$res->data_seek(0);

				$matchess = array();
				
				while ($row = $res->fetch_assoc()) {
					
					foreach($users as $user){
						if($user["id"] == $row["id_one"] || $user["id"] == $row["id_two"]){
							$matches[] = new Match($row["id_phase"] , $row["id_match"] , $row["id_competition"] , $row["id_one"] ,$row["id_two"] , $row["round"] , $row["result"] , $row["id_group"]);
							break;
						}
					}
					
					
				}
				
				return $matches;


			}catch(exception $e) {
				echo "ex: ".$e;
				return true;

			}
			
			
		}
	}
	
	function getMatch($id_match){
					
		$tempQuery="SELECT * FROM matches WHERE id_match=?";
		

		try{
			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt->bind_param("i", $id_match)) {
			    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}
			

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
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
				    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
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
    
}
    
    ?>