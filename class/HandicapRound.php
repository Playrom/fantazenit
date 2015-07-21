<?php

/**
 * Class HandicapRound
 */
class HandicapRound extends Handicap{

    /**
     * @var $id_round Int
     */
    protected $id_round;

    /**
     * @param Int $id
     * @param User $user
     * @param String $description
     * @param Float $points
     * @param $id_round Int
     */
    function __construct($id,$user,$description,$points,$id_round){
        parent::__construct($id,$user,$description,$points);
        $this->id_round=$id_round;
    }

    /**
     * Map
     *
     * @return String|mixed
     */

    public function map(){
        $arr = parent::map();

        $arr["id_round"] = $this->id_round;
        $arr["type"] = "round";

        return $arr;
    }

    /**
     * @return Int
     */
    public function getRound(){
        return $this->id_round;
    }

    /**
     * @param $id Int
     * @return $this
     */
    public function setRound($id){
        $this->id_round=$id;
        return $this;
    }

}

?>