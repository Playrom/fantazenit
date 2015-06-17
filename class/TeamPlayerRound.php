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

	public function __construct($player,$position){
		$this->player=$player;
		$this->position=$position;

 	}

    /**
     * Map the object
     */

    function map(){
        $arr=array();

        $arr["player"]=$this->player->map();
        $arr["position"]=$this->position;

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