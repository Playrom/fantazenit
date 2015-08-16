<?php

/**
 * Class News
 */
class News{
    /**
     * @var int $id
     */
    private $id;
    /**
     * @var String $title
     */
    private $title;
    /**
     * @var String $html
     */
    private $html;
    
    /**
     * @var DateTime $date
     */
    private $date;
	
		
	
    /**
     * @param int
     * @param String
     * @param String
     * @param DateTime
	 *
     */
    function __construct($id,$title,$html,$date){
		$this->id = $id;
		$this->title = $title;
		$this->html = $html;
		$this->date = $date;
                
	}

    /** Map competitions to dictionary
     * 
     * @return String|mixed
     * 
     * */
     
     public function map(){
        $arr = array();

        $arr["id"] = $this->id;
        $arr["title"] = $this->title;
        $arr["html"] = $this->html;
        $arr["date"] = $this->date;

        return $arr;

     }

	public function getId(){
	    return $this->id;
	}
	public function getTitle(){
	    return $this->title;
	}
	public function getHtml(){
	    return $this->html;
	}
	public function getDateNews(){
	    return $this->date;
	}
	
	public function setId($id){
		$this->id = $id;
		return $this;
	}
	
	public function setTitle($title){
		$this->title = $title;
		return $this;
	}
	
	public function setHtml($html){
		$this->html = $html;
		return $this;
	}
	
	public function setDateNews($date){
		$this->date = $date;
		return $this;
	}



}