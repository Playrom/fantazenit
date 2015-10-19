<?php

/**
 * Class Handicap
 */
class Handicap{

	// POINTS NEGATIVI INDICANO PENALIZZAZIONE , POSITIVI INDICANO PREMIO

    /**
     * @var $id Int
     */
    protected $id;
    /**
     * @var $user User
     */
    protected $user;
    /**
     * @var $description String
     */
    protected $description;
    /**
     * @var $points Float
     */
    protected $points;

    /**
     * @param $id Int
     * @param $user User
     * @param $description String
     * @param $points Float
     */
    function __construct($id,$user,$description,$points){
		$this->id=$id;
		$this->user=$user;
		$this->description=$description;
		$this->points=floatval($points);
	}

    /**
     * Map Object
     *
     * @return String|mixed
     */

    public function map(){
        $arr = array();

        $arr["id"] = $this->id;
        $arr["user"] = $this->user->mapBasic();
        $arr["description"] = $this->description;
        $arr["points"] = $this->points;

        return $arr;
    }


    /**
     * @return Int
     */
    public function getId(){
        return $this->id;
    }

    /**
     * @param $id Int
     * @return $this
     */
    public function setId($id){
        $this->id=$id;
        return $this;
    }


    /**
     * @return User
     */
    public function getUser(){
        return $this->user;
    }

    /**
     * @param $user User
     * @return $this
     */
    public function setUser($user){
        $this->user=$user;
        return $this;
    }

    /**
     * @return String
     */
    public function getDescription(){
        return $this->description;
    }

    /**
     * @param $description String
     * @return $this
     */
    public function setDescription($description){
        $this->description=$description;
        return $this;
    }

    /**
     * @return Float
     */
    public function getPoints(){
        return $this->points;
    }

    /**
     * @param $points Float
     * @return $this
     */
    public function setPoints($points){
        $this->points=$points;
        return $this;
    }



}

?>