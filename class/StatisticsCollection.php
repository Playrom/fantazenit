<?php 

class StatisticsCollection extends ArrayObject{
	public function __construct($array = array()){ 
    	parent::__construct($array, ArrayObject::ARRAY_AS_PROPS);
 	}
 	
 	private $round;



    /**
     * Gets the value of round.
     *
     * @return mixed
     */
    public function getRound()
    {
        return $this->round;
    }
    
    /**
     * Sets the value of round.
     *
     * @param mixed $round the round 
     *
     * @return self
     */
    public function setRound($round)
    {
        $this->round = $round;

        return $this;
    }
}

?>