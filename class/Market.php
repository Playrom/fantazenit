<?php 

	class Market{

    private $id;
    private $name;
    private $start_date;
    private $finish_date;
    private $max_change;

    function __construct($id,$name,$start_date,$finish_date,$max_change){
        $this->id=$id;
        $this->name=$name;
        $this->start_date=$start_date;
        $this->finish_date=$finish_date;
        $this->max_change=$max_change;
    }

    /**
     * Map Object to Array
     *
     * @return string|mixed
     *
     */
        public function map(){
            $arr=array();

            $arr["id"]=$this->id;
            $arr["name"]=$this->name;
           
            $arr["start_date"]=$this->start_date->format("d-m-Y H:i");
            $arr["finish_date"]=$this->finish_date->format("d-m-Y H:i");
            $arr["max_change"]=$this->max_change;

            return $arr;
        }
	
    /**
     * Gets the value of id.
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Sets the value of id.
     *
     * @param mixed $id the id 
     *
     * @return self
     */
    private function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Gets the value of name.
     *
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Sets the value of name.
     *
     * @param mixed $name the name 
     *
     * @return self
     */
    private function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Gets the value of start_date.
     *
     * @return mixed
     */
    public function getStartDate()
    {
        return $this->start_date;
    }
    
    /**
     * Sets the value of start_date.
     *
     * @param mixed $start_date the start  date 
     *
     * @return self
     */
    private function setStartDate($start_date)
    {
        $this->start_date = $start_date;

        return $this;
    }

    /**
     * Gets the value of finish_date.
     *
     * @return mixed
     */
    public function getFinishDate()
    {
        return $this->finish_date;
    }
    
    /**
     * Sets the value of finish_date.
     *
     * @param mixed $finish_date the finish  date 
     *
     * @return self
     */
    private function setFinishDate($finish_date)
    {
        $this->finish_date = $finish_date;

        return $this;
    }

    /**
     * Gets the value of max_change.
     *
     * @return mixed
     */
    public function getMaxChange()
    {
        return $this->max_change;
    }
    
    /**
     * Sets the value of max_change.
     *
     * @param mixed $max_change the max  change 
     *
     * @return self
     */
    private function setMaxChange($max_change)
    {
        $this->max_change = $max_change;

        return $this;
    }
}