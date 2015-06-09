<?php 

class RosterPlayer{
    /**
     * @var Player  $player
     */
    private $player;
    /**
     * @var int cost
     */
    private $cost;

	function __construct($player,$cost){
		$this->player=$player;
		$this->cost=$cost;
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
     * @param $player Player
     *
     * @return self
     */
    public function setPlayer($player)
    {
        $this->player = $player;

        return $this;
    }

    /**
     * Gets the value of cost.
     *
     * @return mixed
     */
    public function getCost()
    {
        return $this->cost;
    }
    
    /**
     * Sets the value of cost.
     *
     * @param mixed $cost the cost 
     *
     * @return self
     */
    public function setCost($cost)
    {
        $this->cost = $cost;

        return $this;
    }
}