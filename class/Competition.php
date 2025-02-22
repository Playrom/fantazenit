<?php

/**
 * Class Competition
 */
class Competition{
    /**
     * @var int $Id
     */
    private $id;
    /**
     * @var string $name
     */
    private $name;
    /**
     * @var int $first_round
     */
    private $first_round;
    /**
     * @var int $num_rounds
     */
    private $num_rounds;
	
	/**
     * @var String $type
     */
    private $type;
	
    /**
     * @param int
     * @param string
     * @param int
     * @param int
     * @param string
     */
    function __construct($id,$name,$first_round,$num_rounds,$type){
		$this->id=intval($id);
		$this->name=$name;
		$this->first_round=$first_round;
		$this->num_rounds=$num_rounds;
		$this->type = $type;
                
	}

    /** Map competitions to dictionary
     * 
     * @return String|mixed
     * 
     * */
     
     public function map(){
        $arr = array();

        $arr["id"] = $this->id;
        $arr["name"] = $this->name;
        $arr["first_round"] = $this->first_round;
        $arr["num_rounds"] = $this->num_rounds;
        $arr["type"] = $this->type;

        return $arr;

     }


    /**
     * 
     * @return int
     */
    public function getId(){
    return $this->id;
}

    /**
     * @return String
     */
    public function getName(){
    return $this->name;
}


    /**
     * @param $value int
     */
    public function setId($value){
    $this->id = $value;
}

    /**
     * @param $value String
     */
    public function setName($value){
    $this->name = $value;
}

    /**
     * @return int
     */
    public function getFirstRound(){
	return $this->first_round;
}

    /**
     * @param int
     */
    public function setFirstRound($first_round){
	$this->first_round=$first_round;
}

    /**
     * @return int
     */
    public function getNumRounds(){
	return $this->num_rounds;
}

    /**
     * @param int
     */
    public function setNumRounds($num_rounds){
		$this->num_rounds=$num_rounds;
	}
	
	/**
     * @return String
     */
    public function getTypeCompetition(){
    return $this->type;
}


    
    /**
     * @param $value String
     */
    public function setTypeCompetition($value){
    $this->type = $value;
}
	


}