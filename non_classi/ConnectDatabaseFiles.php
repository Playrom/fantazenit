<?php


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
						$arr[$i]=retStatFact($elem);
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
						if (!($stmt = $mysqli->prepare($query))) {
						    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
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
		calcRound($round);


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
						$arr[$i]=retStatFact($elem);
						$arr[$i]=str_replace(',','.',$arr[$i]);
						if($i==4 || $i==5){
							$arr[$i]=floor($arr[$i]);
						}
						$i++;
					}


					$query="INSERT INTO `players` (`id`,`role`,`name`,`team`,`value`,`first_value`,`diff`,`timestamp`) VALUES(?,?,?,?,?,?,?,?)"; //14

					$date=DateTime::createFromFormat('d-m-Y', $date_in);

					try{
						if (!($stmt = $mysqli->prepare($query))) {
						    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
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
			return retStatFact($col->item(0)->childNodes);

		}else{
			return $col->item(0)->ownerDocument->saveXML($col->item(0));
		}
	}
    
    
    ?>