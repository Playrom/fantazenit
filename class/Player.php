<?php
    
class Player{
    
        
    /**
     * @var $id Int
     */
    private $id;
    /**
     * @var $name String
     */
    private $name;
    /**
     * @var $team String
     */
    private $team;
    /**
     * @var $role String
     */
    private $role;
    /**
     * @var $value Int
     */
    private $value;
    /**
     * @var $first_value Int
     */
    private $first_value;
    /**
     * @var $diff Int
     */
    private $diff;
    /**
     * @var StatisticsCollection $stat
     */
    private $stat=array();

	function __construct($id,$name,$team,$role,$value,$first_value,$diff,$stat=NULL){
		$this->id=$id;
		$this->name=$name;
		$this->team=$team;
		$this->role=$role;
		$this->value=$value;
		$this->first_value=$first_value;
		$this->diff=$diff;
		$this->stat=$stat;
	}	

	function equalsById($id){
		if($this->id==$id) return true;
		return false;
	}

    /**
     * Map the object to Associative Array
     * @return mixed|mixed
     */

    public function map(){
        $arr=array();

        $arr['id']=$this->id;
        $arr['name']=$this->name;
        $arr['team']=$this->team;
        $arr['role']=$this->role;

        $arr['value']=$this->value;
        $arr['first_value']=  $this->first_value;
        $arr['diff']=  $this->diff;




        $second_arr=array();
        $c=count($this->stat);
        if($this->stat!=null){
            foreach($this->stat as  $item){
                $second_arr[$item->getRound()]=$item->map();
            }
        }

        $arr['stat']=$second_arr;


        return $arr;

    }


    /**
     * Gets the value of id.
     *
     * @return Int
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Sets the value of id.
     *
     * @param Int
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Gets the value of name.
     *
     * @return String
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Sets the value of name.
     *
     * @param String
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Gets the value of team.
     *
     * @return String
     */
    public function getTeam()
    {
        return $this->team;
    }
    
    /**
     * Sets the value of team.
     *
     * @param String
     *
     * @return self
     */
    public function setTeam($team)
    {
        $this->team = $team;

        return $this;
    }

    /**
     * Gets the value of role.
     *
     * @return String
     */
    public function getRole()
    {
        return $this->role;
    }
    
    /**
     * Sets the value of role.
     *
     * @param String
     *
     * @return self
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Gets the value of value.
     *
     * @return Int
     */
    public function getValue()
    {
        return $this->value;
    }
    
    /**
     * Sets the value of value.
     *
     * @param Int
     *
     * @return self
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Gets the value of first_value.
     *
     * @return Int
     */
    public function getFirstValue()
    {
        return $this->first_value;
    }
    
    /**
     * Sets the value of first_value.
     *
     * @param Int
     *
     * @return self
     */
    public function setFirstValue($first_value)
    {
        $this->first_value = $first_value;

        return $this;
    }

    /**
     * Gets the value of diff.
     *
     * @return Int
     */
    public function getDiff()
    {
        return $this->diff;
    }
    
    /**
     * Sets the value of diff.
     *
     * @param Int
     *
     * @return self
     */
    public function setDiff($diff)
    {
        $this->diff = $diff;

        return $this;
    }

    /**
     * Gets the value of stat.
     *
     * @return StatisticsCollection
     */
    public function getStat()
    {
        return $this->stat;
    }
    
    /**
     * Sets the value of stat.
     *
     * @param StatisticsCollection]
     *
     * @return self
     */
    public function setStat($stat)
    {
        $this->stat = $stat;

        return $this;
    }
}

//TESTS

/*$prova = new Player(50,'ROMANO','INTER','C','50','45.9','4');
print_r($prova);
echo $prova->getId();
*/

?>