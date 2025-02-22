<?php 
class ConnectDatabasePlayers extends ConnectDatabase{
    
    public function __call($method_name , $parameter){
		if($method_name == "dumpPlayer"){ //Function overloading logic for function name overlodedFunction
			$count = count($parameter);
			if(is_string($parameter[0])) return $this->dumpPlayerByName($parameter[0]);
			else if(is_int($parameter[0])) return $this->dumpPlayerById($parameter[0]);
			else throw new exception("Function $method_name with type=$parameter , does not exists ");
		}
	}

    
    
    function dumpSingoliToList($name_str,$team_str){
		$players=new PlayersList();
		$arr=array();
		
		$db_rounds = new ConnectDatabaseRounds($this->mysqli);
		
		$last_round = $db_rounds->getLastQuoteRound();

		$tempQuery="SELECT *  FROM quote JOIN players ON players.id = quote.id WHERE quote.round = ( SELECT MAX(round) FROM quote )";
		$another=0;
        if($name_str!=null || $team_str!=null) $tempQuery=$tempQuery."and ";

        if($name_str!=null) {
            $tempQuery=$tempQuery."players.name LIKE '".$name_str."'";
            $another++;
        }

        if($team_str!=null){
            if($another!=0)  $tempQuery=$tempQuery." and ";
            $tempQuery=$tempQuery."players.team LIKE '".$team_str."'";

        }

        $tempQuery=$tempQuery." order by players.role DESC, players.name ASC";
        

		$res=$this->mysqli->query($tempQuery);

		$res->data_seek(0);
		while ($row = $res->fetch_assoc()) {


		    $id=$row['id'];
		    $role=$row['role'];
		    $name=$row['name'];
		    $team=$row['team'];
		    $value=$row['value'];
		    $first_value=$row['first_value'];
		    $diff=$row['diff'];
		    
		    $gone = false;
		    
		    if($last_round>$row["round"]){
			    $gone = true;
		    }
		    
		    

		    $item=new Player($id,$name,$team,$role,$value,$first_value,$diff,null,$gone);

		    $arr[$id]=$item;
		}
				
		return $arr;
	}
	
function getSerieaTeams(){
		$arr=array();
		
		

		$tempQuery="SELECT team FROM `players` GROUP BY team";
		


		$res=$this->mysqli->query($tempQuery);

		$res->data_seek(0);
		while ($row = $res->fetch_assoc()) {


		    $arr[] = $row["team"];
		}
				
		return $arr;
	}


function updatePlayers(PlayersList $players){
		foreach($players as $player){
			$query="REPLACE INTO `players` (`id`,`role`,`name`,`team`,`value`,`first_value`,`diff`) VALUES(?,?,?,?,?,?,?)";


			try{
				if (!($stmt = $this->mysqli->prepare($query))) {
				    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
				}

				if (!$stmt->bind_param("isssiii", $player->getId(),$player->getRole(),$player->getName(),$player->getTeam(),$player->getValue(),$player->getFirstValue(),$player->getDiff())) {
				    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
				}

				if (!$stmt->execute()) {
				    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
				}
			}catch(exception $e) {
				echo "ex: ".$e;
				return false;
			}
		}

		return true;
	}


	function dumpPlayerById($id){
		$query="SELECT *  FROM quote JOIN players ON players.id = quote.id WHERE quote.round = ( SELECT MAX(round) FROM quote WHERE quote.id=? ) and quote.id=?";
		
		$db_rounds = new ConnectDatabaseRounds($this->mysqli);
		
		$last_round = $db_rounds->getLastQuoteRound();
		
			try{
				if (!($stmt = $this->mysqli->prepare($query))) {
				    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
				}

				if (!$stmt->bind_param("ii", $id, $id)) {
				    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
				}

				if (!$stmt->execute()) {
				    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
				}

				$res=$stmt->get_result();
				$res->data_seek(0);
				while ($row = $res->fetch_assoc()) {
					$name=$row['name'];
					$team=$row['team'];
					$role=$row['role'];
					$value=$row['value'];
					$first_value=$row['first_value'];
					$diff=$row['diff'];
					$stats=null;
					
					$gone = false;
		    
				    if($last_round>$row["round"]){
					    $gone = true;
				    }
					
					$pla=new Player($id,$name,$team,$role,$value,$first_value,$diff,$stats , $gone);
					return $pla;
				}

			}catch(exception $e) {
				echo "\nERRORE DUMP PLAYER BY ID: ".$e;
				return null;
			}

			return null;
	}
	
	function dumpPlayerByIdNoStats($id){
		$query="SELECT *  FROM quote JOIN players ON players.id = quote.id WHERE quote.round = ( SELECT MAX(round) FROM quote WHERE quote.id=? ) and quote.id=?";
		
		$db_rounds = new ConnectDatabaseRounds($this->mysqli);
		
		$last_round = $db_rounds->getLastQuoteRound();
		
			try{
				if (!($stmt = $this->mysqli->prepare($query))) {
				    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
				}

				if (!$stmt->bind_param("ii", $id, $id)) {
				    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
				}

				if (!$stmt->execute()) {
				    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
				}

				$res=$stmt->get_result();
				$res->data_seek(0);
				while ($row = $res->fetch_assoc()) {
					$name=$row['name'];
					$team=$row['team'];
					$role=$row['role'];
					$value=$row['value'];
					$first_value=$row['first_value'];
					$diff=$row['diff'];
					$stats=null;
					
					$gone = false;
		    
				    if($last_round>$row["round"]){
					    $gone = true;
				    }
					
					$pla=new Player($id,$name,$team,$role,$value,$first_value,$diff,$stats , $gone);
					return $pla;
				}

			}catch(exception $e) {
				echo "\nERRORE DUMP PLAYER BY ID: ".$e;
				return null;
			}

			return null;
	}

	function dumpPlayerByName($name){
		$query="SELECT *  FROM quote JOIN players ON players.id = quote.id WHERE quote.round = ( SELECT MAX(round) FROM quote  ) and players.name=?";
		
		$db_rounds = new ConnectDatabaseRounds($this->mysqli);
		
		$last_round = $db_rounds->getLastQuoteRound();

		try{
			if (!($stmt = $this->mysqli->prepare($query))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt->bind_param("s", $name)) {
			    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			$res=$stmt->get_result();
			$res->data_seek(0);
			while ($row = $res->fetch_assoc()) {
				$id=$row['id'];
				$team=$row['team'];
				$role=$row['role'];
				$value=$row['value'];
				$first_value=$row['first_value'];
				$diff=$row['diff'];
				$stats=null;
				
				$gone = false;
		    
			    if($last_round>$row["round"]){
				    $gone = true;
			    }
				
				$pla=new Player($id,$name,$team,$role,$value,$first_value,$diff,$stats, $gone);
				return $pla;
			}

		}catch(exception $e) {
			echo "\nERRORE DUMP PLAYER BY NAME: ".$e;
			return false;
		}

		return null;
	}



	function dumpStats($id){
		$query="select * from `stats` where id_player=? order by round ASC";
		$stats=array();
				
		try{
			if (!($stmt = $this->mysqli->prepare($query))) {
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
			while ($row = $res->fetch_assoc()) {
				$round=$row['round'];

				$stats_coll=new StatisticsCollection();
				$stats_coll->setRound($round);
				$fields = array_keys($row);
				
				foreach($fields as $key){
					if(isset($row[$key])){
						$stats_coll[$key] = new Statistic($key,$row[$key]);
					}
				}
				

				$stats[$round]=$stats_coll;


			}

		}catch(exception $e) {
			error_log("\nERRORE DUMP STATS: ".$e);
		}


		return $stats;
	}

	function dumpStatsByRound($id,$round){
		$query="select * from `stats` where id_player=? and round=?";
		$stats=array();

		try{
			if (!($stmt = $this->mysqli->prepare($query))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt->bind_param("ii", $id,$round)) {
			    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			$stats_coll=new StatisticsCollection();
			$stats_coll->setRound($round);

			$res=$stmt->get_result();
			$res->data_seek(0);
			$t= $res->fetch_assoc();
			if($t!=null){
				$fields = array_keys($t);

				if(count($t)!=0){
					for($i=2;$i<count($fields);$i++){
						$name_field=$fields[$i];
						if(isset($fields[$i]) && isset($t[$name_field])){
							$stats_coll[$name_field]=new Statistic($name_field,$t[$name_field]);
						}
					}


					return $stats_coll;
				}else{
					return null;
				}
			}else{
				return null;
			}




		}catch(exception $e) {
			echo "\nERRORE DUMP STATS: ".$e;
			return null;
		}


	}


function getValuesOfPlayer($id_player){
		$tempQuery="SELECT *  FROM quote JOIN players ON players.id = quote.id WHERE players.id=? ORDER BY quote.round ASC ";

		try{
			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt->bind_param("i", $id_player)) {
			    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			$res=$stmt->get_result();
			$res->data_seek(0);

			$arr=array();

			$it=1;
			while ($row = $res->fetch_assoc()) {
				$value=$row['value'];

				$arr[]=array('date' => $row['round'], 'value' => $value,'round'=>$it);
				$it++;
			}

			return $arr;

		}catch(exception $e) {
			echo "ex: ".$e;
			return false;

		}
	}

}



?>
