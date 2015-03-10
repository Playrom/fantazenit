<?php

function createRoster($user,$players,$ids){
		try{

			$tempQuery="SELECT * from `rosters` where id_user=?;";

			if (!($stmt = $mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
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

			if (!($stmt = $mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
			}

			if (!$stmt->bind_param("i", $user->getId())) {
			    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}


			$tempQuery="INSERT INTO `rosters` ( `id_user`, `id_player`, `cost`) VALUES (?,?,?);";

			foreach($ids as $id){


				if (!($stmt = $mysqli->prepare($tempQuery))) {
				    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
				}

				if (!$stmt->bind_param("iii", $user->getId(),$players[$id]->getId(),$players[$id]->getValue())) {
				    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
				}

				if (!$stmt->execute()) {
				    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
				}

				$roster[]=new RosterPlayer($players[$id],$players[$id]->getValue());
				$new_balance=$new_balance-$players[$id]->getValue();
			}

			$modUser="UPDATE users SET balance=? where id=?";

			if (!($stmt = $mysqli->prepare($modUser))) {
			    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
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


	


	function changePlayer($old_player,$new_player,$user,$players,$id_market){
		try{

			////////// DELETE FROM ROSTER

			$delete = "DELETE FROM rosters where id_user=? and id_player=?;";

			if(!($stmt = $mysqli->prepare($delete))) {
			    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
			}

			if (!$stmt->bind_param("ii", $user->getId(),$old_player->getId())) {
			    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			////////// ADD TO ROSTER

			$addRoster = "INSERT INTO `rosters` ( `id_user`, `id_player`, `cost`) VALUES (?,?,?);";

			if(!($stmt = $mysqli->prepare($addRoster))) {
			    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
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

			if(!($stmt = $mysqli->prepare($addTransfer))) {
			    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
			}

			if (!$stmt->bind_param("iiiiii", $user->getId(),$id_market,$new_player->getId(),$old_player->getId(),$new_player->getValue(),$old_player->getValue())) {
			    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			if (!$stmt->execute()) {
			    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}


			$generated_id=$mysqli->insert_id;

			//////// UPDATE BALANCE

			$modUser ="UPDATE users SET balance=? where id=?";

			if(!($stmt = $mysqli->prepare($modUser))) {
			    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
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

			if(!($stmt = $mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
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

			if(!($stmt = $mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
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


    function getOpenMarkets(){
		try{
			$tempQuery="SELECT * FROM `markets` WHERE start_date<? and finish_date>?";
			$now=new DateTime("now");
			$date=$now->format("Y-m-d H:i:s");


			if(!($stmt = $mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
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


			if(!($stmt = $mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
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


			if(!($stmt = $mysqli->prepare($tempQuery))) {
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

			if(!($stmt = $mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
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

			if(!($stmt = $mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
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

			if(!($stmt = $mysqli->prepare($tempQuery))) {
			    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
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
    
    
    ?>