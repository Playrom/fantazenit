<?php

class ConnectDatabaseUsers extends ConnectDatabase{
    function signupUser(User $user){
	    
		
		$query="select * from `users` where email LIKE ?";
		$queryInsert="insert into `users`( `name`, `surname`, `username`, `password`, `email`,`balance`,`name_team`,`telephone`,`url_fb`) VALUES (?,?,?,?,?,?,?,?,?)";
		try{
			if (!($stmt = $this->mysqli->prepare($query))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}
			
			
			$email = strtolower($user->getEmail());

			if (!$stmt->bind_param("s", $email)) {
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
			
			
			$name = $user->getName();
			$surname = $user->getSurname();
			$username = $user->getUsername();
			$password = $user->getPassword();
			$email = strtolower($user->getEmail());
			$balance =  intval($user->getBalance());
			$name_team = $user->getNameTeam();
			$telephone = $user->getTelephone();
			$url_fb = $user->getUrlFb();
			
		
			if (!$stmt->bind_param("sssssisss", $name,$surname, $username, $password ,$email , $balance , $name_team , $telephone , $url_fb)) {
			    //echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			    return false;
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
        
    function setApikey($username,$apiKey){
        

        $query="UPDATE `users` SET apiKey=? where username LIKE ?";
        try{
                if (!($stmt = $this->mysqli->prepare($query))) {
                    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
                }

                if (!$stmt->bind_param("ss", $apiKey , $username)) {
                    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
                }

                if (!$stmt->execute()) {
                    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
                }

                return true;

        }catch(exception $e) {
                echo "\nERRORE MODIFICA API KEY: ".$e;
                return false;
        }

        return true;

    }
        
        
    function checkApi($apiKey){

        $query="select * from `users` where `apiKey`=?";
        
        try{
            if (!($stmt = $this->mysqli->prepare($query))) {
                echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
            }

            if (!$stmt->bind_param("s", $apiKey)) {
                echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
            }

            if (!$stmt->execute()) {
                echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
            }

            $res=$stmt->get_result();
            $res->data_seek(0);
            while ($row = $res->fetch_assoc()) {
                return true;
            }
            
            $user = $this->getUserByApiKey($apiKey);
            

        }catch(exception $e) {
            echo $e;
            return false;
        }

        return false;

}

/**
* A method that return true if the api is an api valid for an Admin User
* @param String $apiKey
* @return boolean
*/

function checkAuthOverride($apiKey){
	$query="select * from `users` where `apiKey`=?";
        
        try{
            if (!($stmt = $this->mysqli->prepare($query))) {
                echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
            }

            if (!$stmt->bind_param("s", $apiKey)) {
                echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
            }

            if (!$stmt->execute()) {
                echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
            }

            $res=$stmt->get_result();
            $res->data_seek(0);
            while ($row = $res->fetch_assoc()) {
                if($row['auth'] >= 1 ){
	                return true;
                }else{
	                return false;
                }
            }
            
            
            

        }catch(exception $e) {
            echo $e;
            return false;
        }

        return false;
}

    /*
     * Get user By Api Key
     *
     * @param $apiKey String
     *
     * @return User
     */

    function getUserByApiKey($apiKey){

        $query="select * from `users` where `apiKey`=?";

        try{
            if (!($stmt = $this->mysqli->prepare($query))) {
                echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
            }

            if (!$stmt->bind_param("s", $apiKey)) {
                echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
            }

            if (!$stmt->execute()) {
                echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
            }

            $res=$stmt->get_result();
            $res->data_seek(0);
            while ($row = $res->fetch_assoc()) {
                return $this->getUserById($row['id']);
                
            }

        }catch(exception $e) {
            echo $e;
            return false;
        }

        return false;

    }
    
    function getUsers(){

		$query="select * , UNIX_TIMESTAMP(reg_date) as time from `users` order by name_team ASC";

		$data_players=new ConnectDatabasePlayers($this->mysqli);
		$data_markets=new ConnectDatabaseMarkets($this->mysqli);
		$data_handicaps = new ConnectDatabaseHandicaps($this->mysqli);


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

                $apiKey=$row['apiKey'];
                $url_avatar=$row['url_avatar'];
                
                
				$datetemp = date ("Y-m-d H:i:s", $row['time']);
				$date=new DateTime($datetemp);
				

				


				$us=new User($id,$username,$name,$surname,$password,$email,$date,$auth,$balance,null,array(),$name_team,$telephone,$url_fb,$apiKey,$url_avatar);
                //$data_markets->getTransfers($us);
                
                $bonuses = $data_handicaps->getMoneyBonusesByUser($us);
				
				if($bonuses!=null){
					foreach($bonuses as $bonus){
						$val = intval($bonus->getPoints());
						$balance = $balance + $val;
					}
				}
				
				$us->setBalance($balance);
				                
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
		$data_handicaps = new ConnectDatabaseHandicaps($this->mysqli);

		$query="select * , UNIX_TIMESTAMP(reg_date) as time from `users` where email LIKE ?";
		try{
			if (!($stmt = $this->mysqli->prepare($query))) {
			    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
			}
			
			$email = strtolower($email);

			if (!$stmt->bind_param("s", $email)) {
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
	            
                $url_avatar=$row['url_avatar'];
	            
	            $apiKey=$row['apiKey'];

				$datetemp = date ("Y-m-d H:i:s", $row['time']);
				$date=new DateTime($datetemp);
				
				
				$res2=$this->mysqli->query("select * from `rosters` where id_user=".$id);
				$roster=new RosterList();
				while ($row2 = $res2->fetch_assoc()) {
					$cost=$row2['cost'];
					$player=$data_players->dumpPlayer(intval($row2['id_player']));
					$roster[]=new RosterPlayer($player,$cost);
				}


				$user = new User($id,$username,$name,$surname,$password,$email,$date,$auth,$balance,$roster,array(),$name_team,$telephone,$url_fb,$apiKey,$url_avatar);
				
				$bonuses = $data_handicaps->getMoneyBonusesByUser($user);
				
				if($bonuses!=null){
					foreach($bonuses as $bonus){
						$val = intval($bonus->getPoints());
						$balance = $balance + $val;
					}
				}
				
				$user->setBalance($balance);
				
				return $user;
			}



		}catch(exception $e) {
			echo "\nERRORE DUMP USER BY EMAIL: ".$e;
			return null;
		}

		return null;

	}


	function getUserByUsername($username){

		$data_players=new ConnectDatabasePlayers($this->mysqli);
		$data_handicaps = new ConnectDatabaseHandicaps($this->mysqli);

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
                
                $apiKey=$row['apiKey'];
                
                $url_avatar=$row['url_avatar'];

				$datetemp = date ("Y-m-d H:i:s", $row['time']);
				$date=new DateTime($datetemp);
				

				$res2=$this->mysqli->query("select * from `rosters` where id_user=".$id);
				$roster=new RosterList();
				while ($row2 = $res2->fetch_assoc()) {
					$cost=$row2['cost'];
					$player=$data_players->dumpPlayer(intval($row2['id_player']));
					$roster[]=new RosterPlayer($player,$cost);
				}				
				$user = new User($id,$username,$name,$surname,$password,$email,$date,$auth,$balance,$roster,array(),$name_team,$telephone,$url_fb,$apiKey,$url_avatar);
				
				$bonuses = $data_handicaps->getMoneyBonusesByUser($user);
				
				if($bonuses!=null){
					foreach($bonuses as $bonus){
						$val = intval($bonus->getPoints());
						$balance = $balance + $val;
					}
				}
				
				$user->setBalance($balance);
				
				return $user;
			}



		}catch(exception $e) {
			echo "\nERRORE DUMP USER BY NAME: ".$e;
			return null;
		}

		return null;

	}

	function getUserById($id){
		
		
		$data_players=new ConnectDatabasePlayers($this->mysqli);
		$data_handicaps = new ConnectDatabaseHandicaps($this->mysqli);
		
		$query="select *,UNIX_TIMESTAMP(reg_date) as time from `users` where id=? ; ";
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
                
                $apiKey=$row['apiKey'];
                
                $url_avatar=$row['url_avatar'];

				$datetemp = date ("Y-m-d H:i:s", $row['time']);
				$date=new DateTime($datetemp);
				
				
								

				$res2=$this->mysqli->query("select * from `rosters` where id_user=".$id);

				$roster=new RosterList();
				while ($row2 = $res2->fetch_assoc()) {
					$cost=$row2['cost'];
					$player=$data_players->dumpPlayerByIdNoStats(intval($row2['id_player']));
					$roster[]=new RosterPlayer($player,$cost);
				}
				

				$user = new User($id,$username,$name,$surname,$password,$email,$date,$auth,$balance,$roster,array(),$name_team,$telephone,$url_fb,$apiKey,$url_avatar);
				
				$bonuses = $data_handicaps->getMoneyBonusesByUser($user);
								
				if($bonuses!=null){
					foreach($bonuses as $bonus){
						$val = intval($bonus->getPoints());
						$balance = $balance + $val;
					}
				}
				
				$user->setBalance($balance);
				
				return $user;

			}



		}catch(exception $e) {
			echo "\nERRORE DUMP USER BY ID: ".$e;
			return null;
		}

		return null;

	}
	
	
	function editUser($id,$password,$email,$url_fb,$name_team,$url_avatar){
		
		
		try{
			
			if($password!=null && $password!=""){
				$query="UPDATE `users` SET password=? where id=? ; ";

				if (!($stmt = $this->mysqli->prepare($query))) {
				    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
				    return false;
				}
	
				if (!$stmt->bind_param("si", $password , $id)) {
				    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
				    return false;
				}
	
				if (!$stmt->execute()) {
				    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
				    return false;
				}

			}
			
			if($email!=null && $email!=""){
				$query="UPDATE `users` SET email=? where id=? ; ";

				if (!($stmt = $this->mysqli->prepare($query))) {
				    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
				    return false;
				}
	
				if (!$stmt->bind_param("si", $email , $id)) {
				    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
				    return false;
				}
	
				if (!$stmt->execute()) {
				    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
				    return false;
				}

			}
			
			if($url_fb!=null && $url_fb!=""){
				$query="UPDATE `users` SET url_fb=? where id=? ; ";

				if (!($stmt = $this->mysqli->prepare($query))) {
				    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
				    return false;
				}
	
				if (!$stmt->bind_param("si", $url_fb , $id)) {
				    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
				    return false;
				}
	
				if (!$stmt->execute()) {
				    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
				    return false;
				}

			}
			
			if($name_team!=null && $name_team!=""){
				$query="UPDATE `users` SET name_team=? where id=? ; ";

				if (!($stmt = $this->mysqli->prepare($query))) {
				    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
				    return false;
				}
	
				if (!$stmt->bind_param("si", $name_team , $id)) {
				    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
				    return false;
				}
	
				if (!$stmt->execute()) {
				    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
				    return false;
				}

			}
			
			if($url_avatar!=null && $url_avatar!=""){
				$query="UPDATE `users` SET url_avatar=? where id=? ; ";

				if (!($stmt = $this->mysqli->prepare($query))) {
				    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
				    return false;
				}
	
				if (!$stmt->bind_param("si", $url_avatar , $id)) {
				    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
				    return false;
				}
	
				if (!$stmt->execute()) {
				    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
				    return false;
				}

			}
			
			
			return true;



		}catch(exception $e) {
			echo "\nERRORE EDIT USER ID: ".$e;
			return false;
		}

		return false;

	}
	
	
	
}
    
    ?>