<?php

abstract class Handicap{

	// POINTS NEGATIVI INDICANO PENALIZZAZIONE , POSITIVI INDICANO PREMIO

	protected $id;
	protected $user;
	protected $description;
	protected $points;

	function __construct($id,$user,$description,$points){
		$this->id=$id;
		$this->user=$user;
		$this->description=$description;
		$this->points=$points;
	}
	
	public function getId(){
        return $this->id;
    }

    public function setId($id){
        $this->id=$id;
        return $this;
    }


    public function getUser(){
        return $this->user;
    }

    public function setUser($user){
        $this->user=$user;
        return $this;
    }

    public function getDescription(){
        return $this->description;
    }

    public function setDescription($description){
        $this->description=$description;
        return $this;
    }

    public function getPoints(){
        return $this->points;
    }

    public function setPoints($points){
        $this->points=$points;
        return $this;
    }



}

?>