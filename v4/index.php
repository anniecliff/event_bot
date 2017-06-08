<?php
require_once ('Query.php'); 
require '../flight/Flight.php';
//echo "hiiii";
/* *************************Routes starts here*******************************/
/*** created by : Annie      ***/
/*** Created at : May 15, 2017***/







Flight::route('POST /', function()
{

	//echo "hello world";
	header('Content-type:application/json;charset=utf-8');
	header('Authorization:Bearer 0ccb5842a2b04d0b9fdf23cddd01209d');
	
	ob_start();
	
	//$data = Flight::request()->data;
	$json_obj = file_get_contents('php://input'); 
	$request = json_decode($json_obj, true);
	$action = $request["result"]["action"];
	$parameters = $request["result"]["parameters"];
	if($action == "searchCenter")
	{
		$search = $parameters["search"];
		$res_loc = searchCenter($search);
		$speech = $res_loc;
		$source  = "v4";
		/*$next_context = "location";
		$param1value = $res_loc;
		$param2value = 0;*/
		$context = array("name" => "search");

		$json = json_encode([
	                'speech'   => $speech,
	                'displayText' => $speech,
	                'data' => [],
	                'contextOut' => [$context],
	                'source' => $source
	        ]);
	
	}
	else if($action == "getAvailability")
	{
		
		$search = $parameters["search"];
		$time   = $parameters["time"];
		$date   = $parameters["date"];
		
		$res_loc = searchCenter1($search);
	
		$slots = getAvailableTimeSlot($date,$res_loc,$time);
		$speech = $res_loc;
		$source  = "v4";
		/*$next_context = "location";
		$param1value = $res_loc;
		$param2value = 0;*/
		$context = array("name" => "date");

		$json = json_encode([
	                'speech'   => $slots,
	                'displayText' => $slots,
	                'data' => [],
	                'contextOut' => [$context],
	                'source' => $source
	        		]);
	
	}
	else if($action == "doBookFacility")
	{
		
		$search = $parameters["search"];
		$time   = $parameters["time"];
		$date   = $parameters["date"];
		$cid   = $parameters["Clientid"];
		$numhours   = $parameters["numhours"];
		
		$res_loc = searchCenter1($search);
		$booktime =explode(":",$time);
		$checktime = $booktime[0].$booktime[1];
		$booked = doBookFacility($date,$res_loc,$checktime,$cid,$numhours);
		//$speech = $res_loc;
		$source  = "v4";
		/*$next_context = "location";
		$param1value = $res_loc;
		$param2value = 0;*/
		$context = array("name" => "book");

		$json = json_encode([
	                'speech'   => $booked,
	                'displayText' => $booked,
	                'data' => [],
	                'contextOut' => [$context],
	                'source' => $source
	        		]);
	
	}
	else if($action == "getBookingDetails")
	{
		
		/*$search = $parameters["search"];
		$time   = $parameters["time"];
		$date   = $parameters["date"];*/
		$cid   = $parameters["Clientid"];
		$book_id =$parameters["BookId"];
		/*$numhours   = $parameters["numhours"];
		
		$res_loc = searchCenter1($search);
		$booktime =explode(":",$time);
		$checktime = $booktime[0].$booktime[1];*/
		$booked = getBookingDetails($cid,$book_id);
		
		//$booked = "Hiiiii".$cid." my book id is".$book_id;
		//$speech = $booked;
		$source  = "v4";
		/*$next_context = "location";
		$param1value = $res_loc;
		$param2value = 0;*/
		$context = array("name" => "bookingdetails");

		$json = json_encode([
	                'speech'   => $booked,
	                'displayText' => $booked,
	                'data' => [],
	                'contextOut' => [$context],
	                'source' => $source
	        		]);
	
	}
	else 
	{
		$context = array("name" => "search");
	
		$json = json_encode([
		                'speech'   => "Try again ",
		                'displayText' => "Try again!!!",
		                'data' => [],
		                'contextOut' => [$context],
		                'source' => "v4",
		  		 ]);
		
	}	  
	
/*	$context = array("name" => "search");
	
	$json = json_encode([
	                'speech'   => "Hello World",
	                'displayText' => "Hello World",
	                'data' => [],
	                'contextOut' => [$context],
	                'source' => "v4",
	  		 ]);*/ 
	ob_end_clean();
	echo $json;

	

});
//Route to get Locations with room facility
//Annie, May 15, 2017
Flight::route('/getRoom', function()
{
	//enable_cors()();
	$returnarray=getRoom();
//	header('Content-type:application/json; charset=utf-8');
	header("Content-Type: application/json; charset=utf-8");	
	//$context = "";
	$json = json_encode([
                'speech'   => "text",
                'displayText' => "text",
                'data' => [],
                'contextOut' => [$returnarray],
                'source' => "v4"
        ],JSON_UNESCAPED_SLASHES);


	
	
	
	echo $json;

});

//Route to get facility types
//Annie, May 15, 2017
Flight::route('/getFacilitiesType', function()
{
	//enable_cors()();
	$returnarray=getFacilitiesType();
	header('Content-type:application/json;charset=utf-8');
	echo json_encode($returnarray);

});



//Route to book meeting room
//Annie, May 15, 2017
Flight::route('POST /doBookFacility', function()
{
	//enable_cors()();
	$returnarray=doBookFacility();
	header('Content-type:application/json;charset=utf-8');
	echo json_encode($returnarray);

});

//Route to get call details
//Annie, May 15, 2017
Flight::route('/getCallDetails', function()
{
	//enable_cors()();
	$returnarray=getCallDetails();
	header('Content-type:application/json;charset=utf-8');
	echo json_encode($returnarray);

});

//Route to search location
//Annie, May 15, 2017
Flight::route('POST /searchCenter2', function()
{
   header('Content-type:application/json;charset=utf-8');
	header('Authorization:Bearer 0ccb5842a2b04d0b9fdf23cddd01209d');
	

	ob_start();
	
	$obj = json_decode(file_get_contents('php://input'),true);

	$parameters = $obj['parameters'];
	$search = $parameters['search'];

	$query  = "SELECT * FROM location_info WHERE location_desc like '%".$search."%'";

	$res    = getData($query);
	$count_res = mysqli_num_rows($res);
	if($count_res > 0)
	{
		while($row = mysqli_fetch_array($res))
		{
			$res_loc = $row['location_desc'];

		}	
	
	}
	else 
	{
		$data = "No locations found";
	}
	
		$speech = "Yes Center available ".$res_loc;
	//echo $res_loc;
	$source  = "v4";
	$next_context = "location";
	$param1value = $res_loc;
	$param2value = 0;
	$context = array(array("name" => $next_context, "parameters" => array("param1" => $param1value, "param2" => $param2value)));
	//$context = "";
	$json = json_encode([
                'speech'   => $speech,
                'displayText' => $speech,
                'data' => [],
                'contextOut' => [$context],
                'source' => $source
        ]);



   
	ob_end_clean();
//	$returnarray=searchCenter();

	echo $json;
});



//Route to get client info
//Annie, May 15, 2017
Flight::route('/getClientInfo', function()
{
	//enable_cors()();
	$returnarray=getClientInfo();
	header('Content-type:application/json;charset=utf-8');
	echo json_encode($returnarray);

});



//Route to get client facilities hours left
//Annie, May 16, 2017
Flight::route('/getClientFacilitiesHoursLeft', function()
{
	//enable_cors()();
	$returnarray=getClientFacilitiesHoursLeft();
	header('Content-type:application/json;charset=utf-8');
	echo json_encode($returnarray);

});

//Route to get client facilities hours left
//Annie, May 16, 2017
Flight::route('/getVOFacilities', function()
{
	//enable_cors()();
	$returnarray=getVOFacilities();
	header('Content-type:application/json;charset=utf-8');
	echo json_encode($returnarray);

});

//Route to get available time slots for a day
//Annie, May 16, 2017
Flight::route('POST /getAvailableTimeSlot', function()
{
	//enable_cors()();
	$returnarray=getAvailableTimeSlot();
	header('Content-type:application/json;charset=utf-8');
	echo json_encode($returnarray);

});


//Route to get BOOKNG LIST 
//Annie, May 16, 2017
Flight::route('/getBookingList', function()
{
	//enable_cors()();
	$returnarray=getBookingList();
	header('Content-type:application/json;charset=utf-8');
	echo json_encode($returnarray);

});


//Route to get BOOKNG Details 
//Annie, May 16, 2017
Flight::route('POST /getBookingDetails', function()
{
	//enable_cors()();
	$returnarray=getBookingDetails();
	header('Content-type:application/json;charset=utf-8');
	echo json_encode($returnarray);

});
/* ************************* Routes end here **************************** */

Flight::start();



//function to get locations having meeting rooms
function getRoom() 
{
	$i=0;
	$sql = "SELECT L.*,F.* FROM location_facilities_v2 AS F JOIN location_info as L  ON L.id = F.vo_id WHERE  F.facility_type = 1";
	$res = getData($sql);
	while( $row =mysqli_fetch_array($res))
	{

		$data['location_name'][$i] = $row['location_desc'];      	
		$i++;    	
	}	      
	return $data;
	
}

//function to get all facilities type

function getFacilitiesType()
{
	$i=0;
	$sql  =  "SELECT * FROM facilities_type";
	$res  =  getData($sql);
	while($row = mysqli_fetch_array($res))
	{
   	$data['type'][$i] = $row['facilities_type'];
   	$i++;
   }
   return $data;
	
}

// function to get call details of a client

function getCallDetails()
{
/*	$cid = 10002;
	$token = "50b2061fc834cedec6def1affd60e998";
	$callid = "21203";*/
	$cid = $_POST['cid'];
//	echo $cid;
	$token = $_POST['token'];
	$callid = $_POST['callid'];
	$auth_token = portal_getUserToken($cid);
	$cnt = 0;

	if ($token != "" || $cid != "")
	{
	   if( $token == $auth_token )
	   {
			$query = "SELECT * FROM phone_log WHERE clientid='$cid' AND id='$callid'";
			$call_q = getData($query);
	      while($data = mysqli_fetch_array($call_q))
		   {
				$staff = getStaffName($data["operator"]);
	         $callobj[$cnt] = array(
	               		"id" => $data["id"],
	               		"caller number" => $data["caller_no"],
	              		   "caller name" => $data["caller_name"],
	                	   "log date" => $data["log_date"],
	                     "log time" => $data["log_time"],
	               		"taken message" => $data["msg"],
	               		"caller company name" => $data["caller_coname"],
	               		"action taken" => $data["notify_type"],
	               		"notify via" => $data["notify_via"],
	                     "notification send by" => $staff
	               		);
	
				$cnt++;
			}
	
			$fcall = array("Calls Details" => $callobj);
	      echo json_encode($fcall);
	   }
	   else
	   {
	        echo "Login Failed : 1";
	   }
	}
	else
	{
	       	echo "Login Failed : 1";
	}

}


//function to get client facilites hours left

function getClientFacilitiesHoursLeft()
{
	   $cid = $_POST["cid"];
		$token = $_POST["token"];
		// facility type id meeting = 1, Suite = 2, Hot desking = 3
		//$ftype = $_POST["ftype"];
		
		//$cid = "10002";
		//$token = "50b2061fc834cedec6def1affd60e998";
		//$ftype = 3;
		
		
		
		$auth_token = portal_getUserToken($cid);
		
		$cnt = 0;
		
		if( $token == $auth_token )
		{
			
			$query = "SELECT * FROM client_facilities_core WHERE status=1 AND client_id =".$cid;
			$result = getData($query);
			$count_res = mysqli_num_rows($result);
			if($count_res > 0)
			{
				while($row = mysqli_fetch_array($result))
				{
					$meeting_hours_left = $row['meeting_room_hours_left'];
					$suite_hours_left   = $row['day_office_hours_left'];
					$hotdesk_hours_left = $row['hot_desking_hours_left'];					
						
				}
			
			
			
			}
			
       $facilitieshoursobj[$cnt] = array(
         									"meeting room" => $meeting_hours_left,
         									"office suite" => $suite_hours_left,
        		 								"hotdesking" => $hotdesk_hours_left

         									);
		
		
		$fjsonobj = array("Facilities Hours Left" => $facilitieshoursobj);
		return $fjsonobj;
		
		}
		else
		{
		       	return "Login Failed : 1";
		}

}

//function to validate a user token

function portal_getUserToken($id)
{
   	$data_r = "SELECT token FROM client_info WHERE clientid = '$id'";
   	$res = getData($data_r);
   	$fdata = mysqli_fetch_array($res);
   	return $fdata["token"];
}

// function to get loginname of a staff
function getStaffName($staffid)
{
	$data_q = "SELECT loginname FROM operators WHERE id='$staffid'";
	$data_r = getData($data_q);
	
	$fdata = mysqli_fetch_row($data_r);
	return $fdata[0];
}

// function to return all location that matches search string
function searchCenter($search)
{

	$i=0;
	
	$query  = "SELECT L.*,F.* FROM location_facilities_v2 AS F JOIN location_info as L  ON L.id = F.vo_id WHERE  F.facility_type = 1 AND L.location_desc like '%".$search."%'";

	$res    = getData($query);
	$count_res = mysqli_num_rows($res);
	if($count_res > 0)
	{
		while($row = mysqli_fetch_array($res))
		{
			$res_loc = "Yes Center available. ".$row['location_desc']." Do you wanna book a room?";

		}	
	
	}
	else 
	{
		$res_loc = "No locations found";
	}


	return  $res_loc;
	
}


// function to return all location that matches search string
function searchCenter1($search)
{

	$i=0;
	
	$query  = "SELECT L.id as location_id,L.*,F.* FROM location_facilities_v2 AS F JOIN location_info as L  ON L.id = F.vo_id WHERE  F.facility_type = 1 AND L.location_desc like '%".$search."%'";

	$res    = getData($query);
	$count_res = mysqli_num_rows($res);
	if($count_res > 0)
	{
		while($row = mysqli_fetch_array($res))
		{
			$res_loc = $row['location_id'];

		}	
	
	}
	else 
	{
		$res_loc = "";
	}


	return  $res_loc;
	
}

// function to get client info
function getClientInfo()
{

		$cid = $_POST["cid"];
		$token = $_POST["token"];
		
		/*$cid = "12567";
		$token = "118b5160ddfd792041db4851a76237c9";*/
		
		//$adminfunc = new adminClass();
		$auth_token = portal_getUserToken($cid);
		
		$cnt = 0;
		
		if ($token != "" || $cid != "")
		{
		   if( $token == $auth_token )
		   {
				$clientinfo = getManageClientsInfo("clientid", $cid);

				while ($crow = mysqli_fetch_array($clientinfo))
				{
			                	$infoobj[$cnt] = array(
			                                "first name" => $crow["firstname"],
			                                "last name" => $crow["lastname"],
			                                "email" => $crow["email"],
			                                "company name" => $crow["coname"],
			                                "address" => $crow["address"],
			                                "city" => $crow["city"],
			                                "state" => $crow["state"],
			                                "postal" => $crow["postcode"],
			                                "country" => $crow["country"],
			                                "city" => $crow["city"],
			                                "pri. phone" => $crow["pri_contact_no"],
			                                "alt. phone" => $crow["sec_contact_no"],
			                                "fax" => $crow["fax_number"],
			                                "website" => $crow["website"]
			
			                	);
			
			                	$f_info = array("Client Information" => $infoobj);
			                	return($f_info);
				}
		   }
		   else
		   {
		       return "Login Failed : 1";
		   }
		}
		else
		{
		        return "Login Failed : 1";
		}
		
}

function getManageClientsInfo($searchfor, $searchstring)
{
		$o_search = trim ($searchstring);
//		$o_search = mysqli_real_escape_string($f_search);
		
		$no_go_chk = 0;
		
		if ($searchfor == "clientid")
		{
			$cinfo_sqlstmt = "SELECT * FROM client_info WHERE clientid='$o_search'";			
		}
		elseif ($searchfor == "inv")
		{
			$p_inv = explode ("-", $searchstring);
			$void = trim($p_inv[0]);
			$invid = trim($p_inv[1]);
			$invtable = "client_invoices_".$void;
			$cinv_sqlstmt = "SELECT client_id FROM $invtable WHERE 	invoice_id='$invid' AND vo_id	='$void'";		
			$cinfo_trxn_result = getData($cinv_sqlstmt);
			$result_chk = mysqli_num_rows($cinfo_trxn_result);
			
			$qrow = mysqli_fetch_row($cinfo_trxn_result);
			
			
			if ($result_chk != 0)
			{
				$cinfo_sqlstmt = "SELECT * FROM client_info WHERE clientid='$qrow[0]'";
			}
			else
			{
				$no_go_chk = 1;
			}
			
		}
		elseif ($searchfor == "fname")
		{
			$cinfo_sqlstmt = "SELECT * FROM client_info WHERE firstname REGEXP '".$o_search."'";
		}
		elseif ($searchfor == "lname")
		{
			$cinfo_sqlstmt = "SELECT * FROM client_info WHERE lastname REGEXP '".$o_search."'";
		}
		else
		{
			$cinfo_sqlstmt = "SELECT * FROM client_info WHERE ".$searchfor." REGEXP '".$o_search."'";
		}
		//$cinfo_sqlstmt = "SELECT clientid, firstname, lastname, coname FROM client_info WHERE ".$searchfor." LIKE '".$o_search."'";
		//echo $cinfo_sqlstmt;
		//$cinfo_trxn_result = mysql_query($cinfo_sqlstmt, $dbm) or die (@mysql_error());
		$cinfo_trxn_result = getData($cinfo_sqlstmt);
	
		if ($no_go_chk == 0)
		{
			return $cinfo_trxn_result;
		}
		else
		{
			return "NA";
		}
}

//Function to list facilities of a location
function getVOFacilities()
{
		$cid = $_POST["cid"];
		$void = $_POST["void"];
		$token = $_POST["token"];
		
		//$cid = "10002";
		//$void = "22";
		//$token = "27ef0cf58d359294024a80053d178796";
		
			
		$auth_token = portal_getUserToken($cid);
		
		$cnt = 0;
		
		if ($token != "" || $cid != "")
		{
		
			if( $token == $auth_token )
			{
			       $vo_facilities_q = getVOFacilities_av($void);
		
			       for ($i=0;$i<count($vo_facilities_q);$i++)
			       {
			       		$vo_q = getVOFacilities_v2($void, $vo_facilities_q[$i]);
		
			               	while($data = mysqli_fetch_array($vo_q))
				       	{
		
					       $facobj[$cnt] = array(
		               			"facility id" => $data["id"],
		               			"vo id" => $data["vo_id"],
		               			"facility type" => $data["facility_type"],
		               			"min pax" => $data["min_pax"],
		               			"max pax" => $data["max_pax"],
		               			"description" => $data["description"]
		
		               			);
		
						$cnt++;
					}
				}
		
				$fvoloc = array("Location Facilities" => $facobj);
            return $fvoloc;
		
			}
			else
			{
		       		return "Login Failed : 1";
			}
		}
		else
			        return "Login Failed : 1";

}
function getVOFacilities_av($void)
{
		
		
		$query 	= 	"SELECT available_facilities FROM location_facilities WHERE vo_id=".$void;
		$result 	=	getData($query);
		while($row = mysqli_fetch_array($result))
		{
			$array = $row['available_facilities'];
		
		}

		$rearray = explode(",",$array); 	

		return $rearray;
		
}

function getVOFacilities_v2($void, $facilitytype)
{
		$data_r = "SELECT * FROM location_facilities_v2 WHERE vo_id='$void' AND facility_type='$facilitytype' AND status='1'";
		$res    = getData($data_r);
		return $res;
}

//Function to get available time slots for a day
function getAvailableTimeSlot($bookdate,$loc_id,$booktime)
//function getAvailableTimeSlot()
{
			/*$cid = $_POST["cid"];
			$token = $_POST["token"];
			$bookdate = $_POST["bookdate"];
			$loc_id = $_POST["void"];
			$facilities = $_POST["ftype"];
			$facility_id = $_POST["fid"];*/
			/*$bookdate = $_POST["bookdate"];
			$booktime = $_POST["checktime"];
			//$booktime = $_POST["booktime"];
			$loc_id = $_POST["void"];
*/
			//echo "Book Time".$booktime;
			$facilities = "1";
			$facility_id = "52";
			$time =explode(":",$booktime);
			//print_r($time);
			$flag=0;
			$checktime1 = $time[0].$time[1];
			$checktime  = trim($checktime1);
			

				if ($facilities == 2 || $facilities == 4)
		      {
		                // office suite and flexi office all same type
		               	$room_book_type = 2;
		               	$fid = 0;
		      }
				elseif ($facilities == 1)
	        	{
	               	// meeting room type
	               	$room_book_type = 1;
	        	}
	       	else
	        	{
	               	$room_book_type = $facilities;
	        	}
			
				// check to use check week day start time or weekend
				$check_is_Sat = isSaturday($bookdate);
			   if ($check_is_Sat == false)
				{
			        	/*$f_start_time = $bookingfunc->get_facilities_booking_weekdays_start_time($loc_id);
			        	$f_end_time = $bookingfunc->get_facilities_booking_weekdays_end_time($loc_id);*/
			        	$query  = "SELECT * FROM location_info WHERE id='$loc_id'";
			        	$result = getData($query);
			        	while($row = mysqli_fetch_array($result) )
			        	{
			        		$f_start_time = $row['facility_start_time'];
			        		$f_end_time   = $row['facility_end_time'];
			        	}
			        	
				}
				else
				{
			        
			        	$query  = "SELECT * FROM location_info WHERE id='$loc_id'";
			        	$result = getData($query);
			        	while($row = mysqli_fetch_array($result) )
			        	{
			        		$f_start_time = $row['facility_weekend_start_time'];
			        		$f_end_time   = $row['facility_weekend_end_time'];
			        	}
				}
			
				$q1  =     "SELECT * FROM facilities_booking WHERE facilities_type='$facilities' AND `$checktime`= 0 AND location_id='$loc_id' AND book_date='$bookdate'";
				//AND facility_id='$facility_id'
				//echo $q1;
				$pre_check_dates  = getData($q1);
				$date_exist = mysqli_num_rows($pre_check_dates);
       		$check_dates = mysqli_fetch_array($pre_check_dates);
       		//echo $date_exist;
       		/*$date_exist = 0;
       		$check_dates = 0;*/
				$a_time_slots = "";

				if ($date_exist != 0)
				{
			
					$flag =1;
					/*$loop_time = $time;
			
					
			   		while ($loop_time != $f_end_time)
			   		{
			    	   		$period_end_time = $loop_time + 100;
					
							if ($check_dates[$loop_time] == 0)
							{
								$a_time_slots .= $loop_time."-". $period_end_time." ";
				   		}
				    	   $loop_time = $loop_time + 100;
								
						}*/
				}
				else
				{
					/*$loop_time = $f_start_time;
			
			   		while ($loop_time != $f_end_time)
			   		{
			    	   		$period_end_time = $loop_time + 100;
                  
								$a_time_slots .= $loop_time."-". $period_end_time." ";
			
			           		$loop_time = $loop_time + 100;
			
			   		}*/
			   		
			   		$flag =0;
				}
				
				
					//$checktime = $time[0].$time[1];
					/*$time_slots = explode(" ",$a_time_slots);
					print_r($a_time_slots);
					echo "<pre>";
					print_r($time_slots);

					foreach($time_slots as $avail)
					{
						if($checktime == $avail)
						{
							$flag =1;
							break;						
						
						}	
						else 
						{
							$flag =0;	
						}				
					
					}*/
					if($flag == 0)
					{
						$result_loc = "Time slot already booked";
					}
					else
					{
						$result_loc = "Time slot available.  Please enter your client ID";
							
					}
			/*
					$timeslotobj[$cnt] = array(
			                              "Time Slots" => $a_time_slots
			                				);
			                				
   				$f_atime_slot = array("Available Time Slots" => $timeslotobj);
					return $time[0];*/
			
			return $result_loc;


}

function getBookingList()
{

			$agent = "10000";
			
		/*	$cid = $_POST["cid"];
			$token = $_POST["token"];*/
			
			/*$cid = "10002";
			$token = "50b2061fc834cedec6def1affd60e998";*/
			
			//$auth_token =  portal_getUserToken($cid);
			
			$cnt = 0;
		/*	
			if ($token != "" || $cid != "")
			{
			   if( $token == $auth_token )
			   {
			*/
						$mtdate = date("2015-09-01", time());

               	$query = "SELECT * FROM client_booking_log WHERE client_id='$cid' AND status='1' AND book_date >= DATE(NOW()";
//               	echo $query;
               	$booking_history = getData($query);
               	if(mysqli_num_rows($booking_history) >0)
               	{
       		      while($data = mysqli_fetch_array($booking_history))
				      {
								$data_r = "SELECT facilities_type FROM facilities_type WHERE id= ".$data["facilities_type"];
               			$fdata = getData($data_r);
								while($row1 = mysqli_fetch_array($fdata) ) 
								{
									$type_name = $row1['facilities_type'];
									               			
								}               			
//								$loc_id = $clientfunc->get_client_booking_location($data["book_date"], $data["book_id"], $data["facilities_type"], $data["book_start_time"]);
								
								$q_stmt = "SELECT location_id FROM facilities_booking WHERE book_date='".$data["book_date"]."' AND '".$data["book_start_time"]."' = '".$data["book_id"]."' AND facilities_type='".$data["facilities_type"]."'";
//								echo $q_stmt;								
								$res= getData($q_stmt);
//								print_r($res);
								if(mysqli_num_rows($res) >0) 
								{
									while($row2 = mysqli_fetch_array($res)) 
									{
										$loc_id = $row2['location_id'];
									               			
									}  
								}
//								$fdata = mysql_fetch_array($data_r)
//								$loc_id = mysqli_fetch_array($data_r);
								
								
								
								$voname = getVOName($loc_id);
						       
								$end_time = $data["book_start_time"];
								$slots = $data["book_hours_slots"];
					
								for ($i=0;$i<$slots;$i++)
								{
									$end_time = $end_time + 100;
									if ($end_time == "2400")
									{
										$end_time = "0000";
									}
								}
					
								$timebook = $data["book_start_time"]."-".$end_time;
					
								$bookobj[$cnt] = array(
						               		      "book id" => $data["book_id"],
					        	      		         "location name" => $voname,
									                  "facility name" => $type_name,
					               			      "book date" => $data["book_date"],
									                  "time" => $timebook					
					               		    );
					
								$cnt++;
					}
					}
					$f_book = array("Facility Booking List" => $bookobj);
					return $f_book;
			
			 /*  }
			   else
			   {
			        return "Login Failed : 1";
			   }
			}
			else
			{
			       	return "Login Failed : 1";
			}*/
}

function getBookingDetails($cid,$bookid)
{

			$agent = "10000";
			
		/*	$cid = $_POST["cid"];
			//$token = $_POST["token"];
			$bookid = $_POST["bookid"];*/
			/*
			$cid = "10002";
			$token = "50b2061fc834cedec6def1affd60e998";
			$bookid = "14331";
			
			
			$auth_token = portal_getUserToken($cid);
			
			$cnt = 0;
			
			if ($token != "" || $cid != "")
			{
			   if( $token == $auth_token )
			   {*/
			
					  $myquery = "SELECT * FROM client_booking_log WHERE book_id='$bookid' AND status='1'";
					  //echo $myquery;
			        $booking_details = getData($myquery);
				
				     while($data = mysqli_fetch_array($booking_details))
				     {
						$query2 = "SELECT facilities_type FROM facilities_type WHERE id='$id'";
						$res    = getData($query2);
						while($r = mysqli_fetch_array($res)) {	
							$type_name = $r['facilities_type'];
						}			     	
				     	
//						$type_name = $bookingfunc->get_facility_name($data["facilities_type"]);
						
						$starth = $data["book_start_time"];
						$duration = $data["book_hours_slots"];
				       
						$end_time = $starth;
						for ($i=0; $i<$duration; $i++)
						{
					
							$end_time = $end_time + 100;
							if ($end_time == "2400")
							{
								$end_time = "0000";
							}
						}
			
						$f_time = $starth ."-".$end_time;
						$query3 = "SELECT * FROM facilities_booking WHERE `".$starth."` = ".$data['book_id']." AND facilities_type='".$data['facilities_type']."' AND book_date='".$data['book_date']."'";
						$res3  = getData($query3);
						while($r1 = mysqli_fetch_array($res3))
						{
								$loc_id = $r1['location_id'];
						
						}
					//	echo $query3;
						//$loc_id=14;
//						$loc_id = $bookingfunc->get_location_id_via_booking_table($data["facilities_type"], $data["book_date"], $starth, $data["book_id"]);
						$vo_name = getVOName($loc_id);
						
						
						$msg = "Booking Details : Location -".$vo_name.", Booking ID -".$data["book_id"].", Facility Name -".$type_name.", Pax -".$data["book_pax"].", AddOn -". $data["book_addon"].", Time-".$f_time." ,Duration -". $duration." Hour(s)"." on ".$data["book_date"]; 
			
					/*	$bookobj[$cnt] = array(
			                        "location" => $vo_name,
			               			"book id" => $data["book_id"],
			              				"facility name" => $type_name,
			                        "pax" => $data["book_pax"],
			                        "addon" => $data["book_addon"],
			                        "time" => $f_time,
			                        "duration" => $duration." Hour(s)",
			               			"book date" => $data["book_date"]
			
			               		);*/
			
						//$cnt++;
					}
			
					//$f_book = array("Facility Booking Details" => $bookobj);
//			                echo json_encode($f_book);
					return $msg;
			
			 /*  }
			   else
			   {
			        echo "Login Failed : 1";
			   }
			}
			else
			{
			       	echo "Login Failed : 1";
			}*/
			




}


//function doBookFacility($bookdate,$loc_id,$starttimeslot,$cid,$numhours)
function doBookFacility()
{


$agent = "-1";


$starttimeslot = "1800-2100";
$cid = "10002";
$token = "ffe5c3517bcac0fc7c3261283988e93303bab637";
$bookdate = "2017-07-13";
$loc_id = "14";
$facilities_id = "34";
$numhours = "3";
$pre_start_hour = explode("-",$starttimeslot);
$starth = $pre_start_hour[0];
$addon = "Non";
$pax = "4";


	//$facilities_id = "35";
	$cnt = 0;
	$msg = "";
	$addon_msg = "";
	$inv_msg = "";

	$data_r = "SELECT * FROM client_facilities_core WHERE client_id=".$cid;
		//echo $data_r;
	$fdata = getData($data_r);
	while($row = mysqli_fetch_array($fdata))
	{
		 $conf_hours_left = $row["meeting_room_hours_left"];
		 $f_ref_id= $row["id"];
	
	}
/*	$conf_hours_left = $mhours_hours;
	$f_ref_id =  $mhours_hours_id;*/
	/*$mhours_hours = get_client_conference_hours($cid);
	//echo "......".$mhours_hours."hours......";
	$mhours_hours_id =get_client_conference_hours_ref_id($cid);*/

	
	$q= "Select * from location_facilities_v2 where vo_id =".$loc_id." and facility_type =1";
	$res = getData($q);
	$count = mysqli_num_rows($res);
	if($count > 0)
	{
		while($row = mysqli_fetch_array($res))
		{
			$facilities_id = $row['id'];
					
		}		
		
	}

	$facility_type=1;
	$facility_name="Meeting Room";
	$room_book_type = 1;
	//$m_facilities_id = $facilities_id;
/*
	$query1 = "SELECT shared_room_id FROM location_facilities_v2 WHERE id=".$facilities_id." ";
	$res1   = getData($query1);
	$count1 = mysqli_num_rows($res1);
	if($count1 > 0)
	{
		while($r1 = mysqli_fetch_array($res1))
		{
		
			$shared_room_flag = $r1['shared_room_id'];
		
		}	
	
	
	
	}

	//$shared_room_flag = $bookingfunc->get_facilities_shared_room_id($facilities_id);
	// check if shared room for hotdesk and office Suite
	if ($shared_room_flag != 0)
	{
                  $room_book_type = 2;
		 				$m_facilities_id = 0;
	}
	$room_book_type = 1;*/
	$invid = 0;
	
	//$prep_num_hours = explode("|", $_POST["numslots"]);
	
	$num_hours = $numhours;
	$num_slots = $numhours;

	
	// For billing, we get client vo location not the place he wanna rent
	$client_void = getClientLocation($cid);
	$facility_location =getVOName($loc_id);

	// check if valid booking
	$chk_valid_booking = check_valid_facilities_booking_v2($loc_id, $room_book_type, $bookdate, $starth, $num_slots, $facilities_id);
	//echo $chk_valid_booking;
	$meeting_info = getFacilitiesProductInfo($client_void, $loc_id, $facilities_id);
			
			/*if ($facility_type == 1)
			{*/
				/*$conf_hours_left = $mhours_hours;
				$f_ref_id =  $mhours_hours_id;*/
		/*	
			}				
			*/
/*	
	$p_query = "SELECT * FROM products WHERE product_id= ".$meeting_info["product_id"];
	//echo $p_query;
	$p_result  = getData($p_query);
	while($r11 = mysqli_fetch_array($p_result))
	{
		$product_default_unit_price =$r11['price'];
	}
	*/
	
	$addon_product_name = "Non";
	
	
	if( $chk_valid_booking == 1)
	{
		//echo "echo hours left".$conf_hours_left;
		// deduct hours only invoice addon
			if ($conf_hours_left >= $num_hours)
			{
				//update_client_facility_booking_hours($cid, $num_hours, $conf_hours_left, $facility_type, $f_ref_id);
					// all bill
					// hours left < booked hours
					if ($conf_hours_left > 0)
					{
						$num_hour_deduct = $conf_hours_left - $num_hours;
						$f_hours_deduct = abs($num_hour_deduct); // get absolute number no negative sign the raminder need to bill
						update_client_facility_booking_hours($cid, $num_hours, $conf_hours_left, $facility_type, $f_ref_id);
						$invid = create_client_facilities_invoice($cid, $client_void, $facility_type, $f_hours_deduct, $bookdate, $facility_location, $meeting_info["price"]);
					}
					else
					{
	
						// so already negative. just continue deduct the $num_hours and charge
					   update_client_facility_booking_hours($cid, $num_hours, $conf_hours_left, $facility_type, $f_ref_id);
						$invid = create_client_facilities_invoice($cid, $client_void, $facility_type, $num_hours, $bookdate, $facility_location, $meeting_info["price"]);
					}
					
					//$f_inv = $client_void."-".$invid;
					//$f_inv = $invid;
	
					$inv_msg = "Invoice". $invid." Created for". $facility_location." facility Additional Hours Usage.";
					
					if ($invid != 0)
					{
						$pre_in = explode("-", $invid);
						$p_vo_id = $pre_in[0];
						$p_inv_id = $pre_in[1];
					}
					else
					{
						$p_vo_id = 0;
						$p_inv_id = 0;
						
					}
					
	
					// update new booking
					$book_id =update_facilities_booking($cid, $loc_id, $facility_type, $pax, $bookdate, $starth, $num_slots, $addon_product_name, $p_vo_id, $p_inv_id, $agent);
					update_facilities_booking_table_v2($book_id, $loc_id, $room_book_type, $m_facilities_id, $bookdate, $starth, $num_slots);
		
					$msg = "Booking Completed. Booking ID is ".$book_id;
			

		
		}
		else
		{
					$msg = "Booking Failed. Please try again or contact our Customer Care Team.";
			
		}

		$f_msg = $msg." ".$inv_msg." ".$addon_msg;

   }
   else
   {
        $f_msg = "Failed Booking.";
   }


               return $f_msg;

}

function get_client_conference_hours($cid)
	{
		$data_r = "SELECT * FROM client_facilities_core WHERE client_id=".$cid;
		//echo $data_r;
		$fdata = getData($data_r);
		while($row = mysqli_fetch_array($fdata))
		{
			 $fdat2 = $row["meeting_room_hours_left"];
		
		}

		//echo $fdat2;
		return $fdat2;
		//return $data_r;
	}

	function get_client_conference_hours_ref_id($cid)
	{
		$data_r = "SELECT * FROM client_facilities_core WHERE client_id=".$cid." AND status=1";
	//			echo $data_r;
		$fdata = getData($data_r);
		while($row = mysqli_fetch_array($fdata))
		{
			 $fdat2 = $row["id"];
		
		}
		return $fdat2;
		//return $data_r;
	}

function getClientLocation($cid) 
{
	$query = "SELECT * FROM client_info WHERE clientid=".$cid;
	$result = getData($query);
	while($row = mysqli_fetch_array($result))
	{
		
			$location = $row['location'];		
		
	}	
	return $location;


}

function isSaturday($date) 
{
	$weekDay = date('w', strtotime($date));
	if ($weekDay == 0 || $weekDay == 6)
	{
		return true;	
	}
	else
	{
		return false;
	}
}
function getVOName($void)
{
		$query = "SELECT * FROM location_info WHERE id=".$void;
	   $result = getData($query);
		while($row = mysqli_fetch_array($result))
		{
		
			$fdata = $row['location_desc'];		
		
		}	

	//	$fdata = mysqli_fetch_assoc($data_r);
		return $fdata;
		
		/*$mysqli = new mysqli("myvoffice.me", "myvoff_entrp", "V2PM@.@tGr!Z", "myvoff_vos");
		//$myArray = array();
		$query = "SELECT location_desc FROM location_info WHERE id=".$void;
		if ($result = $mysqli->query($query)) {

    		while($row = $result->fetch_array()) {
            $myArray = $row['location_desc'];
    		}
    //echo json_encode($myArray);
		}

//$result->close();
$mysqli->close();		
		
		
		return $myArray;*/
		
		
		
}



function check_valid_facilities_booking_v2($loc_id, $facility_type, $bookdate, $starttime, $num_slots, $facility_id)
	{
		$valid_flag = 0;
		$pass_day_hour_flag =0;
		
		// check to use check week day start time or weekend		
		$check_is_Sat = isSaturday($bookdate);
		
		// test = 1 or not test =0
		$test = 0;
		$query  = "SELECT * FROM location_info WHERE id='$loc_id'";
			        				        //	echo $query;
     	$result = getData($query);
     	while($row = mysqli_fetch_array($result) )
     	{
     		$f_fac_start_time = $row['facility_start_time'];
     		$f_fac_end_time   = $row['facility_end_time'];
     		$f_week_start_time = $row['facility_weekend_start_time'];
			$f_week_end_time   = $row['facility_weekend_end_time'];
     	}
		// not sat
		if ($check_is_Sat == false)
				{
			        
			        /*	$query  = "SELECT * FROM location_info WHERE id='$loc_id'";
			        				        //	echo $query;
			        	$result = getData($query);
			        	while($row = mysqli_fetch_array($result) )
			        	{
			        		
			        	}*/
			        	$f_start_time = $f_fac_start_time;
			        		$f_end_time   = $f_fac_end_time;
			        	
				}
				else
				{
			        $f_start_time = $f_week_start_time;
			        		$f_end_time   = $f_week_end_time ;
			        	/*$query  = "SELECT * FROM location_info WHERE id='$loc_id'";
			        	//echo $query;
			        	$result = getData($query);
			        	while($row = mysqli_fetch_array($result) )
			        	{
			        		
			        	}*/
				}
			//	echo $f_end_time;

		// check if date exist for exact location and facilities type
		$chk_data_r = "SELECT id FROM facilities_booking WHERE facilities_type=".$facility_type." AND location_id=".$loc_id." AND facility_id=".$facility_id." AND book_date='".$bookdate."'";
		
		$rskdata =getData($chk_data_r);
		//echo $chk_data_r;
		//$fchk_date =0;
		$fchk_date = mysqli_num_rows($rskdata);
		//echo $fchk_date;
		if ($fchk_date == 0)
		{
			// New entry. Valid
			$valid_flag = 1;			
			
			//echo " 1st Trap $fchk_date $facility_type $locid $facility_id ";
		}
		else		
		{
			//echo "2nd Trap<br>";
			while($r = mysqli_fetch_array($rskdata))
			{
				$date_entry_id = $r['id'];
			
			}
		//	echo "Date entry id".$date_entry_id;
			//$date_entry_id = mysqli_fetch_array($chk_data_r);
						
			$pstarttime = trim($starttime);
					
				// Populate the time slots via insert
				$inc_time = $pstarttime;
				for ($i = 0; $i < $num_slots; $i++)
				{
					if ($i==0)
					{
						$time_slot_sql_field_stmt = "`".$pstarttime."` = '0'";
						//$time_slot_sql_value_stmt = "'".$bookid."'";	
					}
					else
					{
						$inc_time = $inc_time + 100;
						
						if ($inc_time >= $f_end_time)
						{
							$valid_flag = 0;
							$pass_day_hour_flag = 1;
							
							//echo $inc_time." <br>";
						}
						else
						{
							$time_slot_sql_field_stmt .= " AND "."`".$inc_time."` = '0'";
						}
						//$time_slot_sql_value_stmt .= ", '".$bookid."'";	
					}
					
				}
				
				if ($pass_day_hour_flag == 0)
				{
					// do insert
					$data_q = "SELECT id FROM facilities_booking WHERE ".$time_slot_sql_field_stmt." AND id='".$date_entry_id."'"; 
					//echo $data_q;
					$data_r = getData($data_q);
					$q_count = mysqli_num_rows($data_r);
				
					//$jmsg .= "You do not access rights to this section.";
						
					/*echo "<script language=\"javascript\"> alert('$data_q')</script>";*/
					if ($q_count != 0)
					{
						// no result found. Good valid 
						 $valid_flag = 1;
					}
					else
					{
						 $valid_flag = 0;
					}
				}
		}

		//echo $data_q;
		
		if ($f_end_time == 0)
		{
			$valid_flag = 0;
		}
		//echo $valid_flag;
		return $valid_flag;
		
	}

/*function //enable_cors()() 
{
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: GET, POST');
	header("Access-Control-Allow-Headers: X-Requested-With");	
	date_default_timezone_set('asia/singapore');
	//date_default_timezone_set('UTC');
}*/



function getFacilitiesProductInfo($client_void, $x_dest_id, $facility_id)
{

   	$data_r ="SELECT product_id, product_name, currency, price FROM products WHERE location_id=".$client_void." AND x_dest_location_id=".$x_dest_id." AND location_facilities_id=".$facility_id." ORDER BY product_name";
		$result = getData($data_r);
   	while($row = mysqli_fetch_array($result))
   	{
   		$meeting_rm_price['product_id']		=	$row['product_id'];
   		$meeting_rm_price['product_name']	=	$row['product_name'];
   		$meeting_rm_price['price']        	=	$row['price'];
   		$meeting_rm_price['currency']			=	$row['currency'];
   	
   	
   	}
   	//$meeting_rm_price = mysql_fetch_array($data_r);
   	 return $meeting_rm_price;

}


function update_client_facility_booking_hours($cid, $num_hours, $num_hours_left, $ftype, $f_ref_id)
	{
				$num_hour_deduct= 0;
				$num_hour_deduct = $num_hours_left - $num_hours;
				if ($ftype == 1)
				{
					$update_booking_stmt = "UPDATE client_facilities_core SET meeting_room_hours_left=".$num_hour_deduct." WHERE status='1' AND 
client_id = '".$cid."' AND id='$f_ref_id'";
				}
				elseif ($ftype == 2)
				{
					$update_booking_stmt = "UPDATE client_facilities_core SET day_office_hours_left=".$num_hour_deduct." WHERE status='1' AND client_id 
= '".$cid."' AND id='$f_ref_id'";
				}
				elseif ($ftype == 3)
				{
						$update_booking_stmt = "UPDATE client_facilities_core SET hot_desking_hours_left=".$num_hour_deduct." WHERE status='1' AND 
client_id = '".$cid."' AND id='$f_ref_id'";
				}
				elseif ($ftype == 4)
				{
						$update_booking_stmt = "UPDATE client_facilities_core SET flexi_office_hours_left=".$num_hour_deduct." WHERE status='1' AND 
client_id = '".$cid."' AND id='$f_ref_id'";
				}
				elseif ($ftype == 5)
				{
						$update_booking_stmt = "UPDATE client_facilities_core SET discussion_room_hours_left=".$num_hour_deduct." WHERE status='1' 
AND client_id = '".$cid."' AND id='$f_ref_id'";
				}

				$update_booking_result = setData($update_booking_stmt);
}


function update_facilities_booking($cid,$locid,$facility_type,$pax, $bookdate,$starttime, $num_slots, $addon, $void, $inv_id, $staff_id)
{

		$mysqli = new mysqli("myvoffice.me", "myvoff_entrp", "V2PM@.@tGr!Z", "myvoff_vos");
		$result = $mysqli->query("INSERT INTO client_booking_log(client_id, facilities_type, book_date, book_pax, book_addon, book_start_time, book_hours_slots, status, vo_id, invoice_id,staff_id)VALUES(".$cid.", ".$facility_type.",'".$bookdate."' , ".$pax.", '".$addon."', ".$starttime.", ".$num_slots.", 1, ".$void.", ".$inv_id.", ".$staff_id.")");
		$last_book_id = $mysqli->insert_id;
		$closeResults = $mysqli->close();
		return $last_book_id;
}





	function update_facilities_booking_table_v2($bookid, $locid, $facility_type, $facility_id, $bookdate, $starttime, $num_slots)
	{
		$round_the_clock = 0;
		
		// check to use check week day start time or weekend		
		$check_is_Sat = isSaturday($bookdate);
		$query  = "SELECT * FROM location_info WHERE id='$locid'";
			        				        //	echo $query;
     	$result = getData($query);
		while($row = mysqli_fetch_array($result) )
     	{
     		$f_fac_start_time = $row['facility_start_time'];
     		$f_fac_end_time   = $row['facility_end_time'];
     		$f_week_start_time = $row['facility_weekend_start_time'];
			$f_week_end_time   = $row['facility_weekend_end_time'];
     	}
		// not sat
		if ($check_is_Sat == false)
				{
			        
			        /*	$query  = "SELECT * FROM location_info WHERE id='$loc_id'";
			        				        //	echo $query;
			        	$result = getData($query);
			        	while($row = mysqli_fetch_array($result) )
			        	{
			        		
			        	}*/
			        	$f_start_time = $f_fac_start_time;
			        		$f_end_time   = $f_fac_end_time;
			        	
				}
				else
				{
			        $f_start_time = $f_week_start_time;
			        		$f_end_time   = $f_week_end_time ;
			        	/*$query  = "SELECT * FROM location_info WHERE id='$loc_id'";
			        	//echo $query;
			        	$result = getData($query);
			        	while($row = mysqli_fetch_array($result) )
			        	{
			        		
			        	}*/
				}
		/*if ($check_is_Sat == false)
				{
			        	/*$f_start_time = $bookingfunc->get_facilities_booking_weekdays_start_time($loc_id);
			        	$f_end_time = $bookingfunc->get_facilities_booking_weekdays_end_time($loc_id);
			        	$query  = "SELECT * FROM location_info WHERE id='$loc_id'";
			        	$result = getData($query);
			        	while($row = mysqli_fetch_array($result) )
			        	{
			        		$f_start_time = $row['facility_start_time'];
			        		$f_end_time   = $row['facility_end_time'];
			        	}
			        	
				}
				else
				{
			        
			        	$query  = "SELECT * FROM location_info WHERE id='$loc_id'";
			        	$result = getData($query);
			        	while($row = mysqli_fetch_array($result) )
			        	{
			        		$f_start_time = $row['facility_weekend_start_time'];
			        		$f_end_time   = $row['facility_weekend_end_time'];
			        	}
				}*/
		
		// check if 24/7
		if ($f_start_time == 100 && $f_end_time == 2400)
		{
			$round_the_clock = 1;	
		}

		
		// check if date exist for exact location and facilities type
		$q3 = "SELECT id FROM facilities_booking WHERE facility_id=".$facility_id." AND location_id=".$locid." AND book_date='".$bookdate."'";
		$chk_data_r = getData($q3);
		$fchk_date = mysqli_num_rows($chk_data_r);
		while($resk = mysqli_fetch_array($chk_data_r))
		{
		
				$date_entry_id = $resk['id'];
		
		}

		$pstarttime = $starttime;
				
		// 1 already exist 0 does not exist
		if ($fchk_date == 0)
		{
			// Populate the time slots via insert
			$inc_time = $pstarttime;
			for ($i = 0; $i < $num_slots; $i++)
			{
				if ($i==0)
				{
					$time_slot_sql_field_stmt = "`".$pstarttime."`";
					$time_slot_sql_value_stmt = "'".$bookid."'";	
				}
				else
				{
					$inc_time = $inc_time + 100;
					if ($round_the_clock == 1 && $inc_time == 2500)
					{
						$inc_time = 100;
					}
					$time_slot_sql_field_stmt .= ", "."`".$inc_time."`";
					$time_slot_sql_value_stmt .= ", '".$bookid."'";	
				}
				
			}

			// do insert
			$data_q = "INSERT INTO facilities_booking(facilities_type, location_id, facility_id, book_date, ".$time_slot_sql_field_stmt.") VALUES(".$facility_type.", ".$locid.",".$facility_id.",'".$bookdate."', ".$time_slot_sql_value_stmt.")";
			$data_r = setData($data_q);
		}
		else
		{
			// Populate the time slots via insert
			$inc_time = $pstarttime;
			for ($i = 0; $i < $num_slots; $i++)
			{
				if ($i==0)
				{
					$time_slot_sql_field_stmt = "`".$pstarttime."`="."'".$bookid."'";
				}
				else
				{
					$inc_time = $inc_time + 100;
					if ($round_the_clock == 1 && $inc_time == 2500)
					{
						$inc_time = 100;
					}

					$time_slot_sql_field_stmt .= ", `".$inc_time."`="."'".$bookid."'";	
				}
				
			}

			// do update

			$update_booking_stmt = "UPDATE facilities_booking SET ".$time_slot_sql_field_stmt." WHERE id = '".$date_entry_id."'";
						//echo $update_booking_stmt;

			$update_booking_result = setData($update_booking_stmt);
			
		}
		
		
	}

function create_client_facilities_invoice($cid, $void, $facility_type, $num_hours, $book_date, $rm_location, $productid)
	{
		//include "mdb.php";

		$inv_table = "client_invoices_".$void;
		
		// set custom invoice plan id as 0;
		$plan_id = 0;
		
		$incurrency = getLocationCurrency($void);
		
		$meeting_rm_loc = $rm_location;

		$facilities_name = get_Facilities_Type($facility_type);
		$product_name = $meeting_rm_loc." ".$facilities_name." ".$num_hours ." Additional Hour(s) Rental.";
		
		$comments = $num_hours ." Hour(s) Rental.";
		$price = get_Product_Price($productid);

		$f_total = "Additional ". $price * $num_hours;
		
		$addinvstmt = "INSERT INTO ".$inv_table."	
					(vo_id, client_id, client_plans_id, description, date_generated, date_due, currency, amount_due, invoice_status, additional_comments, period_from, period_to) 
					VALUES 
					('$void', '$cid', '$plan_id', '$product_name','$book_date', '$book_date', '$incurrency', '$f_total', '1', '$comments', '$book_date', '$book_date')";
		
		$mysqli = new mysqli("myvoffice.me", "myvoff_entrp", "V2PM@.@tGr!Z", "myvoff_vos");
		$result = $mysqli->query($addinvstmt);
		$inv_id = $mysqli->insert_id;
		$closeResults = $mysqli->close();
		$optype = 1; // Create New Invoice
		logInvoiceTransaction($void, $inv_id, $optype, "0");
		
		$f_inv_id = $void."-".$inv_id;
		//echo "Client Added";
		return $f_inv_id;
		
	}

	function getLocationCurrency($vid)
	{

      $cycle_check = "SELECT currency FROM location_info WHERE id=".$vid;
		$bill_cycle_result = getData($cycle_check);
		while($row= mysqli_fetch_array($bill_cycle_result))
		{
			$bill_cycle = $row['currency'];
		
		}
	//	$bill_cycle = mysql_fetch_row($bill_cycle_result);
		return $bill_cycle;

	}
	function get_Facilities_Type($id)
	{	
		$data_r = "SELECT facilities_type FROM facilities_type WHERE id =".$id;
		
		$fdata = getData($data_r);
		while($row= mysqli_fetch_array($fdata))
		{
			$ftype = $row['facilities_type'];
		
		}

		return $ftype;
	}
function get_Product_Price($pid)
	{

      $loc_check = "SELECT price FROM products WHERE product_id=".$pid;
		$loc_info = getData($loc_check);
		while($row= mysqli_fetch_array($loc_info))
		{
			$price = $row['price'];
		
		}
		
		return $price;
	}

function logInvoiceTransaction($void, $inv_id, $optype, $staff)
	{
		//include "../include/config.php";
		//$cbbm = mysql_connect (DB_HOST, DB_USER, DB_PASSWORD) or die ('I cannot connect to the database because: ' . mysql_error());
		//mysql_select_db (DB_NAME) or die("Could not select database \n"); 

		$addinvstmt = "INSERT INTO invoice_transaction_log(vo_id, invoice_id, optype, staff)VALUES(".$void.",".$inv_id.",".$optype.", ".$staff.")";
			
		$resultaddinvstmt =setData($addinvstmt);

	}
function validate_input($input) 
{	
  $input = trim($input);
  //$input = stripslashes($input);
  $input = addslashes($input);
  $input = htmlspecialchars($input);
  return $input;
}

?>