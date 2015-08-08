<?php

/**
 * Class Phase
 */
class Phase{
    /**
     * @var int $id_phase
     */
    private $id_phase;
    /**
     * @var string $name
     */
    private $name;
    /**
     * @var int $id_competition
     */
    private $id_competition;
    /**
     * @var string $type
     */
    private $type;
	
	/**
     * @var String $settings
     */
    private $settings;
    
    /**
     * @var Group[] $groups
     */
    private $groups;
	
    /**
     * @param int
     * @param int
     * @param string
     * @param string
     * @param string
     */
    function __construct($id_phase,$id_competition,$name,$type,$settings,$groups){
		$this->id_phase = $id_phase;
		$this->id_competition = $id_competition;
		$this->name=$name;
		$this->type = $type;
		$this->settings = $settings;
		$this->groups = $groups;
                
	}

    /** Map competitions to dictionary
     * 
     * @return String|mixed
     * 
     * */
     
     public function map(){
        $arr = array();

        $arr["id_phase"] = $this->id_phase;
        $arr["name"] = $this->name;
        $arr["id_competition"] = $this->id_competition;
        $arr["settings"] = json_decode($this->settings,true);
        $arr["type"] = $this->type;
        foreach($this->groups as $group){
	        $arr["groups"][] = $group->map();
        }

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
	


}