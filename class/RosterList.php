<?php 

class RosterList extends ArrayObject{
	public function __construct($array = array()){ 
    	parent::__construct($array, ArrayObject::ARRAY_AS_PROPS);
 	}

 	function searchPlayer($id){
 		foreach($this as $roster_player){
 			if($roster_player->getPlayer()->equalsById($id)) return $roster_player->getPlayer();
 		}
 		return null;
 	}

 	function orderByRole(){
 		$arr=new RosterList();
 		foreach($this as $element){
 			if(strtolower($element->getPlayer()->getRole())==strtolower("p")){
 				$arr[]=$element;
 			}
 		}

 		foreach($this as $element){
 			if(strtolower($element->getPlayer()->getRole())==strtolower("d")){
 				$arr[]=$element;
 			}
 		}

 		foreach($this as $element){
 			if(strtolower($element->getPlayer()->getRole())==strtolower("c")){
 				$arr[]=$element;
 			}
 		}

 		foreach($this as $element){
 			if(strtolower($element->getPlayer()->getRole())==strtolower("a")){
 				$arr[]=$element;
 			}
 		}

 		return $arr;
 	}
 	
 	function getByRole($role){
	 	$arr=new RosterList();
 		foreach($this as $element){
 			if(strtolower($element->getPlayer()->getRole())==strtolower($role)){
 				$arr[]=$element;
 			}
 		}

 		return $arr;
	 	
 	}

    /**
     * Map object to Array
     *
     * @return int|RosterPlayer
     */

    public function map(){
        $arr=array();
        foreach($this as $item){
            $arr[$item->getPlayer()->getId()]=$item->map();
        }



        return $arr;
    }
    
    /**
     * Map object to Array
     *
     * @return int|RosterPlayer
     */

    public function mapOrderedByRole(){
        $arr=array();
        foreach($this->orderByRole() as $item){
            $arr[$item->getPlayer()->getId()]=$item->map();
        }



        return $arr;
    }

}