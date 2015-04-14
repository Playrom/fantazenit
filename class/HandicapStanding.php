<?php

class HandicapStanding extends Handicap{

	protected $competition;

	function __construct($id,$user,$description,$points,$competition){
        parent::__construct($id,$user,$description,$points);
        $this->competition=$competition;
    }

    public function getCompetition(){
        return $this->competition;
    }

    public function setCompetition($competition){

        $this->competition=$competition;
        return $this;
    }

}

?>