<?php 

function dumpSingoliToList($name_str,$team_str){
		$players=new PlayersList();
		$arr=array();

		$tempQuery="SELECT * FROM (SELECT id, MAX(timestamp) AS timestamp FROM players GROUP BY id ) l JOIN players b
   					ON b.id = l.id AND b.timestamp = l.timestamp GROUP BY b.timestamp, b.id";
		$another=0;
        if($name_str!=null || $team_str!=null) $tempQuery=$tempQuery."where ";

        if($name_str!=null) {
            $tempQuery=$tempQuery."name LIKE \'".$name_str."\'";
            $another++;
        }
h
        if($team_str!=null){
            if($another!=0)  $tempQuery=$tempQuery." and ";
            $tempQuery=$tempQuery."team LIKE \'".$team_str."\'";

        }

        $tempQuery=$tempQuery." order by name";


		$res=$mysqli->query($tempQuery);

		$res->data_seek(0);
		while ($row = $res->fetch_assoc()) {


		    $id=$row['id'];
		    $role=$row['role'];
		    $name=$row['name'];
		    $team=$row['team'];
		    $value=$row['value'];
		    $first_value=$row['first_value'];
		    $diff=$row['diff'];

		    $item=new Player($id,$name,$team,$role,$value,$first_value,$diff);

		    $arr[$id]=$item;
		}
		return $arr;
	}


function updatePlayers(PlayersList $players){
		foreach($players as $player){
			$query="REPLACE INTO `players` (`id`,`role`,`name`,`team`,`value`,`first_value`,`diff`) VALUES(?,?,?,?,?,?,?)";


			try{
				if (!($stmt = $mysqli->prepare($query))) {
				    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
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
		$query="select * from `players` where id=? ORDER BY timestamp DESC LIMIT 1";


			try{
				if (!($stmt = $mysqli->prepare($query))) {
				    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
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
					$name=$row['name'];
					$team=$row['team'];
					$role=$row['role'];
					$value=$row['value'];
					$first_value=$row['first_value'];
					$diff=$row['diff'];

					$stats=dumpStats($id);
					$pla=new Player($id,$name,$team,$role,$value,$first_value,$diff,$stats);
					return $pla;
				}

			}catch(exception $e) {
				echo "\nERRORE DUMP PLAYER BY ID: ".$e;
				return false;
			}

			return null;
	}

	function dumpPlayerByName($name){
		$query="select * from `players` where name=? ORDER BY timestamp DESC LIMIT 1";


		try{
			if (!($stmt = $mysqli->prepare($query))) {
			    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
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
				$stats=dumpStats($id);
				$pla=new Player($id,$name,$team,$role,$value,$first_value,$diff,$stats);
				return $pla;
			}

		}catch(exception $e) {
			echo "\nERRORE DUMP PLAYER BY NAME: ".$e;
			return false;
		}

		return null;
	}



	function dumpStats($id){
		$query="select * from `stats` where id_player=?";
		$stats=array();

		try{
			if (!($stmt = $mysqli->prepare($query))) {
			    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
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
				$query="select * from `stats` where id_player=? and round=?";

				if (!($st2 = $mysqli->prepare($query))) {
				    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
				}

				if (!$st2->bind_param("ii", $id,$round)) {
				    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
				}

				if (!$st2->execute()) {
				    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
				}

				$res2=$st2->get_result();
				$res2->data_seek(0);

				$row_num = $res2->fetch_array(MYSQLI_NUM);
				for($i=0;$i<count($fields);$i++){

					if(isset($fields[$i]) && isset($row_num[$i])){
						$stats_coll[$fields[$i]]=new Statistic($fields[$i],$row_num[$i]);
					}
				}

				$stats[$round]=$stats_coll;


			}

		}catch(exception $e) {
			echo "\nERRORE DUMP STATS: ".$e;
		}


		return $stats;
	}

	function dumpStatsByRound($id,$round){
		$query="select * from `stats` where id_player=? and round=?";
		$stats=array();

		try{
			if (!($stmt = $mysqli->prepare($query))) {
			    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
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
		$tempQuery="SELECT * FROM players  WHERE id=? ORDER BY timestamp ASC ";

		try{
			if(!($stmt = $mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
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
				$datetemp = date ("Y-m-d H:i:s", strtotime(str_replace('-','/', $row['timestamp'])));
				$date=new DateTime($datetemp);
				$value=$row['value'];

				$arr[]=array('date' => $date, 'value' => $value,'round'=>$it);
				$it++;
			}

			return $arr;

		}catch(exception $e) {
			echo "ex: ".$e;
			return false;

		}
	}





?>
