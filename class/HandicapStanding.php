<?php

/**
 * Class HandicapStanding
 */
class HandicapStanding extends Handicap{

    /**
     * @var $competition Competition
     */
    protected $competition;

    /**
     * @param Int $id
     * @param User $user
     * @param String $description
     * @param Float $points
     * @param $competition Competition
     */
    function __construct($id,$user,$description,$points,$competition){
        parent::__construct($id,$user,$description,$points);
        $this->competition=$competition;
    }

    /**
     * Map
     *
     * @return String|mixed
     */

    public function map(){
        $arr = parent::map();

        $arr["competition"]=$this->competition->map();

        $arr["type"] = "Competition";

        return $arr;
    }

    /**
     * @return Competition
     */
    public function getCompetition(){
        return $this->competition;
    }

    /**
     * @param $competition Competition
     * @return $this
     */
    public function setCompetition($competition){

        $this->competition=$competition;
        return $this;
    }

}

?>