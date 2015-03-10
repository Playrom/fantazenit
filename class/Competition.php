<?php

class Competition{
	private $id;
	private $name;
	private $first_round;
	private $num_rounds;

	function __construct($id,$name,$first_round,$num_rounds){
		$this->id=$id;
		$this->name=$name;
		$this->first_round=$first_round;
		$this->num_rounds=$num_rounds;
	}


public function getId(){
    return $this->id;
}
public function getName(){
    return $this->name;
}


public function setId($value){
    $this->id = $value;
}
public function setName($value){
    $this->name = $value;
}

public function getFirstRound(){
	return $this->first_round;
}

public function setFirstRound($first_round){
	$this->first_round=$first_round;
}

public function getNumRounds(){
	return $this->num_rounds;
}

public function setNumRounds($num_rounds){
	$this->num_rounds=$num_rounds;
}

}