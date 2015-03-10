<?php

class PlayersList extends ArrayObject{

	public function __construct($array = array()){ 
    	parent::__construct($array, ArrayObject::ARRAY_AS_PROPS);
 	}

	public function getPlayer($name){
		foreach($this as $player){
			if(strtolower($player->getName())==strtolower($name)) return $player;
		}
		return NULL;
	}
}

/*$arr= array(

	'50' => new Player(50,'ROMANO','INTER','C','50','45.9','4'),
	'78' => new Player(78,'Miao Miao','INTER','C','70','45.9','4'),
	 );

var_dump($arr);

echo "\n\n\n";

$players=new PlayersList($arr);
var_dump($players);

var_dump($players->getPlayer('ROMANO'));*/

?>