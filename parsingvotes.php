<?php
function __autoload($class_name) {
  require_once $class_name . '.php';
}


    $database=new ConnectDatabase(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);
    $database->getPointsByUser(1,1);

?>
