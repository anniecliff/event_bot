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
		header('Authorization:Bearer e15cdaff36e746608d48c92acdf80539');
		
		ob_start();
		
		//$data = Flight::request()->data;
		$json_obj = file_get_contents('php://input'); 
		$request = json_decode($json_obj, true);
		$action = $request["result"]["action"];
		$parameters = $request["result"]["parameters"];
		if($action == "searchEvents")
		{
				$keyword = $parameters["events"];
				$result 	= searchEvents($keyword);
//				$result 	= "test".$keyword;
				$context = array("name" => "event");
				$source  = "event_bot";
				$json = json_encode([
			                'speech'   => $result,
			                'displayText' => $result,
			                'data' => [],
			                'contextOut' => [$context],
			                'source' => $source
			       	 ]); 
		
		
		}
		else if($action == "eventRunningThisday")
		{
				$keyword = $parameters["date-period"];
				$result 	= eventRunningThisday($keyword);
				if($result == false)
				{
					$speech	=	"no events for today";				
				
				}
				else 
				{
					$speech = "";
					foreach($result as $row)
					{
							$speech .= $row;
							$speech .= "  ";
												
					}
				}
//				$result 	= "test".$keyword;
				$context = array("name" => "event");
				$source  = "event_bot";
				$json = json_encode([
			                'speech'   => $result,
			                'displayText' => $result,
			                'data' => [],
			                'contextOut' => [$context],
			                'source' => $source
			       	 ]); 
		
		
		}
		
	
	ob_end_clean();
	echo $json;
	
});
// Route for search Events
//created by Annie , June , 14 2017
Flight::route('POST /searchEvents' ,function(){

	$returnarray=searchEvents();
	header('Content-type:application/json;charset=utf-8');
	echo json_encode($returnarray);



});
//Route for events for a day given
//Created by Annie, June 19, 2017
Flight::route('POST /eventRunningThisday' ,function(){

	$returnarray=eventRunningThisday();
	header('Content-type:application/json;charset=utf-8');
	echo json_encode($returnarray);



});

Flight::start();

//Function for searching an event
//Input : keyword
//Response : Event Details
//created by Annie , June 19,2017
//function searchEvents()

function searchEvents($keyword)
{
	//$keyword = $_POST['event'];
	//echo $keyword;
	$query = 'SELECT * FROM entrp_events WHERE eventName like "%'.$keyword.'%"';
	//echo $query;
	$result = getData($query);
	$count= mysqli_num_rows($result);

	if( $count > 0 )
	{
		while($row = mysqli_fetch_array($result))
		{
				$event['eventName']		 = $row["eventName"];
				$event["description"]	 =	$row["description"];
				$event["address"]			 =	$row["address"];
				$event["event_date"]		 =	$row["event_date"];
				//$event["time"]				 =	$row["event_time"];
				$event["start_time"]		 =	$row["start_time"];
				$event["end_time"]		 =	$row["end_time"];
				$status				 		 =	$row["status"];
		}
		if($status == 2)
		{
				$msg = "Sorry !! This is an expired event. ";
		
		}
		else 
		{
				$msg = $event['eventName']." \r\n ".$event["description"]." \r\n Address : ".$event["address"]." \r\n ".$event["event_date"]." From : ".$event["start_time"]." to ".$event["end_time"];
		}

	}	
	else 
	{
			$msg = "no results found";
	}	
	return $msg;
}

//Function  to get events in a given day
//Input : day, week or month
//Response : List of events
//Created by : Annie, June 19, 2017
function eventRunningThisday()
{
	$day 			=	$_POST["date_period"];
	$eventdate 	=	explode("/",$day);
	$begindate 	=	$eventdate[0];
	$enddate		=	$eventdate[1];
	$query		=	"SELECT * FROM entrp_events WHERE event_date >='".$begindate."' AND event_date < '".$enddate."'";
	$result		=	getData($query);
	$count		=	mysqli_num_rows($result);
	if($count > 0)
	{
		while($row = mysqli_fetch_array($result))
		{
	
			$eventName[$i]	=	$row["eventName"];	
			$i++;
			
		}
	
	
	}
	else 
	{
		$eventName = false;
	}

	return $eventName;

}



?>