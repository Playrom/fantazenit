<?php


class ConnectDatabase{

	private	$username;
	private $password;
	private $ip;
	private $port;
	private $database_name;

	private $mysqli;

	function __construct($ip,$username,$password,$database_name,$port){
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

		$tempQuery="SELECT * FROM (SELECT id, MAX(timestamp) AS timestamp FROM players GROUP BY id ) l JOIN players b
   					ON b.id = l.id AND b.timestamp = l.timestamp GROUP BY b.timestamp, b.id";
		$another=0;
        if($name_str!=null || $team_str!=null) $tempQuery=$tempQuery."where ";

        if($name_str!=null) {
            $tempQuery=$tempQuery."name LIKE \'".$name_str."\'";
            $another++;
        }

        if($team_str!=null){
            if($another!=0)  $tempQuery=$tempQuery." and ";
            $tempQuery=$tempQuery."team LIKE \'".$team_str."\'";

        }

        $tempQuery=$tempQuery." order by name";


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

		    $item=new Player($id,$name,$team,$role,$value,$first_value,$diff);

		    $arr[$id]=$item;
		}
		return $arr;
	}

	function close(){
		$this->mysqli->close();
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
		$query="select * from `players` where id=? ORDER BY timestamp DESC LIMIT 1";


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
					$name=$row['name'];
					$team=$row['team'];
					$role=$row['role'];
					$value=$row['value'];
					$first_value=$row['first_value'];
					$diff=$row['diff'];

					$stats=$this->dumpStats($id);
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
				$stats=$this->dumpStats($id);
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
				$query="select * from `stats` where id_player=? and round=?";

				if (!($st2 = $this->mysqli->prepare($query))) {
				    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
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

	function signupUser(User $user){

		$query="select * from `users` where email LIKE ?";
		$queryInsert="insert into `users`( `name`, `surname`, `username`, `password`, `email`,`balance`,`name_team`) VALUES (?,?,?,?,?,?,?)";
		try{
			if (!($stmt = $this->mysqli->prepare($query))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt->bind_param("s", strtolower($user->getEmail()))) {
			    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			$res=$stmt->get_result();
			$res->data_seek(0);
			while ($row = $res->fetch_assoc()) {
				return false;
			}




			if (!($stmt = $this->mysqli->prepare($queryInsert))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt->bind_param("sssssis", $user->getName(),$user->getSurname(),$user->getUsername(),$user->getPassword(),strtolower($user->getEmail()),$user->getBalance(),$user->getNameTeam())) {
			    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			    return false;
			}

				return true;

		}catch(exception $e) {
			echo "\nERRORE REGISTRAZIONE UTENTE: ".$e;
			return false;
		}

		return true;

	}

	function getUserByEmail($email){

		$query="select * , UNIX_TIMESTAMP(reg_date) as time from `users` where email LIKE ?";
		try{
			if (!($stmt = $this->mysqli->prepare($query))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt->bind_param("s", strtolower($email))) {
			    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			$res=$stmt->get_result();
			$res->data_seek(0);
			while ($row = $res->fetch_assoc()) {

				$id=$row['id'];
				$name=$row['name'];
				$surname=$row['surname'];
				$username=$row['username'];
				$auth=$row['auth'];
				$balance=$row['balance'];
				$password=$row['password'];
				$name_team=$row['name_team'];

				$datetemp = date ("Y-m-d H:i:s", $row['time']);
				$date=new DateTime($datetemp);

				$res2=$this->mysqli->query("select * from `rosters` where id_user=".$id);
				$roster=new RosterList();
				while ($row2 = $res2->fetch_assoc()) {
					$cost=$row2['cost'];
					$player=$this->dumpPlayer($row2['id_player']);
					$roster[]=new RosterPlayer($player,$cost);
				}


				return new User($id,$username,$name,$surname,$password,$email,$date,$auth,$balance,$roster,NULL,$name_team);
			}



		}catch(exception $e) {
			echo "\nERRORE DUMP USER BY EMAIL: ".$e;
			return null;
		}

		return null;

	}


	function getUserByUsername($username){

		$query="select *,UNIX_TIMESTAMP(reg_date) as time from `users` where username LIKE ?";
		try{
			if (!($stmt = $this->mysqli->prepare($query))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt->bind_param("s", $username)) {
			    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			$res=$stmt->get_result();
			$res->data_seek(0);
			while ($row = $res->fetch_assoc()) {

				$id=$row['id'];
				$name=$row['name'];
				$surname=$row['surname'];
				$email=$row['email'];
				$auth=$row['auth'];
				$balance=$row['balance'];
				$password=$row['password'];
				$name_team=$row['name_team'];

				$datetemp = date ("Y-m-d H:i:s", $row['time']);
				$date=new DateTime($datetemp);

				$res2=$this->mysqli->query("select * from `rosters` where id_user=".$id);
				$roster=new RosterList();
				while ($row2 = $res2->fetch_assoc()) {
					$cost=$row2['cost'];
					$player=$this->dumpPlayer(intval($row2['id_player']));
					$roster[]=new RosterPlayer($player,$cost);
				}

				return new User($id,$username,$name,$surname,$password,$email,$date,$auth,$balance,$roster,NULL,$name_team);
			}



		}catch(exception $e) {
			echo "\nERRORE DUMP USER BY NAME: ".$e;
			return null;
		}

		return null;

	}

	function getUserById($id){

		$query="select *,UNIX_TIMESTAMP(reg_date) as time from `users` where id=?";
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

				$username=$row['username'];
				$name=$row['name'];
				$surname=$row['surname'];
				$email=$row['email'];
				$auth=$row['auth'];
				$balance=$row['balance'];
				$password=$row['password'];
				$name_team=$row['name_team'];

				$datetemp = date ("Y-m-d H:i:s", $row['time']);
				$date=new DateTime($datetemp);

				$res2=$this->mysqli->query("select * from `rosters` where id_user=".$id);
				$roster=new RosterList();
				while ($row2 = $res2->fetch_assoc()) {
					$cost=$row2['cost'];
					$player=$this->dumpPlayer(intval($row2['id_player']));
					$roster[]=new RosterPlayer($player,$cost);
				}

				return new User($id,$username,$name,$surname,$password,$email,$date,$auth,$balance,$roster,NULL,$name_team);
			}



		}catch(exception $e) {
			echo "\nERRORE DUMP USER BY ID: ".$e;
			return null;
		}

		return null;

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

			return true;


		}catch(exception $e) {
			echo "\nERRORE DUMP CONFIG: ".$e;
			return false;
		}


	}


	function createRoster($user,$players,$ids){
		try{

			$tempQuery="SELECT * from `rosters` where id_user=?;";

			if (!($stmt = $this->mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt->bind_param("i", $user->getId())) {
			    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}


			$roster=new RosterList();
			$new_balance=$user->getBalance();

			$res=$stmt->get_result();
			$res->data_seek(0);
			while ($row = $res->fetch_assoc()) {
				$id=$row['id_player'];
				$new_balance=$new_balance+$players[$id]->getValue();
			}


			$tempQuery="DELETE from `rosters` where id_user=?;";

			if (!($stmt = $this->mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt->bind_param("i", $user->getId())) {
			    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}


			$tempQuery="INSERT INTO `rosters` ( `id_user`, `id_player`, `cost`) VALUES (?,?,?);";

			foreach($ids as $id){


				if (!($stmt = $this->mysqli->prepare($tempQuery))) {
				    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
				}

				if (!$stmt->bind_param("iii", $user->getId(),$players[$id]->getId(),$players[$id]->getValue())) {
				    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
				}

				if (!$stmt->execute()) {
				    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
				}

				$roster[]=new RosterPlayer($players[$id],$players[$id]->getValue());
				$new_balance=$new_balance-$players[$id]->getValue();
				var_dump($new_balance);
			}

			$modUser="UPDATE users SET balance=? where id=?";

			if (!($stmt = $this->mysqli->prepare($modUser))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt->bind_param("ii", $new_balance,$user->getId())) {
			    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			$user->setBalance($new_balance);
			$user->setPlayers($roster);

			return true;






		}catch(exception $e) {
			echo "\nERRORE CREATION ROSTER: ".$e;
			return false;
		}

		return true;
	}


	function insertTeam($id_user,$ids,$reserves,$round,$tactic){
		try{

			$players=$this->dumpSingoliToList(null,null);

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

				$pla=$players[$id];
				$zero=0;

				if (!$stmt->bind_param("iiii", $id_user,$pla->getId(),$round,$zero)) {
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

				$pla=$players[$id];

				$uno=1;

				if (!$stmt->bind_param("iiii", $id_user,$pla->getId(),$round,$uno)) {
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

	function getTeam($id_user,$round){


		$players=$this->dumpSingoliToList(null,null);
		try{

			$tempQuery="SELECT * from `teams` where id_user=? and round=?;";

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
				$players_team[]=new TeamPlayerRound($players[$id],$pos);
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
			}

			$team=new Team($id_user,$round,$dif,$cen,$att,$players_team);

			return $team;


		}catch(exception $e) {
			echo "\nERRORE DUMP FORMAZIONE: ".$e;
			return null;
		}
	}


	function changePlayer($old_player,$new_player,$user,$players,$id_market){
		try{

			////////// DELETE FROM ROSTER

			$delete = "DELETE FROM rosters where id_user=? and id_player=?;";

			if(!($stmt = $this->mysqli->prepare($delete))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt->bind_param("ii", $user->getId(),$old_player->getId())) {
			    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			////////// ADD TO ROSTER

			$addRoster = "INSERT INTO `rosters` ( `id_user`, `id_player`, `cost`) VALUES (?,?,?);";

			if(!($stmt = $this->mysqli->prepare($addRoster))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			$new_balance=$old_player->getValue()-$new_player->getValue();

			if (!$stmt->bind_param("iii", $user->getId(),$new_player->getId(),$new_balance)) {
			    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			$new_balance=$user->getBalance()+$old_player->getValue()-$new_player->getValue();

			/////////  ADD TRANSFER

			$addTransfer = "INSERT INTO `transfers` (`id_user`, `id_market`, `id_new_player`, `id_old_player`, `new_player_cost`, `old_player_cost`) VALUES (?,?,?,?,?,?);\n";

			if(!($stmt = $this->mysqli->prepare($addTransfer))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt->bind_param("iiiiii", $user->getId(),$id_market,$new_player->getId(),$old_player->getId(),$new_player->getValue(),$old_player->getValue())) {
			    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}


			$generated_id=$this->mysqli->insert_id;

			//////// UPDATE BALANCE

			$modUser ="UPDATE users SET balance=? where id=?";

			if(!($stmt = $this->mysqli->prepare($modUser))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt->bind_param("ii", $new_balance,$user->getId())) {
			    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}


		}catch(exception $e) {
			echo "\nERRORE DUMP CAMBIO GIOCATORE: ".$e;
			return null;
		}
	}


	function getTransfers($user,$players){
		$transfers=array();

		try{
			$tempQuery="Select * , UNIX_TIMESTAMP(date) as time from `transfers` where id_user=?";

			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt->bind_param("i", $user->getId())) {
			    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			$res=$stmt->get_result();
			$res->data_seek(0);

			while ($row = $res->fetch_assoc()) {
				$id=$row['id_transfer'];
				$old_cost=$row['old_player_cost'];
				$new_cost=$row['new_player_cost'];
				$old_player=new RosterPlayer($players[$row['id_old_player']],$old_cost);
				$new_player=new RosterPlayer($players[$row['id_new_player']],$new_cost);
				$datetemp = date ("Y-m-d H:i:s", $row['time']);
				$date=new DateTime($datetemp);;
				$id_market=$row['id_market'];

				$transfers[$id]=new Transfer($id,$user,$id_market,$old_player,$new_player,$date);

			}

			return $transfers;


		}catch(exception $e) {
			echo "\nERRORE DUMP TRANSFERS: ".$e;
			return null;
		}
	}


	function getTransfersByIdMarket($user,$players,$id_market){
		$transfers=array();

		try{
			$tempQuery="Select * , UNIX_TIMESTAMP(date) as time from `transfers` where id_user=? and id_market=?";

			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt->bind_param("ii", $user->getId(),$id_market)) {
			    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			$res=$stmt->get_result();
			$res->data_seek(0);

			while ($row = $res->fetch_assoc()) {
				$id=$row['id_transfer'];
				$old_cost=$row['old_player_cost'];
				$new_cost=$row['new_player_cost'];
				$old_player=new RosterPlayer($players[$row['id_old_player']],$old_cost);
				$new_player=new RosterPlayer($players[$row['id_new_player']],$new_cost);
				$datetemp = date ("Y-m-d H:i:s", $row['time']);
				$date=new DateTime($datetemp);;
				$id_market=$row['id_market'];

				$transfers[$id]=new Transfer($id,$user,$id_market,$old_player,$new_player,$date);

			}

			return $transfers;


		}catch(exception $e) {
			echo "\nERRORE DUMP TRANSFERS: ".$e;
			return null;
		}
	}



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

			$this->calcRound($round);

			$this->calcRoundUser($round);

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

					var_dump($available);

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


					var_dump($nuova);

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

	function getOpenMarkets(){
		try{
			$tempQuery="SELECT * FROM `markets` WHERE start_date<? and finish_date>?";
			$now=new DateTime("now");
			$date=$now->format("Y-m-d H:i:s");


			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt->bind_param("ss", $date,$date)) {
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
				$name=$row['name'];
				$start_date=$row['start_date'];
				$finish_date=$row['finish_date'];
				$max_change=$row['max_change'];
				$arr[]=new Market($id,$name,$start_date,$finish_date,$max_change);

			}

			return $arr;

		}catch(exception $e) {
			echo "\nERRORE GET MARKET: ".$e;
			return null;
		}
	}

    function getMarkets(){
		try{
			$tempQuery="SELECT * FROM `markets`";
			$now=new DateTime("now");
			$date=$now->format("Y-m-d H:i:s");


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
				$id=$row['id'];
				$name=$row['name'];
				$start_date=$row['start_date'];
				$finish_date=$row['finish_date'];
				$max_change=$row['max_change'];

                $date_1=DateTime::createFromFormat('Y-m-d H:i:s', $start_date);
                $date_2=DateTime::createFromFormat('Y-m-d H:i:s', $finish_date);



				$arr[]=new Market($id,$name,$date_1,$date_2,$max_change);

			}

			return $arr;

		}catch(exception $e) {
			echo "\nERRORE GET MARKET: ".$e;
			return null;
		}
	}

	function getMarketById($id){
		try{
			$tempQuery="SELECT * FROM `markets` WHERE id=?";
			$now=new DateTime("now");
			$date=$now->format("Y-m-d H:i:s");


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
				$name=$row['name'];
				$start_date=$row['start_date'];
				$finish_date=$row['finish_date'];
				$max_change=$row['max_change'];
                $date_1=DateTime::createFromFormat('Y-m-d H:i:s', $start_date);
                $date_2=DateTime::createFromFormat('Y-m-d H:i:s', $finish_date);


				$ret=new Market($id,$name,$date_1,$date_2,$max_change);

				return $ret;

			}

			return null;

		}catch(exception $e) {
			echo "\nERRORE GET MARKET: ".$e;
			return null;
		}
	}

    function deleteMarket($id){
		try{
			$tempQuery="DELETE FROM `markets` WHERE id=?";

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
			echo "\nERRORE DELETE MARKET: ".$e;
			return false;
		}
	}

    function createMarket($name,$max_change,$start_date,$finish_date){
		try{
            $date_1=DateTime::createFromFormat('d-m-Y H:i', $start_date);
            $date_2=DateTime::createFromFormat('d-m-Y H:i', $finish_date);

			$tempQuery="INSERT INTO `markets` (`name`, `start_date`, `finish_date`, `max_change`) VALUES (?,?,?,?)";

			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt->bind_param("sssi", $name,$date_1->format("Y-m-d H:i:00"),$date_2->format("Y-m-d H:i:00"),$max_change)) {
			    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			return true;

		}catch(exception $e) {
			echo "\nERRORE INSERT MARKET: ".$e;
			return false;
		}
	}

    function editMarket($id,$name,$max_change,$start_date,$finish_date){
		try{
            $date_1=DateTime::createFromFormat('d-m-Y H:i', $start_date);
            $date_2=DateTime::createFromFormat('d-m-Y H:i', $finish_date);
            var_dump($id);

			$tempQuery="REPLACE INTO `markets` (`id`,`name`, `start_date`, `finish_date`, `max_change`) VALUES (?,?,?,?,?)";

			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt->bind_param("isssi", $id,$name,$date_1->format("Y-m-d H:i:00"),$date_2->format("Y-m-d H:i:00"),$max_change)) {
			    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			return true;

		}catch(exception $e) {
			echo "\nERRORE EDIT MARKET: ".$e;
			return false;
		}
	}

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

	function loadStatsToDatabase($round,$file){
		$dom = new DOMDocument;
		$dom->loadHTMLFile($file);
		$lines=$dom->getElementsByTagName('tr');
		$i=0;
		foreach($lines as $item){

			$enter=true;
			$col=$item->getElementsByTagName('td');
			foreach($col as $k){
				if($k->hasAttribute("colspan")) $enter=false;
			}

			$is_people=true;

			if($enter){
				$html='';
				$children=$col->item(0)->childNodes;
				foreach($children as $child){
					if(!is_numeric($child->ownerDocument->saveXML($child))){
						$is_people=false;
					}
				}

				if($is_people){
					$children=$col->item(1)->childNodes;
					foreach($children as $child){
						if($child->ownerDocument->saveXML($child)=="ALL"){
							$is_people=false;
						}
					}
				}

				$id=0;


				if($is_people){


					$i=2;


					$arr=array();
					foreach($col as $temp){
						$elem=$temp->childNodes;
						$arr[$i]=$this->retStatFact($elem);
						$i++;
					}

					$arr[0]=$col->item(0)->childNodes->item(0)->ownerDocument->saveXML($col->item(0)->childNodes->item(0));
					$voto=$col->item(3)->childNodes->item(0)->ownerDocument->saveXML($col->item(3)->childNodes->item(0));

					$voto=str_replace(',','.',$voto);

					if(strpos($voto,'*') !== false){
						$voto=-1;
					}else{
						$voto = floor(floatval($voto) * 2) / 2;
					}
					$arr[1]=$voto;


					$query="REPLACE INTO `stats` (`id_player`,`round`,`vote`,`scored`,`taken`,`free_kick_keeped`,`free_kick_missed`,`free_kick_scored`,`autogol`,`yellow_card`,`red_card`,`assist`,`stop_assist`,`gdv`,`gdp`) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"; //14

					try{
						if (!($stmt = $this->mysqli->prepare($query))) {
						    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
						}

						if (!$stmt->bind_param("iidiiiiiiiiiiii",$arr[0],$round,$arr[1],$arr[6],$arr[7],$arr[8],$arr[9],$arr[10],$arr[11],$arr[12],$arr[13],$arr[14],$arr[15],$arr[16],$arr[17])){
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

			}



		}
		$this->calcRound($round);


	}


	function loadPlayersToDatabase($file,$date_in){
		$dom = new DOMDocument;
		$dom->loadHTMLFile($file);
		$lines=$dom->getElementsByTagName('tr');
		$i=0;
		foreach($lines as $item){
			$enter=true;
			$col=$item->getElementsByTagName('td');
			foreach($col as $k){
				if($k->hasAttribute("colspan")) $enter=false;
			}

			$is_people=false;

			if($enter){
				$html='';
				$children=$col->item(0)->childNodes;
				foreach($children as $child){
					if(is_numeric($child->ownerDocument->saveXML($child))){
						$is_people=true;
					}
				}

				if($is_people){
					$children=$col->item(1)->childNodes;
					foreach($children as $child){
						if($child->ownerDocument->saveXML($child)=="ALL"){
							$is_people=false;
						}
					}
				}

				$id=0;


				if($is_people){


					$i=0;


					$arr=array();
					foreach($col as $temp){
						$elem=$temp->childNodes;
						$arr[$i]=$this->retStatFact($elem);
						$arr[$i]=str_replace(',','.',$arr[$i]);
						if($i==4 || $i==5){
							$arr[$i]=floor($arr[$i]);
						}
						$i++;
					}


					$query="INSERT INTO `players` (`id`,`role`,`name`,`team`,`value`,`first_value`,`diff`,`timestamp`) VALUES(?,?,?,?,?,?,?,?)"; //14

					$date=DateTime::createFromFormat('d-m-Y', $date_in);

					try{
						if (!($stmt = $this->mysqli->prepare($query))) {
						    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
						}

						if (!$stmt->bind_param("isssiiis",$arr[0],$arr[1],$arr[2],$arr[3],$arr[4],$arr[5],$arr[6],$date->format("Y-m-d  H:i:s"))){
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

			}



		}

	}


	function retStatFact($col){
		if($col->item(0)->hasChildNodes()){
			return $this->retStatFact($col->item(0)->childNodes);

		}else{
			return $col->item(0)->ownerDocument->saveXML($col->item(0));
		}
	}

	function calcRound($round){

		$players=$this->dumpSingoliToList(null,null);
		$tempQuery="SELECT stats.* , pla.role  FROM stats LEFT OUTER JOIN (  SELECT *  FROM (SELECT id as t, MAX(timestamp) AS time FROM players GROUP BY t ) l JOIN players b
					ON b.id = l.t AND b.timestamp = l.time GROUP BY b.timestamp, b.id  ) as pla ON pla.id=stats.id_player WHERE stats.round=?";

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
		$stat=$this->dumpStatsByRound($roling[$step]->getPlayer()->getId(),$round);
		$nextSub=false;
		if($stat!=null && !isset($alread_in[$roling[$step]->getPlayer()->getId()])){
			$vote=$stat['final']->getValue();
			if($vote==-1){
				$nextSub=true;
			}else{
				$ret=array('vote'=>$vote,'id'=>$roling[$step]->getPlayer()->getId());
				var_dump($ret);
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

			$tempQuery="SELECT *  FROM users ";
			$config=$this->dumpConfig();

			$max_sub=2;
			if(isset($config['max_sub'])) $max_sub=$config['max_sub'];

			try{

				$players=$this->dumpSingoliToList(null,null);

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

					if($team->getPlayers()==null && $round>1 && $this->getTeam($id_user,$round-1)->getPlayers()!=null){ // SE NON CE SQUADRA QUESTA GIORNATA MA PRECEDENTE SI
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
						foreach($start as $pla){
							$player=$pla->getPlayer();
							$id_player=$player->getId();
							$position=$pla->getPosition();
							$stat=$this->dumpStatsByRound($id_player,$round);

							$enterSub=false;


							if($stat!=null){
								if($stat['final']->getValue()==-1){
									$enterSub=true;
								}else{
									$vote=$stat['final']->getValue();
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
										$result=$result+$subvote;
									}
								}


							}

						}
						$gol=0;

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
		$tempQuery="SELECT * FROM competitions_in_rounds  WHERE id_competition=? ";

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
				$arr[]=$row['round_competition'];
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
				$arr[]=$row['round'];
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
				if($diff>0){
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

			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt->bind_param("sii", $name,$first_round,$num_rounds)) {
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

			$this->mysqli->autocommit(FALSE);

			$tempQuery="REPLACE INTO `competitions` (`id`,`name`,`first_round`,`num_rounds`) VALUES (?,?,?,?)";

			if(!($stmt = $this->mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt->bind_param("isii", $id,$name,$first_round,$num_rounds)) {
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

	function getUsersInCompetition($id_competition){
		$tempQuery="SELECT * FROM users_in_competitions  WHERE id_competition=? ";

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
				$arr[]=$row['id_user'];
			}

			return $arr;

		}catch(exception $e) {
			echo "ex: ".$e;
			return null;

		}
	}

	function getStandings($id_competition){
		$rounds=$this->getRoundsByCompetition($id_competition);
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
		$users=$this->getUsersInCompetition($id_competition);

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

	function getValuesOfPlayer($id_player){
		$tempQuery="SELECT * FROM players  WHERE id=? ORDER BY timestamp ASC ";

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






}

function is_num($number) {
  if(!is_numeric($number)) {
    throw new Exception("Value is not number");
  }
  return true;
}

?>