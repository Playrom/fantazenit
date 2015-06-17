<?php
class Team{
	private $id_user;
	private $round;
	private $def;
    private $cen;
    private $att;
	private $players; //TeamPlayerList

	function __construct($id_user,$round,$def,$cen,$att,$players){
		$this->id_user=$id_user;
		$this->round=$round;
		$this->def=$def;
		$this->cen=$cen;
		$this->att=$att;
		$this->players=$players;
	}

	/*
	 * Map Object to Array
	 *
	 * @return String|mixed
	 */
    function map(){
        $arr=array();

        $arr["id_user"] = $this->id_user;
        $arr["round"] = $this->round;
        $arr["def"] = $this->def;
        $arr["cen"] = $this->cen;
        $arr["att"] = $this->att;
        if($this->players!=null) {
            $arr["players"] = $this->players->map();
        }else{
            $arr["players"] = null;
        }

        return $arr;
    }

    function mapOrderedByRole(){
        $arr=array();

        $arr["id_user"] = $this->id_user;
        $arr["round"] = $this->round;
        $arr["def"] = $this->def;
        $arr["cen"] = $this->cen;
        $arr["att"] = $this->att;

        if($this->players!=null) {
            $arr["players"] = $this->getPlayers()->orderByRole();
            $arr["players"]["titolari"] = $arr["players"][0]->map();
            $arr["players"]["panchina"] = $arr["players"][1]->map();
            unset($arr["players"][0]);
            unset($arr["players"][1]);
        }else{
            $arr["players"] = null;
        }

        return $arr;
    }

	

    /**
     * Gets the value of id_user.
     *
     * @return mixed
     */
    public function getIdUser()
    {
        return $this->id_user;
    }
    
    /**
     * Sets the value of id_user.
     *
     * @param mixed $id_user the id  user 
     *
     * @return self
     */
    public function setIdUser($id_user)
    {
        $this->id_user = $id_user;

        return $this;
    }

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

    /**
     * Gets the value of def,$cen,$att.
     *
     * @return mixed
     */
    public function getDef()
    {
        return $this->def;
    }
    
    /**
     * Sets the value of def,$cen,$att.
     *
     * @param mixed $def,$cen,$att the def,$cen,$att 
     *
     * @return self
     */
    public function setDef($def)
    {
        $this->def = $def;

        return $this;
    }

    /**
     * Gets the value of def,$cen,$att.
     *
     * @return mixed
     */
    public function getCen()
    {
        return $this->cen;
    }
    
    /**
     * Sets the value of def,$cen,$att.
     *
     * @param mixed $def,$cen,$att the def,$cen,$att 
     *
     * @return self
     */
    public function setCen($cen)
    {
        $this->cen = $cen;

        return $this;
    }


    /**
     * Gets the value of def,$cen,$att.
     *
     * @return mixed
     */
    public function getAtt()
    {
        return $this->att;
    }
    
    /**
     * Sets the value of def,$cen,$att.
     *
     * @param mixed $def,$cen,$att the def,$cen,$att 
     *
     * @return self
     */
    public function setAtt($att)
    {
        $this->att = $att;

        return $this;
    }



    /**
     * Gets the value of players.
     *
     * @return mixed
     */
    public function getPlayers()
    {
        return $this->players;
    }
    
    /**
     * Sets the value of players.
     *
     * @param mixed $players the players 
     *
     * @return self
     */
    public function _setPlayers($players)
    {
        $this->players = $players;

        return $this;
    }


    
}

?>