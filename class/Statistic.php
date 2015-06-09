<?php 

class Statistic{
	private $name;
	private $value;
	private $date_modified; //DateTime

	function __construct($name,$value=0,$date_modified=NULL){
		$this->name=$name;
		$this->value=$value;
		if($date_modified==NULL) $this->date_modified=null;//new DateTime();
		else $this->date_modified=$date_modified;
	}

	public function getName(){
		return $this->name;
	}

	public function setName($name){
		$this->name=$name;
		return $this;
	}

	public function getValue(){
		return $this->value;
	}

	public function setValue($value){
		$this->value=$value;
		return $this;
	}

	public function getDateModified(){
		return $this->date_modified;
	}

	public function setDateModified($date){
		$this->date_modified=$date;
		return $this;
	}

    public function map(){
        $second_arr=array();
        $second_arr['name']=$this->getName();
        $second_arr['value']=$this->getValue();
        $second_arr['date_modified']=$this->getDateModified();

        return $second_arr;
    }
}

//Test

/*$prova=new Statistic('ciao');
print_r($prova); */

?>