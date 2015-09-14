<?php

/**
 * Class Match
 */
class Match{
    /**
     * @var int $d_phase
     */
    private $id_phase;
    /**
     * @var int $id_match
     */
    private $id_match;
    /**
     * @var int $id_competition
     */
    private $id_competition;
    
    /**
     * @var int $id_one
     */
    private $id_one;
	
	/**
     * @var int $id_two
     */
    private $id_two;
	
	/**
     * @var int $round
     */
    private $round;
	
	/**
     * @var String $result 
     * 1X2
     */
    private $result;
	
	/**
     * @var int $id_group
     */
    private $id_group;
	
	
    /**
     * @param int
     * @param int
     * @param int
     * @param int
     * @param int
     * @param int
     * @param string
     * @param int
     */
    function __construct($id_phase,$id_match , $id_competition,$id_one , $id_two , $round , $result , $id_group){
		$this->id_phase = $id_phase;
		$this->id_match = $id_match;
		$this->id_competition = $id_competition;
		$this->id_one = $id_one;
		$this->id_two = $id_two;
		$this->round = $round;
		$this->result = $result;
		$this->id_group = $id_group;
                
	}

    /** Map competitions to dictionary
     * 
     * @return String|mixed
     * 
     * */
     
     public function map(){
        $arr = array();

        $arr["id_phase"] = $this->id_phase;
        $arr["id_match"] = $this->id_match;
        $arr["id_competition"] = $this->id_competition;
        $arr["id_one"] = $this->id_one;
        $arr["id_two"] = $this->id_two;
        $arr["round"] = $this->round;
        $arr["result"] = $this->result;
        $arr["id_group"] = $this->id_group;

        return $arr;

     }


    /**
     * 
     * @return int
     */
    public function getIdPhase(){
	    return $this->id_phase;
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
    public function setIdPhase($value){
	    $this->id_phase = $value;
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
    public function getIdCompetition(){
		return $this->id_competition;
	}

    /**
     * @param int
     */
    public function setIdCompetition($first_round){
		$this->id_competition=$first_round;
	}

    /**
     * @return String
     */
    public function getSettings(){
		return $this->settings;
	}

    /**
     * @param string
     */
    public function setSettings($settings){
		$this->settings=$settings;
	}
	
	/**
     * @return String
     */
    public function getCompetitionType(){
	    return $this->type;
	}


    
    /**
     * @param $value String
     */
    public function setCompetitionType($value){
	    $this->type = $value;
	}

/**
     * @return Groups[]
     */
    public function getGroups(){
	    return $this->groups;
	}


    
    /**
     * @param $value Groups[]
     */
    public function setType($value){
	    $this->groups = $value;
	}
	
	/**
     * @return int
     */
    public function getIdOne(){
		return $this->id_one;
	}

    /**
     * @param int
     */
    public function setIdOne($id){
		$this->id_one = $id;
	}
	
	
	/**
     * @return int
     */
    public function getIdTwo(){
		return $this->id_two;
	}

    /**
     * @param int
     */
    public function setIdTwo($id){
		$this->id_two = $id;
	}
	
	/**
     * @return int
     */
    public function getId(){
		return $this->id_match;
	}

    /**
     * @param int
     */
    public function setId($id){
		$this->id_match = $id;
	}


}