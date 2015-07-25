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

        $temp = $this->getRealName($this->getName());

        if(isset($temp)){
            $second_arr["real_name"] = $temp;
        }



        return $second_arr;
    }

    private function getRealName($name){
        switch($name){
            case "taken" :
                return "Gol Subiti";
            case "free_kick_scored" :
                return "Rigori Segnati";
            case "round" :
                return  "Round";
            case "scored" :
                return "Gol Segnati";
            case "autogol" :
                return "Autogol";
            case "vote" :
                return "Voto Base";
            case "id_player":
                return "Id Giocatore";
            case "yellow_card":
                return "Cartellini Gialli";
            case "free_kick_keeped":
                return "Rigori Parati";
            case "gdv" :
                return "GDV";
            case "assist":
                return "Assist";
            case "red_card":
                return "Cartellini Rossi";
            case "gdp" :
                return "GDP";
            case "final":
                return "Voto Finale";
            case "free_kick_missed":
                return "Rigori Sbagliati";
            case "stop_assist":
                return "Assist da Fermo";
            default:
                return null;
    }

        return null;
    }

}

/** FUNZIONE RITORNA NOME DISPLAY DELLA TIPOLOGIA
 *
 * @param String $name
 * @return String
 */



//Test

/*$prova=new Statistic('ciao');
print_r($prova); */

?>