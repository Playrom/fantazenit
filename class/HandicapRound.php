<?php

class HandicapRound extends Handicap{

	protected $id_round;

    function __construct($id,$user,$description,$points,$id_round){
        parent::__construct($id,$user,$description,$points);
        $this->id_round=$id_round;
    }

    public function getRound(){
        return $this->id_round;
    }

    public function setRound($id){
        $this->id_round=$id;
        return $this;
    }

}

?>