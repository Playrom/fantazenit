<?php

class TeamPlayerList extends ArrayObject{ // Utilize TeamPlayerRound
	
	function __construct($array = array()){ 
    	parent::__construct($array, ArrayObject::ARRAY_AS_PROPS);
 	}

	function searchID($id){
		foreach($this as $player){
			if($player->getPlayer()->getId()==$id) return true;

		}
		return false;
	}

	function getByRole($role){
		$ret=new TeamPlayerList();
		foreach($this as $player){
			if(strtolower($player->getPlayer()->getRole())==strtolower($role)) $ret->append($player);
		}
		return $ret;
	}

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