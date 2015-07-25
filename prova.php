<?php

require_once 'functions.api.php';
require_once 'config.php';

$apiAccess=new ApiAccess(API_PATH);

$userToken=$_SESSION['userToken'];
$apiAccess->setToken($userToken);

$json=$apiAccess->accessApi("/me","GET");

print_r($json);

?>