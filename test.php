<?php 
function __autoload($class_name) {
  require_once $class_name . '.php';
}
ob_start();
session_start();

$str_data = file_get_contents("roster.json");
$data = json_decode($str_data,true);
 
//var_dump($data);

$keys=array();

foreach($data as $element){
	$keys[]= $element['id'];
}

//var_dump($keys);

$final_roster=array_combine($keys,$data);
 
// Modify the value, and write the structure to a file "data_out.json"
// var_dump($final_roster);

 $players=new PlayersList();
 
 foreach($final_roster as $element){
 	if(isset($element['id']) && isset($element['Calciatore']) && isset($element['Squadra']) && isset($element['Ruolo']) && isset($element['Costo']) && isset($element['Costo_Iniziale']) && isset($element['Diff'])){
		$players[]=new Player($element['id'],$element['Calciatore'],$element['Squadra'],$element['Ruolo'],$element['Costo'],$element['Costo_Iniziale'],$element['Diff']);
 	}

}

//var_dump($players->getPlayer('PALLADINO'));

$database=new ConnectDatabase("localhost","root","aicon07","fantacalcio",3306);
//var_dump($database->dumpSingoliToList('TEVEZ',NULL));
var_dump($database->dumpPlayer('TEVEZ'));
//$database->updatePlayers($players);
//var_dump($database->dumpPlayer(2));
//var_dump($database->dumpPlayer('SAU'));

//if(isset($_SESSION['username'])) echo "true";

//var_dump($_SESSION['username']);
//var_dump($database->getUserByUsername($_SESSION['username']));
//var_dump($database->dumpConfig());


?>