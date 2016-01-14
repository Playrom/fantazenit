<?php

class ConnectDatabaseRounds extends ConnectDatabase{
    function updateStats($stats_coll,$round){
		try{
			$tempQuery="DELETE from `stats` where round=?";

			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt->bind_param("i", $round)) {
			    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			$n=0;
			$keys=array();
			foreach(array_keys($stats_coll) as $key){
				$keys[]=$key;
			}

			foreach($stats_coll as $stats){
				$i=0;
				$tempQuery="SHOW COLUMNS FROM stats";
				$res=$this->mysqli->query($tempQuery);
				$stats_array=array();
				$fields=array();

				$res->data_seek(0);

				while ($row = $res->fetch_assoc()) {
					$fields[]=$row['Field'];
					$stats_array=$stats[$fields[$i]];
					$i++;
				}

				$id=$keys[$n];

				$tempQuery="INSERT INTO `stats` (`id_player`,`round`,";

	            for($k=2;$k<$i;$k++){
	                if(($i-$k)>=2) $tempQuery=$tempQuery."`".$fields[$k]."`,";
	                else $tempQuery=$tempQuery."`".$fields[$k]."`) VALUES (\'".$id."\',\'".$round."\',";
	            }

	            for($k=2;$k<$i;$k++){
	                if(($i-$k)>=2) {
	                    $sta=$stats_array[$k];
	                    $str=null;
	                    if($sta!=null) $str=strval($sta); /// Cast
	                    $tempQuery=$tempQuery."\'".$str."\',";
	                }else {
	                    $sta=$stats_array[$k];
	                    $str=null;
	                    if($sta!=null) $str=strval($sta); //CAST
	                    $tempQuery=$tempQuery."\'".$str."\')";
	                }
	            }


	            $new_res=$this->mysqli->query($tempQuery);

			}

			return true;

		}catch(exception $e) {
			echo "\nERRORE UPDATE STATS: ".$e;
			return false;
		}
	}

    function insertTeam($id_user,$ids,$reserves,$round,$tactic,$recovered=false){
	    
	    
    	$data_players=new ConnectDatabasePlayers($this->mysqli);

		try{
			
			
			$players=$data_players->dumpSingoliToList(null,null);
			

			$query="DELETE from `teams` where id_user=? and round=?;";

			if (!($stmt = $this->mysqli->prepare($query))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt->bind_param("ii", $id_user,$round)) {
			    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}
                        
			foreach($ids as $id){
				
				$prepQuery="INSERT INTO `teams` ( `id_user`, `id_player`, `round` , `position`) VALUES (?,?,?,?);";

				if(!($stmt = $this->mysqli->prepare($prepQuery))) {
				    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
				}

				$position = $id["position"];
				$zero=0;
				
				$id_pla = $id["id"];

				if (!$stmt->bind_param("iiii", $id_user,$id_pla,$round,$position)) {
				    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
				}

				if (!$stmt->execute()) {
				    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
				}
			}

			foreach($reserves as $id){
				$prepQuery="INSERT INTO `teams` ( `id_user`, `id_player`, `round` , `position`) VALUES (?,?,?,?);";

				if (!($stmt = $this->mysqli->prepare($prepQuery))) {
				    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
				}

				$id_pla = $id["id"];
				$position = $id["position"];

				$uno=1;
				

				if (!$stmt->bind_param("iiii", $id_user,$id_pla,$round,$position)) {
				    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
				}

				if (!$stmt->execute()) {
				    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
				}
			}
			

			$tacticQuery="REPLACE INTO tactics (`id_user`,`tactic`,`round`,`recovered`) VALUES (?,?,?,?);";
			
			$val = 0;
			
			if($recovered){
				$val = 1;
			}

			if(!($stmt = $this->mysqli->prepare($tacticQuery))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt->bind_param("isii", $id_user,$tactic,$round,$val)) {
			    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			return true;

		}catch(exception $e) {
			echo "\nERRORE INSERIMENTO FORMAZIONE: ".$e;
			return false;
		}
	}
	
	function deleteTeam($id_user,$round){
		$query="DELETE from `teams` where id_user=? and round=?;";
		$query2 =  "DELETE from `tactics` where id_user=? and round=?;";

		try{
		
			if (!($stmt = $this->mysqli->prepare($query))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}
			
			if (!$stmt->bind_param("ii", $id_user,$round)) {
			    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}
	
			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}
			
			if (!($stmt = $this->mysqli->prepare($query2))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}
			
			if (!$stmt->bind_param("ii", $id_user,$round)) {
			    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}
	
			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}
			
			return true;
			
		}catch(exception $e) {
			echo "\nERRORE INSERIMENTO FORMAZIONE: ".$e;
			return false;
		}
	}
    
    function getDateLastChange($id_user){
        
		try{
			$tempQuery="Select *  from `transfers` where id_user=? order by date DESC LIMIT 1";

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

			while ($row = $res->fetch_assoc()) {
				$datetemp = date ("Y-m-d H:i:s", strtotime(str_replace('-','/', $row['date'])));
				return new DateTime($datetemp);

			}

			return null;


		}catch(exception $e) {
			echo "\nERRORE DUMP TRANSFERS: ".$e;
			return null;
		}
    }
    
    function isValidFormation($id_user,$round){
	    
	    $conf=$this->dumpConfig();
		$current_round=$conf['current_round'];
        
        try{
	        
	        if($round==$current_round){
		        
		        $tempQuery="SELECT * from `tactics` where id_user=? order by round DESC;";

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
				
				
				while ($row = $res->fetch_assoc()) {
					
					
	                
	                $datetemp = date ("Y-m-d H:i:s", strtotime(str_replace('-','/', $row['date'])));
					$date_tactic=new DateTime($datetemp);
						
					$date_last_change=$this->getDateLastChange($id_user);
	                
	                if($date_last_change!=null){
	                    $stamp_tactic=$date_tactic->getTimestamp();
	                    $stamp_change=$date_last_change->getTimestamp();
	
	                    $diff=$stamp_tactic-$stamp_change;
	                                        
	                    
	                    if($diff<=0){
	                        return false;
	                    }else{
	                        return true;
	                    }
	                }else{
		                return true;
	                }
				}
				
				return true;
				
				
	        }else{
		        return true;
	        }
	        
            
			
			


		}catch(exception $e) {
			echo "\nERRORE CHECK VALIDITA FORMAZIONE: ".$e;
			return false;
		}
    }
    
    function isValidFormationOfAnotherRound($id_user,$round,$formation_round){
	    
	    $conf=$this->dumpConfig();
		$current_round=$conf['current_round'];
        
        try{
	        
	        
		        
		        $tempQuery="SELECT * from `tactics` where id_user=? and round=?;";

				if(!($stmt = $this->mysqli->prepare($tempQuery))) {
				    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
				}
	
				if (!$stmt->bind_param("ii", $id_user,$formation_round)) {
				    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
				}
	
				if (!$stmt->execute()) {
				    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
				}
	
				$res=$stmt->get_result();
				$res->data_seek(0);
				
				
				while ($row = $res->fetch_assoc()) {
					
					
					
					
	                
	                $datetemp = date ("Y-m-d H:i:s", strtotime(str_replace('-','/', $row['date'])));
					$date_tactic=new DateTime($datetemp);
						
					$date_last_change=$this->getDateLastChange($id_user);
	                
	                if($date_last_change!=null){
	                    $stamp_tactic=$date_tactic->getTimestamp();
	                    $stamp_change=$date_last_change->getTimestamp();
	
	                    $diff=$stamp_tactic-$stamp_change;
	                
	               
	                    
	                    if($diff<=0){
	                        return false;
	                    }else{
	                        return true;
	                    }
	                }else{
		                return true;
	                }
				}
				
				return true;
				
				
	        
	        
            
			
			


		}catch(exception $e) {
			echo "\nERRORE CHECK VALIDITA FORMAZIONE: ".$e;
			return false;
		}
    }
    
    function getTeam($id_user,$round){

    	$data_players=new ConnectDatabasePlayers($this->mysqli);

		try{

			$tempQuery="SELECT * from `teams` where id_user=? and round=? order by position;";

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

			$players_team=null;
			$enter=true;
			
			while ($row = $res->fetch_assoc()) {
				if($enter){
					$players_team=new TeamPlayerList();
					$enter=false;
				}
				

				$id=$row['id_player'];
				$pos=$row['position'];
                $pla=$data_players->dumpPlayerById($id);
                $stats = $data_players->dumpStatsByRound($id,$round);
                

				$players_team[]=new TeamPlayerRound($pla,$pos,$stats);
			}


			////////////////////

			$tempQuery="SELECT * from `tactics` where id_user=? and round=?;";

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

			$dif=0;
			$cen=0;
			$att=0;
			
			$recovered = false;
			$modificatore = false;

			while ($row = $res->fetch_assoc()) {
				$tactic=$row['tactic'];
				$dif=$tactic[0];
				$cen=$tactic[1];
				$att=$tactic[2];

                if($this->isValidFormation($id_user,$round)){

                }else{
                    $players_team=null;
                }
                
                $recovered = $row['recovered'];
                $modificatore = $row['modificatore'];

			}


			$team=new Team($id_user,$round,$dif,$cen,$att,$players_team,$recovered,$modificatore);

			return $team;


		}catch(exception $e) {
			echo "\nERRORE DUMP FORMAZIONE: ".$e;
			return null;
		}
	}

    function getTactic($user,$round){
		try{
			$tempQuery="Select * from `tactics` where id_user=? and round=?";

			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt->bind_param("ii", $user->getId(),$round)) {
			    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			$res=$stmt->get_result();
			$res->data_seek(0);

			while ($row = $res->fetch_assoc()) {
				return $row['tactic'];
			}

			return null;

		}catch(exception $e) {
			echo "\nERRORE GET TACTIC: ".$e;
			return null;
		}
	}
    
    function closeRound($round){
		try{
			
			$db_competitions = new ConnectDatabaseCompetitions($this->mysqli);
			$db_users = new ConnectDatabaseUsers($this->mysqli);
			
			$settings=$this->dumpConfig();

			$available=$settings['available-round'];

			$rounds=explode(";",$available);

			$mod=strval($round);
			$nuova=null;
			$first=true;

			foreach($rounds as $temp){
				if($temp!=$mod) {
					if($first) {
						$nuova=$temp;
						$first=false;
					}else{
						$nuova=$nuova.";".$temp;
					}
				}
			}
			
			$this->calcRound($round);

			$this->calcRoundUser($round);
            
            $this->setLastRound($round);

			$tempQuery="UPDATE `settings` SET value=? where name='available-round' ";

			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt->bind_param("s", $nuova)) {
			    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}


			$available=$settings['already-calc'];

			$rounds=explode(";",$available);

			$mod=strval($round);
			$nuova=null;

			$first=true;

			if($available!=null){
				$mod=strval($round);
				$nuova=null;

				$first=true;

				foreach($rounds as $temp){
					if($temp!=$mod) {
						if($first) {
							$nuova=$temp;
							$first=false;
						}else{
							$nuova=$nuova.";".$temp;
						}
					}
				}
				$nuova=$nuova.";".$round;

			}else{
				$nuova=$round;
			}

			$tempQuery="UPDATE `settings` SET value=? where name='already-calc' ";

			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt->bind_param("s", $nuova)) {
			    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}
									
			$standings = $this->getRoundStandings($settings["default_competition"],$round);
			
			$standing = $standings[0];
										
			$id_user = $standing["id_user"];
		
			$points_lead = $standing["points"];
			
			$competition_standings = $db_competitions->getStandings($settings["default_competition"]);
			
			
			$standings_by_user = array();
			
			for($i=0 ; $i<count($competition_standings) ; $i++){
			    $temp_user = $db_users->getUserById(intval($competition_standings[$i]["id_user"]));
		        $standings_by_user[$temp_user->getId()] = $i+1; 
		    }
		    			
			$position = $standings_by_user[$id_user];
			
			$tempQuery="REPLACE INTO `recaps` (`winner`,`round`,`points`,`position`) VALUES(?,?,?,?) ";

			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt->bind_param("iidi", $id_user,$round,$points_lead,$position)) {
			    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}
		


		}catch(exception $e) {
			echo "\nERRORE CHIUSURA ROUND: ".$e;
			return null;
		}
	}
    
    function setLastRound($round){
		try{

			$settings=$this->dumpConfig();

			$tempQuery="UPDATE `settings` SET value=? where name='last-round' ";

			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt->bind_param("s", $round)) {
			    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}


		}catch(exception $e) {
			echo "\nERRORE CHIUSURA ROUND: ".$e;
			return null;
		}
	}

	function getRounds(){
		try{


			$tempQuery="SELECT * FROM `rounds` ";

			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			$res=$stmt->get_result();
			$res->data_seek(0);
			$arr=array();

			while ($row = $res->fetch_assoc()) {
				$arr[]=$row['round'];
			}

			return $arr;

		}catch(exception $e) {
			echo "\nERRORE GET ROUNDS: ".$e;
			return null;
		}
	}

	function addRound($round){
		try{
				$tempQuery="INSERT INTO `rounds` (`round`) VALUES (?) ";

				if(!($stmt = $this->mysqli->prepare($tempQuery))) {
				    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
				}

				if (!$stmt->bind_param("i", $round)) {
				    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
				}

				if (!$stmt->execute()) {
				    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
				}
				
				if(!$this->isOpenRound($round)){
				$settings=$this->dumpConfig();
				$available=$settings['available-round'];

				$rounds=explode(";",$available);

				if($available!=null){
					$mod=strval($round);
					$nuova=null;

					$first=true;

					foreach($rounds as $temp){
						if($temp!=$mod) {
							if($first) {
								$nuova=$temp;
								$first=false;
							}else{
								$nuova=$nuova.";".$temp;
							}
						}
					}
					$nuova=$nuova.";".$round;

				}else{
					$nuova=$round;
				}


				$tempQuery="UPDATE `settings` SET value=? where name='available-round' ";

				if(!($stmt = $this->mysqli->prepare($tempQuery))) {
				    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
				}

				if (!$stmt->bind_param("s", $nuova)) {
				    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
				}

				if (!$stmt->execute()) {
				    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
				}
			}

			return true;

		}catch(exception $e) {
			echo "\nERRORE Creazione ROUND: ".$e;
			return false;
		}

	}

	function openRound($round){
		try{

			if(!$this->isOpenRound($round)){
				$settings=$this->dumpConfig();
				$available=$settings['available-round'];
				
				$rounds=explode(";",$available);

				if($available!=null){
					$mod=strval($round);
					$nuova=null;

					$first=true;

					foreach($rounds as $temp){
						if($temp!=$mod) {
							if($first) {
								$nuova=$temp;
								$first=false;
							}else{
								$nuova=$nuova.";".$temp;
							}
						}
					}
					$nuova=$nuova.";".$round;

				}else{
					$nuova=$round;
				}


				$tempQuery="UPDATE `settings` SET value=? where name='available-round' ";

				if(!($stmt = $this->mysqli->prepare($tempQuery))) {
				    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
				}

				if (!$stmt->bind_param("s", $nuova)) {
				    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
				}

				if (!$stmt->execute()) {
				    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
				}

				if($this->isCalcRound($round)){

					$this->unCalcRound($round);

					$available=$settings['already-calc'];

					$rounds=explode(";",$available);

					$mod=strval($round);
					$nuova=null;
					$first=true;
					$mod=strval($round);
					$nuova=null;
					$first=true;

					foreach($rounds as $temp){
						if($temp!=$mod && $temp!="") {
							if($first) {
								$nuova=$temp;
								$first=false;
							}else{
								$nuova=$nuova.";".$temp;
							}
						}
					}

					$tempQuery="UPDATE `settings` SET value=? where name='already-calc' ";

					if(!($stmt = $this->mysqli->prepare($tempQuery))) {
					    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
					}

					if (!$stmt->bind_param("s", $nuova)) {
					    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
					}

					if (!$stmt->execute()) {
					    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
					}

				}
			}

		}catch(exception $e) {
			echo "\nERRORE APERTURA ROUND: ".$e;
			return null;
		}

	}

	function isOpenRound($round){
		$settings=$this->dumpConfig();
		$available=$settings['available-round'];

		$rounds=explode(";",$available);

		$mod=strval($round);
		$nuova=null;
		$ret=false;

		foreach($rounds as $temp){
			if($temp==$mod) $ret=true;
		}

		return $ret;
	}

	function isCalcRound($round){
		$settings=$this->dumpConfig();
		$available=$settings['already-calc'];

		$rounds=explode(";",$available);

		$mod=strval($round);
		$nuova=null;
		$ret=false;

		foreach($rounds as $temp){
			if($temp==$mod) $ret=true;
		}

		return $ret;
	}
    
    function calcRound($round){

    	$data_players=new ConnectDatabasePlayers($this->mysqli);
    	
		$players=$data_players->dumpSingoliToList(null,null);
		$tempQuery="SELECT stats.* , players.role  
					FROM stats 
					LEFT OUTER JOIN players
					ON players.id=stats.id_player 
					WHERE stats.round=?";
					
		

		try{
			
			/*$sassuolo = $data_players->dumpSingoliToList(null,"SASSUOLO");
			
			$sassuolo = array_values($sassuolo);
			
			for($i=0 ; $i<count($sassuolo) ; $i++){
				$pla = $sassuolo[$i];
								
				if($pla->getName() != "BERARDI"){
					$qr="REPLACE INTO `stats` (`id_player`,`round`,`vote`,`scored`,`taken`,`free_kick_keeped`,`free_kick_missed`,`free_kick_scored`,`autogol`,`yellow_card`,`red_card`,`assist`,`stop_assist`,`gdv`,`gdp`) VALUES(?,?,6,0,0,0,0,0,0,0,0,0,0,0,0)"; //14
					
					if(!($stmt = $this->mysqli->prepare($qr))) {
					    error_log("Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error);
					}
					
					$id = intval($pla->getId());
		
					if (!$stmt->bind_param("ii", $id,$round)) {
					    error_log("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
					}
		
					if (!$stmt->execute()) {
					    error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
					}
				}
			}
			
			$torino = $data_players->dumpSingoliToList(null,"TORINO");
			
			$torino = array_values($torino);
			
			for($i=0 ; $i<count($torino) ; $i++){
				$pla = $torino[$i];
				
				
				if($pla->getId() != 546){
					$qr="REPLACE INTO `stats` (`id_player`,`round`,`vote`,`scored`,`taken`,`free_kick_keeped`,`free_kick_missed`,`free_kick_scored`,`autogol`,`yellow_card`,`red_card`,`assist`,`stop_assist`,`gdv`,`gdp`) VALUES(?,?,6,0,0,0,0,0,0,0,0,0,0,0,0)"; //14
					
					if(!($stmt = $this->mysqli->prepare($qr))) {
					    error_log("Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error);
					}
					
					$id = intval($pla->getId());
		
					if (!$stmt->bind_param("ii", $id,$round)) {
					    error_log("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
					}
		
					if (!$stmt->execute()) {
					    error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
					}
				}
			}*/

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
			$arr=array();

			while ($row = $res->fetch_assoc()) {
				$id_player=$row['id_player'];

				$stats_coll=new StatisticsCollection();
				$stats_coll->setRound($round);
				$fields = array_keys($row);
				$query="select * from `stats` where id_player=? and round=?";

				if (!($st2 = $this->mysqli->prepare($query))) {
				    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
				}

				if (!$st2->bind_param("ii", $id_player,$round)) {
				    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
				}

				if (!$st2->execute()) {
				    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
				}

				$res2=$st2->get_result();
				$res2->data_seek(0);

				$row_num = $res2->fetch_array(MYSQLI_NUM);
				for($i=1;$i<count($fields);$i++){

					if(isset($fields[$i+1]) && isset($row_num[$i+1])){
						$stats_coll[$fields[$i+1]]=new Statistic($fields[$i+1],$row_num[$i+1]);
					}
				}

				$final_vote=$this->calc($stats_coll,$row['role']);

				$query="UPDATE stats SET final=? where id_player=? and round=?";

				if (!($st2 = $this->mysqli->prepare($query))) {
				    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
				}

				if (!$st2->bind_param("dii", $final_vote,$id_player,$round)) {
				    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
				}

				if (!$st2->execute()) {
				    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
				}
			}

			return true;
		}catch(exception $e) {
			echo "ex: ".$e;
			return false;
		}

		return false;
	}

	function substitute($roling,$round,$alread_in,$max_sub,$step=0){

		$data_players=new ConnectDatabasePlayers($this->mysqli);

        if($step>1) return 0;
		$stat=$data_players->dumpStatsByRound($roling[$step]->getPlayer()->getId(),$round);
		$nextSub=false;
		if($stat!=null && !isset($alread_in[$roling[$step]->getPlayer()->getId()])){
			
			$role = $roling[$step]->getPlayer()->getRole();
			
			$vote=$stat['final']->getValue();
			$original = $stat["vote"]->getValue();
			if($vote==-1){
				$nextSub=true;
			}else{
				
				$noEdit = $original;
				
				if($original == -1){ // MI SERVE VOTO FINALE MENO BONUS E MALUS
										
					if($original==-1 && strtolower($role)=="p"){
		                if($stat['red_card']->getValue()==1){
		                    $noEdit=4;
		                } // DA CONTROLLARE IL MINUTAGGIO
		                //$vote=$vote+$scored-$taken+$free_keep-$free_miss+$free_score-$auto-$yellow-$red+$assist+$stop_assist+$gdp+$gdv;
		            }else if($original==-1 && strtolower($role)!="p"){
		                if($stat['red_card']->getValue()==1){
		                    $noEdit=4;
		                }else if($stat['scored']->getValue()>0 || $stat['free_kick_keeped']->getValue()>0 || $stat['free_kick_scored']->getValue()>0 || $stat['assist']->getValue()>0 || $stat['stop_assist']->getValue()>0){
		                    $noEdit=6;
		                }else if($stat['free_kick_missed']->getValue()>0 || $stat['autogol']->getValue()>0){
		                   $noEdit=6;
		                }
		            }
					
				}
				
				$ret=array('vote'=>$vote,"originale" => $original , 'role' => $role , "noEdit" => $noEdit ,  'id'=>$roling[$step]->getPlayer()->getId());
				return $ret;
			}
		}else{
			$nextSub=true;
		}

		if (($max_sub--)==0 || $roling[$step]->getPlayer()->getRole()=='P') {
			return 0;
		}

		$step++;

		return $this->substitute($roling,$round,$alread_in,$max_sub,$step);
	}

	function calcRoundUser($round){

			$data_players=new ConnectDatabasePlayers($this->mysqli);

			$tempQuery="SELECT *  FROM users ";
			$config=$this->dumpConfig();

			$max_sub=2;
			
			$error = null;
			
			$minimum = floatval(60);
			
			$to_adding = array();
			
			if(isset($config['max_sub'])) $max_sub=$config['max_sub'];

			try{

				$players=$data_players->dumpSingoliToList(null,null);

				if(!($stmt = $this->mysqli->prepare($tempQuery))) {
				    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
				}

				/*if (!$stmt->bind_param("i", $round)) {
				    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
				}*/

				if (!$stmt->execute()) {
				    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
				}

				$res=$stmt->get_result();
				$res->data_seek(0);
				while ($row = $res->fetch_assoc()) {
					$sub=0;
					$result=0;
					$id_user=$row['id'];
					$team=$this->getTeam($id_user,$round);

					if($team->getPlayers()==null && $round>1 && $this->getTeam($id_user,$round-1)->getPlayers()!=null && $this->isValidFormation($id_user,$round)){ // SE NON CE SQUADRA QUESTA GIORNATA MA PRECEDENTE SI
						$team=$this->getTeam($id_user,$round-1);
						$roster=$team->getPlayers();
						$tempArr=$roster->orderByRole();
		                $start=$tempArr[0];
		                $back=$tempArr[1];

		                $ids=array();
		                $reserves=array();

		                foreach($start as $pl){
			                $temp=array();
			                $temp["id"]=$pl->getPlayer()->getId();
			                $temp["position"]=$pl->getPosition();
			                $ids[]=$temp;
			            }

			            foreach($back as $pl){
			                $temp=array();
			                $temp["id"]=$pl->getPlayer()->getId();
			                $temp["position"]=$pl->getPosition();
			                $reserves[]=$temp;
			            }

			            $tactic=$team->getDef().$team->getCen().$team->getAtt();
			            
			            $destroy_team = true;
			            
			            if($round>5 && $this->isValidFormationOfAnotherRound($id_user,$round,$round-1)){ // Check Se formazione non messa per 5 giornate
				            for($i=$round-1;$i>$round-5;$i--){
					            $checking_team = $this->getTeam($id_user,$i); // Prelevo il team
					            if(!$checking_team->isRecovered()){
						            $destroy_team=false;
					            }
				            }
			            }else if($round > 1 && $this->isValidFormationOfAnotherRound($id_user,$round,$round-1)){
				            $destroy_team = false;
			            }else{
				            $destroy_team = true;
			            }
			            
			            if(!$destroy_team){ // SE NELLE SCORSE 5 GIORNATE il team almeno una volta non è stato recuperato recuperalo
			            	$this->insertTeam($id_user,$ids,$reserves,$round,$tactic,true);
			            }else{ // ALTRIMENTI IMPOSTA A NO I PLAYERS DEL TEAM
				            $team->setPlayers(null);
				            $error = "5 Giornate senza aver inserito formazione";
			            }


					}


					if($team->getPlayers()!=null){
						$tit=$team->getPlayers()->orderByRole();
						$start=$tit[0];
						$fin=$tit[1];
						$alread_in=array();
						$modificatore = false;
						
						if($team->getDef()>3){
							$modificatore = true;
						}
						
						$modArr = array();
						$modArr["portiere"] = array();
						$modArr["centro"] = array();
						
						foreach($start as $pla){
							$player=$pla->getPlayer();
							$id_player=$player->getId();
							$position=$pla->getPosition();
							$stat=$data_players->dumpStatsByRound($id_player,$round);

							$enterSub=false;


							if($stat!=null){
								if($stat['final']->getValue()==-1){
									$enterSub=true;
								}else{
									$vote=$stat['final']->getValue();
									
									if($modificatore==true && $player->getRole()=="D"){
										$modArr["centro"][] = $stat['vote']->getValue();
									}else if($modificatore==true && $player->getRole()=="P"){
										$modArr["portiere"][] = $stat['vote']->getValue();
									}
									
									$result=$result+$vote;
								}
							}else{
								$enterSub=true;
							}

							if($sub<$max_sub && $enterSub==true){
								$roling=$fin->getByRole($player->getRole());

								$arr=$this->substitute($roling,$round,$alread_in,$max_sub);
								if($arr==0){
								}else{
									$subvote=$arr['vote'];
									$alread_in[$arr['id']]=$arr['id'];

									if($subvote!=0){
										$sub++;
										
										if($modificatore==true && $arr["role"]=="D"){
											if($arr["originale"] == $arr["noEdit"]){
												$modArr["centro"][] = $arr["originale"];
											}else{
												$modArr["centro"][] = $arr["noEdit"];
											}
										}else if($modificatore==true && $arr["role"]=="P"){
											if($arr["originale"] == $arr["noEdit"]){
												$modArr["portiere"][] = $arr["originale"];
											}else{
												$modArr["portiere"][] = $arr["noEdit"];
											}
										}
										
										$result=$result+$subvote;
									}
								}


							}

						}
						$gol=0;
						
						
						
						if(count($modArr["portiere"])>0 && count($modArr["centro"])>3){
							
							// OK MODIFICATORE VALE
							
							$punti_mod = $modArr["portiere"][0];
														
							$arrcen = $modArr["centro"];
							
							$new = array();
														
							foreach($arrcen as $ele){
								if(count($new)>0){
									for($i=0;$i<count($new);$i++){
										$enter = false;
										
										
										if($ele>=$new[$i]){
											$enter = true;
											$new_arr = array($ele);
											if($i==0){
												array_unshift($new, $ele);
												break;
											}else{
												
												array_splice($new, $i, 0, $new_arr);
												break;
											}
										}
										
										
									}
									
									if(!$enter){
										$new[] = $ele;
									}
								}else{
									$new[]=$ele;
								}
								
							}
							
							
							for($i = 0 ; $i<3 ; $i++){
								$punti_mod = $punti_mod + $new[$i];
							}
							
							$media = $punti_mod / 4;
							
							if($media >= 7){
								$result = $result + 6;
							}else if($media >= 6.5 && $media < 7){
								$result = $result + 3;
							}else if($media >= 6 && $media < 6.5){
								$result = $result + 1;
							}
							
							$tempQuery="UPDATE `tactics` SET modificatore=1 WHERE id_user=? and round=?";

							if(!($stmt = $this->mysqli->prepare($tempQuery))) {
							    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
							}
	
							if (!$stmt->bind_param("ii", $id_user,$round)) {
							    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
							}
	
							if (!$stmt->execute()) {
							    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
							}
							
						}

						

						if($result>=66){
							$gol=floor(($result-66)/6)+1;
						}
						
						
						
						if($result<60 && floatval($result)<floatval($minimum)){
							$minimum = floatval($result);
						}
						

						$tempQuery="REPLACE INTO `rounds_result` (`id_user`,`round`,`points`,`gol`) VALUES(?,?,?,?)";

						if(!($stmt = $this->mysqli->prepare($tempQuery))) {
						    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
						}

						if (!$stmt->bind_param("iidi", $id_user,$round,$result,$gol)) {
						    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
						}

						if (!$stmt->execute()) {
						    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
						}
						
						





					}else{
						
						$to_adding[]=$id_user;						
						
						
					}
					
					

				}
				
				
				if(count($to_adding)>0){
										
					foreach($to_adding as $id){
						
						if($round>1){
							$temp_team = $this->getTeam($id,$round-1);
							
							if($temp_team->getPlayers() != null){ // SE DIVERSO DA NULL METTI MINIMO
								$tempQuery="REPLACE INTO `rounds_result` (`id_user`,`round`,`points`,`gol`) VALUES(?,?,?,?)";

								if(!($stmt = $this->mysqli->prepare($tempQuery))) {
								    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
								}
								
								$zero = 0;
		
								if (!$stmt->bind_param("iidi", $id,$round,$minimum,$zero)) {
								    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
								}
		
								if (!$stmt->execute()) {
								    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
								}
							}else{ // ALTRIMENTI METTI ZERO
								$tempQuery="REPLACE INTO `rounds_result` (`id_user`,`round`,`points`,`gol`) VALUES(?,?,?,?)";

								if(!($stmt = $this->mysqli->prepare($tempQuery))) {
								    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
								}
								
								$zero = 0;
		
								if (!$stmt->bind_param("iidi", $id,$round,$zero,$zero)) {
								    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
								}
		
								if (!$stmt->execute()) {
								    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
								}
							}
							
						}else if($round == 1){
							
							$tempQuery="REPLACE INTO `rounds_result` (`id_user`,`round`,`points`,`gol`) VALUES(?,?,?,?)";

							if(!($stmt = $this->mysqli->prepare($tempQuery))) {
							    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
							}
							
							$zero = 0;
	
							if (!$stmt->bind_param("iidi", $id,$round,$minimum,$zero)) {
							    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
							}
	
							if (!$stmt->execute()) {
							    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
							}
							
						}
						
						
						
						
					}
					
					
				}
				
				$this->calcMatches($round);

				



		}catch(exception $e) {
				echo "ex: ".$e;
				return false;

		}
	}
	
	function calcMatches($round){

		$results = $this->getInfoRound($round);
		$db_competitions = new ConnectDatabaseCompetitions($this->mysqli);
		
		$db_handicaps = new ConnectDatabaseHandicaps($this->mysqli);
		
		$matches = $db_competitions->getMatchesByRound($round);
		
		$tempQuery="UPDATE `matches` SET result=? where id_match=?";
		
		
		foreach($matches as $match){
			
			error_log("match");
			
			$result = "X";
			
			$id_match = $match->getId();
			
			$points_one = intval($results[$match->getIdOne()]["points"]);
			$points_two = intval($results[$match->getIdTwo()]["points"]);
			
			$gol_one = 0;
			$gol_two = 0;
			
			$handicaps=$db_handicaps->getHandicapsRoundsByUserIdAndRound($match->getIdOne(),$round);

        	foreach($handicaps as $handicap){
				$round_handicap=$handicap->getPoints();
				$points_one=$points_one+$round_handicap;
			}
			
			$handicaps=$db_handicaps->getHandicapsRoundsByUserIdAndRound($match->getIdTwo(),$round);

        	foreach($handicaps as $handicap){
				$round_handicap=$handicap->getPoints();
				$points_two=$points_two+$round_handicap;
			}
			
			if($points_one>=66){
				$gol_one=floor(($points_one-66)/6)+1;
			}
			
			if($points_two>=66){
				$gol_two=floor(($points_two-66)/6)+1;
			}
			
			if($gol_one > $gol_two){
				$result = "1";
			}else if($gol_two > $gol_one){
				$result = "2";
			}


			
			try{
			
				if(!($stmt = $this->mysqli->prepare($tempQuery))) {
				    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
				    return false;
				}
	
				if (!$stmt->bind_param("si",  $result , $id_match)) {
				    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
				    return false;
				}
	
				if (!$stmt->execute()) {
				    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
				    return false;
				}	
		
		
			}catch(exception $e) {
					echo "ex: ".$e;
					return false;
	
			}
		}
		
		return true;
		
		
		
	}

	function unCalcRound($round){
		if($this->isCalcRound($round)){
			$tempQuery="UPDATE stats SET final=NULL  WHERE round=? ";
			$tempRemove="DELETE FROM rounds_result WHERE round=?";
			
			$db_users = new ConnectDatabaseUsers($this->mysqli);
			
			

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

				if(!($stmt = $this->mysqli->prepare($tempRemove))) {
				    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
				}

				if (!$stmt->bind_param("i", $round)) {
				    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
				}

				if (!$stmt->execute()) {
				    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
				}
				
				$users = $db_users->getUsers();
				
				foreach($users as $user){
					$id_user = $user->getId();
					
					$team = $this->getTeam($id_user,$round);
					
					if($team->isRecovered()){
						$this->deleteTeam($id_user,$round);
					}
				}
				
				$tempQuery="DELETE FROM `recaps` WHERE round=? ";

				if(!($stmt = $this->mysqli->prepare($tempQuery))) {
				    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
				}
	
				if (!$stmt->bind_param("i", $round)) {
				    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
				}
	
				if (!$stmt->execute()) {
				    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
				}
				
				
				return $this->unCalcMatches($round);

			}catch(exception $e) {
				echo "ex: ".$e;
				return false;
			}
		}

		return false;
	}
	
	function unCalcMatches($round){
		if($this->isCalcRound($round)){
			$tempRemove="UPDATE `matches` SET result=NULL where round=?";			
			

			try{

				if(!($stmt = $this->mysqli->prepare($tempRemove))) {
				    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
				    return false;
				}

				if (!$stmt->bind_param("i", $round)) {
				    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
				    return false;
				}

				if (!$stmt->execute()) {
				    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
				    return false;
				}
				
				return true;
			}catch(exception $e) {
				echo "ex: ".$e;
				return false;
			}
		}

		return false;
	}

	function getInfoRound($round){
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

			$arr=array();

			while ($row = $res->fetch_assoc()) {
				$id_user=$row['id_user'];
				$gol=$row['gol'];
				$points=$row['points'];

				$arr[$id_user]=array('gol' => $gol, 'points' => $points);
			}

			return $arr;

		}catch(exception $e) {
			echo "ex: ".$e;
			return false;

		}
	}
	
	function getInfoRoundUser($round,$id_user){
		$tempQuery="SELECT * FROM rounds_result  WHERE round=? and id_user=?";
		
		$db_handicaps = new ConnectDatabaseHandicaps($this->mysqli);

		try{
			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt->bind_param("ii", $round,$id_user)) {
			    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			$res=$stmt->get_result();
			$res->data_seek(0);

			$arr=null;
			
			

			while ($row = $res->fetch_assoc()) {
				$id_user=$row['id_user'];
				$gol=$row['gol'];
				$points=$row['points'];
				
				$handicaps=$db_handicaps->getHandicapsRoundsByUserIdAndRound($id_user,$round);

	        	foreach($handicaps as $handicap){
					$round_handicap=$handicap->getPoints();
					$points=$points+$round_handicap;
				}
				
				if($points>=66){
					$gol=floor(($points-66)/6)+1;
				}

				$arr=array('gol' => $gol, 'points' => $points);
			}

			return $arr;

		}catch(exception $e) {
			echo "ex: ".$e;
			return false;

		}
	}
	
	
	function getRecap($round){
		$tempQuery="SELECT * FROM recaps  WHERE round=? ";

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

			$arr=null;

			while ($row = $res->fetch_assoc()) {
				$id_user=$row['winner'];
				$points=$row['points'];
				$position=$row['position'];
				$round = $row["round"];

				$arr=array( 'points' => $points,'position' => $position,'id_user'=>$id_user,'round'=>$round);
			}

			return $arr;

		}catch(exception $e) {
			echo "ex: ".$e;
			return false;

		}
	}

	function getRoundsOfCompetition($id_competition){
		$tempQuery="SELECT * FROM competitions_in_rounds  WHERE id_competition=?  ORDER BY round";
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
				$arr[$row["round"]]=$row['round_competition'];
			}

			return $arr;

		}catch(exception $e) {
			echo "ex: ".$e;
			return null;

		}
	}

	function getRoundsByCompetition($id_competition){
		$tempQuery="SELECT * FROM competitions_in_rounds  WHERE id_competition=? order by round_competition";

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
				$arr[$row['round_competition']]=$row['round'];
			}

			return $arr;

		}catch(exception $e) {
			echo "ex: ".$e;
			return null;

		}
	}

	function isPossibleToEditFormation($id_round){
		$tempQuery="SELECT * FROM rounds  WHERE round=? ";

		try{
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
				$datetemp = date ("Y-m-d H:i:s", strtotime(str_replace('-','/', $row['closetime'])));
				$date=new DateTime($datetemp);
				
				$now=new DateTime("now");
								
				$date->sub(new DateInterval("PT15M"));
			
				$stamp_now=$now->getTimestamp();
				$stamp_finish=$date->getTimestamp();

				$diff=$stamp_finish-$stamp_now;                
                
                if($diff>0 && !$this->isCalcRound($id_round)){
					return true;
				}else{
					return false;
				}
			}

			return $arr;

		}catch(exception $e) {
			echo "ex: ".$e;
			return null;

		}
	}
    
    function secondsToClosingTime(){
		$tempQuery="SELECT * FROM rounds  WHERE round=? ";
        
        $conf=$this->dumpConfig();
        $round=$conf['current_round'];
        
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

			$arr=array();

			while ($row = $res->fetch_assoc()) {
				$datetemp = date ("Y-m-d H:i:s", strtotime(str_replace('-','/', $row['closetime'])));

				$date=new DateTime($datetemp);

				$date->sub(new DateInterval("PT15M"));
                
				return $date->format("Y/m/d H:i:s");
                
			}

			return 0;

		}catch(exception $e) {
			echo "ex: ".$e;
			return 0;

		}
	}

	function roundExist($id_round){
		$tempQuery="SELECT * FROM rounds  WHERE round=? ";

		try{
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
				return true;
			}

			return false;

		}catch(exception $e) {
			echo "ex: ".$e;
			return false;

		}
	}
    
    function getRoundCompetitionByRealRound($round,$id_competition){
		try{
            $thisRound="SELECT * FROM competitions_in_rounds WHERE round=? and id_competition=?";
            
            if(!($stmt = $this->mysqli->prepare($thisRound))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt->bind_param("ii", $round,$id_competition)) {
			    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}
            
            $res=$stmt->get_result();
			$res->data_seek(0);
			$arr=array();

            $round_comp=$round;
            
			while ($row = $res->fetch_assoc()) {
				return $row['round_competition'];

			}


		}catch(exception $e) {
			echo "\nERRORE GET MARKET: ".$e;
			return null;
		}
	}
    
    function getRealRoundByRoundCompetition($round,$id_competition){
		try{
            $thisRound="SELECT * FROM competitions_in_rounds WHERE round_competition=? and id_competition=?";
            
            if(!($stmt = $this->mysqli->prepare($thisRound))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt->bind_param("ii", $round,$id_competition)) {
			    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}
            
            $res=$stmt->get_result();
			$res->data_seek(0);
			$arr=array();

            $round_comp=$round;
            
			while ($row = $res->fetch_assoc()) {
				return $row['round'];

			}


		}catch(exception $e) {
			echo "\nERRORE GET MARKET: ".$e;
			return null;
		}
	}
  
    function getTeamsByRoundAndCompetition($round,$id_competition){
		$arr=array();
        $players=new ConnectDatabasePlayers($this->mysqli);
		try{
			$tempUser="SELECT users.* FROM users LEFT OUTER JOIN users_in_competitions ON users_in_competitions.id_user=users.id WHERE users_in_competitions.id_competition=?";
			$tempQuery="SELECT teams.* , users.username FROM teams LEFT OUTER JOIN users ON users.id=teams.id_user WHERE teams.id_user=? ";
            $thisRound="SELECT * FROM competitions_in_rounds WHERE round_competition=?";
            
            if(!($stmt = $this->mysqli->prepare($thisRound))) {
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
			$arr=array();

            $round_comp=$round;
            
			while ($row = $res->fetch_assoc()) {
				$round_comp=$row['round'];

			}

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
				$arr[]=$arrayName = array('username' => $username , 'name_team' => $name_team, 'id_user' => $id_user , 'team' => $this->getTeam($id_user,$round_comp));

			}

			return $arr;

		}catch(exception $e) {
			echo "\nERRORE GET MARKET: ".$e;
			return null;
		}
	}

    function getLastStatRound(){
		$tempQuery="SELECT MAX(round) as max FROM stats";

		try{
			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			$res=$stmt->get_result();
			$res->data_seek(0);

			while ($row = $res->fetch_assoc()) {
				return $row['max'];
			}

			return 0;

		}catch(exception $e) {
			echo "ex: ".$e;
			return 0;

		}
	}
	
	function getLastQuoteRound(){
		$tempQuery="SELECT MAX(round) as max FROM quote";

		try{
			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			$res=$stmt->get_result();
			$res->data_seek(0);

			while ($row = $res->fetch_assoc()) {
				return $row['max'];
			}

			return 0;

		}catch(exception $e) {
			echo "ex: ".$e;
			return 0;

		}
	}
	
	function getLastCalcRound(){
		$tempQuery="SELECT MAX(round) as max FROM rounds_result";

		try{
			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			$res=$stmt->get_result();
			$res->data_seek(0);

			while ($row = $res->fetch_assoc()) {
				return $row['max'];
			}

			return 0;

		}catch(exception $e) {
			echo "ex: ".$e;
			return 0;

		}
	}

    function calc($stat,$role){
            $vote=$stat['vote']->getValue();
            $scored=3*$stat['scored']->getValue();
            $taken=1*$stat['taken']->getValue();
            $free_keep=3*$stat['free_kick_keeped']->getValue();
            $free_miss=3*$stat['free_kick_missed']->getValue();
            $free_score=3*$stat['free_kick_scored']->getValue();
            $auto=2*$stat['autogol']->getValue();
            $yellow=0.5*$stat['yellow_card']->getValue();
            $red=1*$stat['red_card']->getValue();

            $autogol=2*$stat['autogol']->getValue();

            $assist=1*$stat['assist']->getValue();
            $stop_assist=1*$stat['stop_assist']->getValue();

            $gdp=0*$stat['gdp']->getValue();
            $gdv=0*$stat['gdv']->getValue();

            if($vote!=-1){
                $vote=$vote+$scored-$taken+$free_keep-$free_miss+$free_score-$auto-$yellow-$red+$assist+$stop_assist+$gdp+$gdv;
            }else if($vote==-1 && strtolower($role)=="p"){
                if($stat['red_card']->getValue()==1){
                    $vote=4;
                } // DA CONTROLLARE IL MINUTAGGIO
                //$vote=$vote+$scored-$taken+$free_keep-$free_miss+$free_score-$auto-$yellow-$red+$assist+$stop_assist+$gdp+$gdv;
            }else if($vote==-1 && strtolower($role)!="p"){
                if($stat['red_card']->getValue()==1){
                    $vote=4;
                }else if($stat['scored']->getValue()>0 || $stat['free_kick_keeped']->getValue()>0 || $stat['free_kick_scored']->getValue()>0 || $stat['assist']->getValue()>0 || $stat['stop_assist']->getValue()>0){
                    $vote=6;
                    $vote=$vote+$scored+$free_keep+$free_score+$assist+$stop_assist;
                }else if($stat['free_kick_missed']->getValue()>0 || $stat['autogol']->getValue()>0){
                    $vote=6;
                    $vote=$vote-$free_miss-$autogol;
                }else{
                    $vote=-1;
                }
            }

            return $vote;

    }
    
    function getRoundStandings($id_competition,$id_round){
		$results=array();

		$data_competitions=new ConnectDatabaseCompetitions($this->mysqli);
		$data_handicaps = new ConnectDatabaseHandicaps($this->mysqli);
		
		$id_round=$this->getRealRoundByRoundCompetition($id_round,$id_competition);
		
        $tempQuery="SELECT * FROM rounds_result  WHERE round=? ";

        try{
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

            $round_result=array();

            while ($row = $res->fetch_assoc()) {

            	$id_user=$row['id_user'];

            	$handicaps=$data_handicaps->getHandicapsRoundsByUserId($id_user);

            	$result=$row['points'];
				$gol=$row['gol'];

	        	foreach($handicaps as $handicap){
					if(intval($handicap->getRound())==intval($id_round)){
						$round_handicap=$handicap->getPoints();
						$result=$result+$round_handicap;
						if($result>=66){
							$gol=floor(($result-66)/6)+1;
						}
					}
				}

                $round_result[$id_user]['points']=$result;
                $round_result[$id_user]['gol']=$gol;
                $round_result[$id_user]['user']=$id_user;
            }
         

            $results=$round_result;


        }catch(exception $e) {
            echo "ex: ".$e;
            return true;

        }
        
        if(count($results)==0){
	        return null;
        }
        
        
		$classifica=array();
		$users=$data_competitions->getUsersInCompetition($id_competition);

		foreach($users as $user){
			$gols=0;
			$points=0;
			$round=$results;
			
			if(isset($round[intval($user["id"])])){
                $res=$round[intval($user["id"])];
                $gols+=$res['gol'];
                $points+=$res['points'];
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

}
    
    ?>