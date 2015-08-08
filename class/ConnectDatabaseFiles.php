<?php


class ConnectDatabaseFiles extends ConnectDatabase{
    
    function loadStatsToDatabase($round,$file){

    	$database_rounds = new ConnectDatabaseRounds($this->mysqli);

		$dom = new DOMDocument;
		$dom->loadHTMLFile($file);

		$html=$dom->getElementsByTagName('html');

		foreach($html as $item){
			if($item->hasAttribute("xmlns")) return false;
		}

		$lines=$dom->getElementsByTagName('tr');
		$i=0;
		
		$not_same_year = false;
		
		foreach($lines as $item){
			
			

			$enter=true;
			$col=$item->getElementsByTagName('td');
			foreach($col as $k){
				if($k->hasAttribute("colspan")) {
					$enter=false;
					
					if($col->item(0)!=null){
						$children=$col->item(0)->childNodes;
						foreach($children as $child){
							if(strpos($child->ownerDocument->saveXML($child),"VOTI") == true){
								if(strpos($child->ownerDocument->saveXML($child),"2014") == false){
									$not_same_year = true;
								}
							}
						}
					}
					
				}
			}

			$is_people=true;

			if($enter && !$not_same_year){
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
		$database_rounds->calcRound($round);

		return true;


	}


	function loadPlayersToDatabase($file,$round){
		$dom = new DOMDocument;
		$dom->loadHTMLFile($file);
		
		$html=$dom->getElementsByTagName('html');
		
		foreach($html as $item){
			if($item->hasAttribute("xmlns")) return false;
		}

		$lines=$dom->getElementsByTagName('tr');
		$i=0;
		
		$not_same_year = false;
		
		foreach($lines as $item){
			$enter=true;
			$col=$item->getElementsByTagName('td');
			foreach($col as $k){
				if($k->hasAttribute("colspan")) {
					$enter=false;
					
					if($col->item(0)!=null){
						$children=$col->item(0)->childNodes;
						foreach($children as $child){
							if(strpos($child->ownerDocument->saveXML($child),"QUOTAZIONI") == true){
								if(strpos($child->ownerDocument->saveXML($child),"2014") == false){
									$not_same_year = true;
								}
							}
						}
					}
					
					
				}
			}
			

			$is_people=false;

			if($enter && !$not_same_year){
				$html='';
				
				if($col->item(0)!=null){
					$children=$col->item(0)->childNodes;
					foreach($children as $child){
						if(is_numeric($child->ownerDocument->saveXML($child))){
							$is_people=true;
						}
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


					$query="REPLACE INTO `players` (`id`,`role`,`name`,`team`,`value`,`first_value`,`diff`,`round`) VALUES(?,?,?,?,?,?,?,?)"; //14


					try{
						if (!($stmt = $this->mysqli->prepare($query))) {
						    echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
						}

						if (!$stmt->bind_param("isssiiis",$arr[0],$arr[1],$arr[2],$arr[3],$arr[4],$arr[5],$arr[6],$round)){
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

		return true;

	}


	function retStatFact($col){
		if($col->item(0)->hasChildNodes()){
			return $this->retStatFact($col->item(0)->childNodes);

		}else{
			return $col->item(0)->ownerDocument->saveXML($col->item(0));
		}
	}
    
}
    
    ?>