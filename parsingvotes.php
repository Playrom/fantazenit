<?php
function __autoload($class_name) {
  require_once $class_name . '.php';
}


    $database=new ConnectDatabase("localhost","root","aicon07","fantacalcio",3306);
    $database->getPointsByUser(1,1);

?>
