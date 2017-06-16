<?php
/*************** EVENT BOT APIs' **************/
require_once ('Query.php'); 
require 'flight/Flight.php';

//echo "hiiii";
//Route Function 
//Created By Annie, June 14,2017
Flight::route('POST /', function()
{
		
		header('Content-type:application/json;charset=utf-8');
		header('Authorization:Bearer 0ccb5842a2b04d0b9fdf23cddd01209d');
		
		ob_start();
		
		//$data = Flight::request()->data;
		$json_obj = file_get_contents('php://input'); 
		$request = json_decode($json_obj, true);
		$action = $request["result"]["action"];
		$parameters = $request["result"]["parameters"];
		
	
	
	
});
Flight::route('POST /searchEvents 'function(){

	$returnarray=searchEvents();
	header('Content-type:application/json;charset=utf-8');
	echo json_encode($returnarray);



});


Flight::start();


function searchEvents()
{
	$keyword = $_POST['event'];
	echo $keyword;
	$query = "SELECT * FROM entrp_events WHERE eventName like '%".$keyword."%'";
	$result = getData($query);
	if(mysqli_num_rows($result) > 0)
	{
		while($row = mysqli_fetch_array($result))
		{
				$event['eventName']		 = $row["eventName"];
				$event["description"]	 =	$row["description"];
				$event["address"]			 =	$row["address"];
				$event["event_date"]		 =	$row["event_date"];
				$event["time"]				 =	$row["event_time"];
		}
	}	
	else 
	{
			$event = "no results found";
	}	
	return $event;
}

?>