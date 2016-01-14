<?php

class TeamPlayerRound{
    /**
     * @var Player $player
     */
	private $player; //Player Instance
    /**
     * @var Int $position
     */
	private $position;
	
	/**
	* @var StatisticsCollection $stats
	**/
	
	private $stats;

	public function __construct($player,$position,$stats){
		$this->player=$player;
		$this->position=$position;
		$this->stats = $stats;

 	}

    /**
     * Map the object
     */

    function map(){
        $arr=array();

        $arr["player"]=$this->player->map();
        $arr["position"]=$this->position;
        if($this->stats != null){
	        $arr["stats"] = $this->stats->map();
	    }else{
		    $arr["stats"] = null;
	    }

        return $arr;
    }

    /**
     * Map the object by ROUND
     */

    function mapByRound($round){
        $arr=array();

        $arr["player"]=$this->player->mapByRound($round);
        $arr["position"]=$this->position;
        
        if($this->stats != null){
	        $arr["stats"] = $this->stats->map();
	    }else{
		    $arr["stats"] = null;
	    }

        return $arr;
    }




    /**
     * Gets the value of player.
     *
     * @return Player
     */
    public function getPlayer()
    {
        return $this->player;
    }
    
    /**
     * Sets the value of player.
     *
     * @param Player $player
     *
     * @return self
     */
    public function setPlayer($player)
    {
        $this->player = $player;

        return $this;
    }

    /**
     * Gets the value of position.
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }
    
    /**
     * Sets the value of position.
     *
     * @param int
     *
     * @return self
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }
}

?>