<?php

function signupUser(User $user){

		$query="select * from `users` where email LIKE ?";
		$queryInsert="insert into `users`( `name`, `surname`, `username`, `password`, `email`,`balance`,`name_team`) VALUES (?,?,?,?,?,?,?)";
		try{
			if (!($stmt = $mysqli->prepare($query))) {
			    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
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




			if (!($stmt = $mysqli->prepare($queryInsert))) {
			    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
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
			if (!($stmt = $mysqli->prepare($query))) {
			    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
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

				$res2=$mysqli->query("select * from `rosters` where id_user=".$id);
				$roster=new RosterList();
				while ($row2 = $res2->fetch_assoc()) {
					$cost=$row2['cost'];
					$player=dumpPlayer($row2['id_player']);
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
			if (!($stmt = $mysqli->prepare($query))) {
			    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
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

				$res2=$mysqli->query("select * from `rosters` where id_user=".$id);
				$roster=new RosterList();
				while ($row2 = $res2->fetch_assoc()) {
					$cost=$row2['cost'];
					$player=dumpPlayer(intval($row2['id_player']));
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

				$res2=$mysqli->query("select * from `rosters` where id_user=".$id);
				$roster=new RosterList();
				while ($row2 = $res2->fetch_assoc()) {
					$cost=$row2['cost'];
					$player=dumpPlayer(intval($row2['id_player']));
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
    
    ?>