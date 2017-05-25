<?php

header('Content-Type: application/json');
ob_start();

$json = file_get_contents('php://input');
//$input = json_decode(file_get_contents('php://input'), true);
$request = json_decode($json, true);
$action = $request["result"]["action"];
$parameters = $request["result"]["parameters"];


/*$mysqli = new mysqli("myvoffice.me", "myvoff_entrp", "V2PM@.@tGr!Z", "myvoff_vos");
$result = $mysqli->query("SELECT * FROM location_info WHERE location_desc like '%$search%'");
while($row = $result->fetch_array()){
			$data['location_name'][$i] = $row['location_desc'];
			$i++;
}
*/

//[Code to set $outputtext, $nextcontext, $param1, $param2 values]

$next = "sendmessage-location-followup";
$param1value = "1";
$param2value = "true";
$outputtext  = "Yes available";

$output["contextOut"] = array(array("name" => "$next-context", "parameters" =>
array("param1" => $param1value, "param2" => $param2value)));
$output["speech"] = $outputtext;
$output["displayText"] = $outputtext;
$output["source"] = "webhook";









/*
$i=0;
$search =$_GET['search'];	
//echo $search;
$mysqli = new mysqli("myvoffice.me", "myvoff_entrp", "V2PM@.@tGr!Z", "myvoff_vos");
$result = $mysqli->query("SELECT * FROM location_info WHERE location_desc like '%$search%'");
while($row = $result->fetch_array()){
			$data['location_name'][$i] = $row['location_desc'];
			$i++;
}
*/

ob_end_clean();
echo json_encode($output);
?>