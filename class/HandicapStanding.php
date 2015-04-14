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
<<<<<<< HEAD
        $this->competition=$competition->getId();
=======
        $this->competition=$id;
>>>>>>> origin/master
        return $this;
    }

}

?>