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

    function insertTeam($id_user,$ids,$reserves,$round,$tactic){
	   
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

				$pla=$players[$id["id"]];
				$position = $id["position"];
				$zero=0;
				
				$id_pla = $pla->getId();

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

				$pla=$players[$id["id"]];
				$position = $id["position"];

				$uno=1;
				
				$id_pla = $pla->getId();

				if (!$stmt->bind_param("iiii", $id_user,$id_pla,$round,$position)) {
				    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
				}

				if (!$stmt->execute()) {
				    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
				}
			}
			

			$tacticQuery="REPLACE INTO tactics (`id_user`,`tactic`,`round`) VALUES (?,?,?);";

			if(!($stmt = $this->mysqli->prepare($tacticQuery))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt->bind_param("isi", $id_user,$tactic,$round)) {
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

				$players_team[]=new TeamPlayerRound($pla,$pos);
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

			while ($row = $res->fetch_assoc()) {
				$tactic=$row['tactic'];
				$dif=$tactic[0];
				$cen=$tactic[1];
				$att=$tactic[2];

                if($this->isValidFormation($id_user,$round)){

                }else{
                    $players_team=null;
                }

			}


			$team=new Team($id_user,$round,$dif,$cen,$att,$players_team);

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
			
			error_log("precalc");

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
		$tempQuery="SELECT stats.* , pla.role  FROM stats LEFT OUTER JOIN (  SELECT *  FROM (SELECT id as t, MAX(round) AS time FROM players GROUP BY t ) l JOIN players b
					ON b.id = l.t AND b.round = l.time GROUP BY b.round, b.id  ) as pla ON pla.id=stats.id_player WHERE stats.round=?";

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
			                $ids[]=$pl->getPlayer()->getId();
			            }

			            foreach($back as $pl){
			                $reserves[]=$pl->getPlayer()->getId();
			            }

			            $tactic=$team->getDef().$team->getCen().$team->getAtt();

			            $this->insertTeam($id_user,$ids,$reserves,$round,$tactic);

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
										$modArr["centro"] = $stat['vote']->getValue();
									}else if($modificatore==true && $player->getRole()=="P"){
										$modArr["portiere"] = $stat['vote']->getValue();
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
												$modArr["centro"] = $arr["originale"];
											}else{
												$modArr["centro"] = $arr["noEdit"];
											}
										}else if($modificatore==true && $arr["role"]=="P"){
											if($arr["originale"] == $arr["noEdit"]){
												$modArr["portiere"] = $arr["originale"];
											}else{
												$modArr["portiere"] = $arr["noEdit"];
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
							
							$arrcen = rsort($modArr["centro"]);
							
							for($i = 0 ; $i<3 ; $i++){
								$punti_mod = $punti_mod + $arrcen[$i];
							}
							
							$media = $punti_mod / 4;
							
							if($media >= 7){
								$result = $result + 6;
							}else if($media >= 6.5 && $media < 7){
								$result = $result + 3;
							}else if($media >= 6 && $media < 6.5){
								$result = $result + 1;
							}
						}

						

						if($result>=66){
							$gol=floor(($result-66)/6)+1;
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
						$tempQuery="REPLACE INTO `rounds_result` (`id_user`,`round`) VALUES(?,?)";

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

				}





		}catch(exception $e) {
				echo "ex: ".$e;
				return false;

		}
	}

	function unCalcRound($round){
		if($this->isCalcRound($round)){
			$tempQuery="UPDATE stats SET final=NULL  WHERE round=? ";
			$tempRemove="DELETE FROM rounds_result WHERE round=?";

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

				$now=new DateTime("now");
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
            $thisRound="SELECT * FROM competitions_in_rounds WHERE round=?";
            
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
				return $row['round_competition'];

			}


		}catch(exception $e) {
			echo "\nERRORE GET MARKET: ".$e;
			return null;
		}
	}
    
    function getRealRoundByRoundCompetition($round,$id_competition){
		try{
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
		$tempQuery="SELECT MAX(round) as max FROM players";

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
            $free_miss=1*$stat['free_kick_missed']->getValue();
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
		    return $diff;
		});
		return $classifica;

	}

}
    
    ?>