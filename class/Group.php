<?php

/**
 * Class Group
 */
class Group{
    /**
     * @var int $Id_phase
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
     * @var int $id_group
     */
    private $id_group;
    
    /**
     * @var Match[] $matches
     */
    private $matches;
	
	
	
    /**
     * @param int
     * @param int
     * @param int
     * @param string
     * @param string
     * @param Match[]
     */
    function __construct($id_phase,$id_competition,$name,$id_group,$matches){
		$this->id_phase = $id_phase;
		$this->id_competition = $id_competition;
		$this->name=$name;
		$this->id_group = $id_group;
		$this->matches = $matches;
                
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
        $arr["id_group"] = $this->id_group;
        
        if($this->matches != null){
	        foreach ($this->matches as $match){
		        $arr["matches"][] = $match->map();
	        }
        }else{
	        $arr["matches"] = null;
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
     * @return int
     */
    public function getIdGroup(){
	return $this->id_group;
}

    /**
     * @param int
     */
    public function setIdGroup($id_group){
		$this->id_group=$id_group;
	}
	


}