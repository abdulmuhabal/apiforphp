<?php
 error_reporting(E_ALL);
 ini_set("display_errors", 1);
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json");

// clean trailing slash
$raw = $_SERVER["REQUEST_URI"];
if($raw[strlen($raw)-1] == "/") {
	$raw = rtrim($raw,"/");
}

$params = explode("/",$raw);

require_once("Routes.php");
if(!isset($params[4])){
	Routes::handleError();
}

$route = array("module"=>$params[3], "action"=>$params[4]);
Routes::executeRoute($route);
