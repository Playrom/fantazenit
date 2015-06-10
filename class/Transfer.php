<?php

/**
 * Class Transfer
 */
class Transfer{
    /**
     * @var $id_transfer Int
     */
    private $id_transfer;
    /**
     * @var $user User
     */
    private $user; // User Instance
    /**
     * @var $old_player RosterPlayer
     */
    private $old_player; // Roster_Player
    /**
     * @var $new_player RosterPlayer
     */
    private $new_player;
    /**
     * @var $date DateTime
     */
    private $date; // DateTime
    /**
     * @var $id_market Int
     */
    private $id_market;

    /**
     * @param $id_transfer Int
     * @param $user User
     * @param $id_market Int
     * @param $old_player RosterPlayer
     * @param $new_player RosterPlayer
     * @param $date DateTime
     */
    public function __construct($id_transfer,$user,$id_market,$old_player,$new_player,$date){
		$this->id_transfer=$id_transfer;
		$this->user=$user;
		$this->old_player=$old_player;
		$this->new_player=$new_player;
		$this->date=$date;
        $this->id_market=$id_market;
	}

    /**
     * Map Object to Array
     *
     * @return string|mixed
     */
    public function map(){
        $arr=array();

        $arr['id_transfer']=$this->id_transfer;
        $arr['id_user']=$this->user->getId();
        $arr['old_player']=$this->old_player->map();
        $arr['new_player']=$this->new_player->map();


        $strDate=strftime("%A %e %B %Y , %H:%M",$this->date->getTimestamp());
        $arr['date']=$strDate;
        $arr['id_market']=$this->id_market;

        return $arr;
    }



    /**
     * Gets the value of id_transfer.
     *
     * @return int
     */
    public function getIdTransfer()
    {
        return $this->id_transfer;
    }
    
    /**
     * Sets the value of id_transfer.
     *
     * @param Int
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
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
    
    /**
     * Sets the value of user.
     *
     * @param User
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
     * @return RosterPlayer
     */
    public function getOldPlayer()
    {
        return $this->old_player;
    }
    
    /**
     * Sets the value of old_player.
     *
     * @param RosterPlayer
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
     * @return RosterPlayer
     */
    public function getNewPlayer()
    {
        return $this->new_player;
    }
    
    /**
     * Sets the value of new_player.
     *
     * @param RosterPlayer
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
     * @return DateTime
     */
    public function getDate()
    {
        return $this->date;
    }
    
    /**
     * Sets the value of date.
     *
     * @param DateTime
     *
     * @return self
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return Int
     */
    public function getIdMarket(){
        return $this->id_market;
    }

    /**
     * @param Int
     * @return self
     */
    public function setIdMarket($id){
        $this->id_market=$id;
        return $this;
    }
}

?>