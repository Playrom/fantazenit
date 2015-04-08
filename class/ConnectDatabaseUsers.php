<?php

class ConnectDatabaseUsers extends ConnectDatabase{
    function signupUser(User $user){

		$query="select * from `users` where email LIKE ?";
		$queryInsert="insert into `users`( `name`, `surname`, `username`, `password`, `email`,`balance`,`name_team`,`telephone`,`url_fb`) VALUES (?,?,?,?,?,?,?,?,?)";
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

			if (!$stmt->bind_param("sssssisss", $user->getName(),$user->getSurname(),$user->getUsername(),$user->getPassword(),strtolower($user->getEmail()),$user->getBalance(),$user->getNameTeam(),$user->getTelephone(),$user->getUrlFb())) {
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
    
    function getUsers(){

		$query="select * , UNIX_TIMESTAMP(reg_date) as time from `users` order by username ASC";

		$data_players=new ConnectDatabasePlayers($this->mysqli);
		$data_markets=new ConnectDatabaseMarkets($this->mysqli);

		try{
			if (!($stmt = $this->mysqli->prepare($query))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			$res=$stmt->get_result();
			$res->data_seek(0);
            $users=array();
            
			while ($row = $res->fetch_assoc()) {

				$id=$row['id'];
				$name=$row['name'];
				$surname=$row['surname'];
				$username=$row['username'];
				$auth=$row['auth'];
				$balance=$row['balance'];
				$password=$row['password'];
				$name_team=$row['name_team'];
				$email=$row['email'];
                $telephone=$row['telephone'];
                $url_fb=$row['url_fb'];
                
                
				$datetemp = date ("Y-m-d H:i:s", $row['time']);
				$date=new DateTime($datetemp);

				$res2=$this->mysqli->query("select * from `rosters` where id_user=".$id);
				$roster=new RosterList();
				while ($row2 = $res2->fetch_assoc()) {
					$cost=$row2['cost'];
					$player=$data_players->dumpPlayer(intval($row2['id_player']));
					$roster[]=new RosterPlayer($player,$cost);
				}


				$us=new User($id,$username,$name,$surname,$password,$email,$date,$auth,$balance,$roster,NULL,$name_team,$telephone,$url_fb);
                $data_markets->getTransfers($us,$this->dumpSingoliToList(null,null));
                $users[]=$us;
            }
            return $users;



		}catch(exception $e) {
			echo "\nERRORE DUMP USER BY EMAIL: ".$e;
			return null;
		}

		return null;

	}


	function getUserByEmail($email){

		$data_players=new ConnectDatabasePlayers($this->mysqli);

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
                $telephone=$row['telephone'];
                $url_fb=$row['url_fb'];

				$datetemp = date ("Y-m-d H:i:s", $row['time']);
				$date=new DateTime($datetemp);

				$res2=$this->mysqli->query("select * from `rosters` where id_user=".$id);
				$roster=new RosterList();
				while ($row2 = $res2->fetch_assoc()) {
					$cost=$row2['cost'];
					$player=$data_players->dumpPlayer(intval($row2['id_player']));
					$roster[]=new RosterPlayer($player,$cost);
				}


				return new User($id,$username,$name,$surname,$password,$email,$date,$auth,$balance,$roster,NULL,$name_team,$telephone,$url_fb);
			}



		}catch(exception $e) {
			echo "\nERRORE DUMP USER BY EMAIL: ".$e;
			return null;
		}

		return null;

	}


	function getUserByUsername($username){

		$data_players=new ConnectDatabasePlayers($this->mysqli);

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
                $telephone=$row['telephone'];
                $url_fb=$row['url_fb'];

				$datetemp = date ("Y-m-d H:i:s", $row['time']);
				$date=new DateTime($datetemp);

				$res2=$this->mysqli->query("select * from `rosters` where id_user=".$id);
				$roster=new RosterList();
				while ($row2 = $res2->fetch_assoc()) {
					$cost=$row2['cost'];
					$player=$data_players->dumpPlayer(intval($row2['id_player']));
					$roster[]=new RosterPlayer($player,$cost);
				}

				return new User($id,$username,$name,$surname,$password,$email,$date,$auth,$balance,$roster,NULL,$name_team,$telephone,$url_fb);
			}



		}catch(exception $e) {
			echo "\nERRORE DUMP USER BY NAME: ".$e;
			return null;
		}

		return null;

	}

	function getUserById($id){

		$data_players=new ConnectDatabasePlayers($this->mysqli);

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
                $telephone=$row['telephone'];
                $url_fb=$row['url_fb'];

				$datetemp = date ("Y-m-d H:i:s", $row['time']);
				$date=new DateTime($datetemp);

				$res2=$this->mysqli->query("select * from `rosters` where id_user=".$id);
				$roster=new RosterList();
				while ($row2 = $res2->fetch_assoc()) {
					$cost=$row2['cost'];
					$player=$data_players->dumpPlayer(intval($row2['id_player']));
					$roster[]=new RosterPlayer($player,$cost);
				}

				return new User($id,$username,$name,$surname,$password,$email,$date,$auth,$balance,$roster,NULL,$name_team,$telephone,$url_fb);
			}



		}catch(exception $e) {
			echo "\nERRORE DUMP USER BY ID: ".$e;
			return null;
		}

		return null;

	}
}
    
    ?>