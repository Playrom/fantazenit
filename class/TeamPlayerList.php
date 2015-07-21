<?php

class TeamPlayerList extends ArrayObject{ // Utilize TeamPlayerRound
	
	function __construct($array = array())
    {
        parent::__construct($array, ArrayObject::ARRAY_AS_PROPS);

    }

    /**
     * map the Object
     *
     * @return array
     */
    function map(){
        $arr=array();
        foreach($this as $player){
            $arr[]=$player->map();
        }

        return $arr;
    }

    /**
     * map the Object by round
     *
     * @return array
     */
    function mapByRound($round){
        $arr=array();
        foreach($this as $player){
            $arr[]=$player->mapByRound($round);
        }

        return $arr;
    }
    /**
     * Find if a player is in this list
     *
     * @param Int $id
     *
     * @return boolean
     *
     */

	function searchID($id){
		foreach($this as $player){
			if($player->getPlayer()->getId()==$id) return true;

		}
		return false;
	}

    /** return a TeamPlayerList by role
     * @param String $role
     * @return TeamPlayerList
     */
	function getByRole($role){
		$ret=new TeamPlayerList();
		foreach($this as $player){
			if(strtolower($player->getPlayer()->getRole())==strtolower($role)) $ret->append($player);
		}
		return $ret;
	}

    /** order by role and select panchina
     * @return TeamPlayerList
     */
	function orderByRole(){
        $arr=array(0=>new TeamPlayerList() , 1=> new TeamPlayerList());
        foreach($this as $element){
            if(strtolower($element->getPlayer()->getRole())==strtolower("p")){
                if($element->getPosition()==0){
                    $arr[0][]=$element;
                }else{
                    $arr[1][]=$element;
                }
            }
        }

        foreach($this as $element){
            if(strtolower($element->getPlayer()->getRole())==strtolower("d")){
                if($element->getPosition()==0){
                    $arr[0][]=$element;
                }else{
                    $arr[1][]=$element;
                }
            }
        }

        foreach($this as $element){
            if(strtolower($element->getPlayer()->getRole())==strtolower("c")){
                if($element->getPosition()==0){
                    $arr[0][]=$element;
                }else{
                    $arr[1][]=$element;
                }
            }
        }

        foreach($this as $element){
            if(strtolower($element->getPlayer()->getRole())==strtolower("a")){
                if($element->getPosition()==0){
                    $arr[0][]=$element;
                }else{
                    $arr[1][]=$element;
                }
            }
        }

        return $arr;
    }
}
?>