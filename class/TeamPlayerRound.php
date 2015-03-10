<?php

class TeamPlayerRound{
	private $player; //Player Instance
	private $position;

	public function __construct($player,$position){
		$this->player=$player;
		$this->position=$position;

 	}



    /**
     * Gets the value of player.
     *
     * @return mixed
     */
    public function getPlayer()
    {
        return $this->player;
    }
    
    /**
     * Sets the value of player.
     *
     * @param mixed $player the player 
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
     * @return mixed
     */
    public function getPosition()
    {
        return $this->position;
    }
    
    /**
     * Sets the value of position.
     *
     * @param mixed $position the position 
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