<?php 

class Transfer{
	private $id_transfer;
	private $user; // User Instance
	private $old_player; // Roster_Player
	private $new_player;
	private $date; // DateTime
    private $id_market;

	public function __construct($id_transfer,$user,$id_market,$old_player,$new_player,$date){
		$this->id_transfer=$id_transfer;
		$this->user=$user;
		$this->old_player=$old_player;
		$this->new_player=$new_player;
		$this->date=$date;
        $this->id_market=$id_market;
	}



    /**
     * Gets the value of id_transfer.
     *
     * @return mixed
     */
    public function getIdTransfer()
    {
        return $this->id_transfer;
    }
    
    /**
     * Sets the value of id_transfer.
     *
     * @param mixed $id_transfer the id  transfer 
     *
     * @return self
     */
    public function setIdTransfer($id_transfer)
    {
        $this->id_transfer = $id_transfer;

        return $this;
    }

    /**
     * Gets the value of user.
     *
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }
    
    /**
     * Sets the value of user.
     *
     * @param mixed $user the user 
     *
     * @return self
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Gets the value of old_player.
     *
     * @return mixed
     */
    public function getOldPlayer()
    {
        return $this->old_player;
    }
    
    /**
     * Sets the value of old_player.
     *
     * @param mixed $old_player the old  player 
     *
     * @return self
     */
    public function setOldPlayer($old_player)
    {
        $this->old_player = $old_player;

        return $this;
    }

    /**
     * Gets the value of new_player.
     *
     * @return mixed
     */
    public function getNewPlayer()
    {
        return $this->new_player;
    }
    
    /**
     * Sets the value of new_player.
     *
     * @param mixed $new_player the new  player 
     *
     * @return self
     */
    public function setNewPlayer($new_player)
    {
        $this->new_player = $new_player;

        return $this;
    }

    /**
     * Gets the value of date.
     *
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }
    
    /**
     * Sets the value of date.
     *
     * @param mixed $date the date 
     *
     * @return self
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    public function getIdMarket(){
        return $this->id_market;
    }

    public function setIdMarket($id){
        $this->id_market=$id;
        return $this;
    }
}

?>