<?php
require_once ('Query.php'); 
require '../flight/Flight.php';
//echo "hiiii";
/* *************************Routes starts here*******************************/
/*** created by : Annie      ***/
/*** Created at : May 15, 2017***/
Flight::route('/', function()
{

	echo "hello world";

});
//Route to get Locations with room facility
//Annie, May 15, 2017
Flight::route('/getRoom', function()
{
	//enable_cors()();
	$returnarray=getRoom();
	header('Content-type:application/json;charset=utf-8');
	echo json_encode($returnarray);

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
Flight::route('/searchCenter', function()
{
	header('Content-type:application/json;charset=utf-8');
	header('Authorization: Bearer {0ccb5842a2b04d0b9fdf23cddd01209d}');
	/*$opts = array(
  'http'=>array(
    'method'=>"GET",
    'header'=>"Content-type:application/json;charset=utf-8" .
              "Authorization: Bearer {984e6416dfea4be0b79816938f1253ec}\r\n"

  )
);
*/
//$context1 = stream_context_create($opts);

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

	echo json_encode($json);

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
Flight::route('/getAvailableTimeSlot', function()
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
Flight::route('/getBookingDetails', function()
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
function searchCenter()
{
	//sheader('Content-Type: application/json');
ob_start();
	$i=0;
	/*$dummy = json_decode($_GET['search']);	
	echo $dummy;
	/*foreach($dummy->city as $key=>$value)
	{
		$search = $value;
	
	
	}
	*/
	$obj = json_decode(file_get_contents('php://input'), true);
	//echo $obj;
	$parameters = $obj['parameters'];
	$search = $parameters['search'];
//	$search  = $d['city']; 
	
//	$obj = json_decode($json);
	//print_r($obj);
	//$search =$_GET['search'];
	$query  = "SELECT * FROM location_info WHERE location_desc like '%".$search."%'";
//	echo $query;
	$res    = getData($query);
	$count_res = mysqli_num_rows($res);
	if($count_res > 0)
	{
		while($row = mysqli_fetch_array($res))
		{
			$res_loc = $row['location_desc'];
					//print_r($res_loc);
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
	$context = array(array("name" => $next_context, "parameters" =>
array("param1" => $param1value, "param2" => $param2value)));
	//$context = "";
	$json = json_encode([
                'speech'   => $speech,
                'displayText' => $speech,
                'data' => [],
                'contextOut' => [$context],
                'source' => $source
        ]);




/*	
	$array['speech'] = $speech;
	$array['displayText'] = $speech;
	$array['data']['contextOut'] =[]; 
	$array['source'] = "v4";
	*/

	/*$array  =  (
        "speech" => $speech,
        "displayText" => $speech,
       
        "source" =>"api-bot"
    );*/
   
	ob_end_clean();
//	echo json_encode($array);
	return $json;
	
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
function getAvailableTimeSlot()
{
			$cid = $_POST["cid"];
			$token = $_POST["token"];
			$bookdate = $_POST["bookdate"];
			$loc_id = $_POST["void"];
			$facilities = $_POST["ftype"];
			$facility_id = $_POST["fid"];
			
			/*$cid = "10002";
			$token = "50b2061fc834cedec6def1affd60e998";
			$bookdate = "2014-07-19";
			$loc_id = "21";
			$facilities = "1";
			$facility_id = "52";*/
			
			
			$auth_token = portal_getUserToken($cid);
			
			$cnt = 0;
			
			if ($token != "" || $cid != "")
			{
			   if( $token == $auth_token )
			   {
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
			
				$q1  =     "SELECT * FROM facilities_booking WHERE facilities_type='$facilities' AND facility_id='$facility_id' AND location_id='$loc_id' AND book_date='$bookdate'";
				$pre_check_dates  = getData($q1);
				$date_exist = mysqli_num_rows($pre_check_dates);
       		$check_dates = mysqli_fetch_array($pre_check_dates);
				$a_time_slots = "";
				if ($date_exist != 0)
				{
			
					$loop_time = $f_start_time;
			
					
			   		while ($loop_time != $f_end_time)
			   		{
			    	   		$period_end_time = $loop_time + 100;
					
						if ($check_dates[$loop_time] == 0)
						{
							$a_time_slots .= $loop_time."-". $period_end_time." ";
			   		}
			    	   $loop_time = $loop_time + 100;
							
					}
				}
				else
				{
					$loop_time = $f_start_time;
			
			   		while ($loop_time != $f_end_time)
			   		{
			    	   		$period_end_time = $loop_time + 100;
                  
								$a_time_slots .= $loop_time."-". $period_end_time." ";
			
			           		$loop_time = $loop_time + 100;
			
			   		}
				}
			
			
					$timeslotobj[$cnt] = array(
			                              "Time Slots" => $a_time_slots
			                				);
   				$f_atime_slot = array("Available Time Slots" => $timeslotobj);
					return $f_atime_slot;
			
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

function getBookingList()
{

			$agent = "10000";
			
			$cid = $_POST["cid"];
			$token = $_POST["token"];
			
			/*$cid = "10002";
			$token = "50b2061fc834cedec6def1affd60e998";*/
			
			$auth_token =  portal_getUserToken($cid);
			
			$cnt = 0;
			
			if ($token != "" || $cid != "")
			{
			   if( $token == $auth_token )
			   {
			
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

function getBookingDetails()
{

			$agent = "10000";
			
			/*$cid = $_POST["cid"];
			$token = $_POST["token"];
			$bookid = $_POST["bookid"];*/
			
			$cid = "10002";
			$token = "50b2061fc834cedec6def1affd60e998";
			$bookid = "14331";
			
			
			$auth_token = portal_getUserToken($cid);
			
			$cnt = 0;
			
			if ($token != "" || $cid != "")
			{
			   if( $token == $auth_token )
			   {
			
					  $myquery = "SELECT * FROM client_booking_log WHERE book_id='$bookid' AND status='1'";
					  
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
						$query3 = "SELECT location_id FROM facilities_booking WHERE".$starth." = ".$data['book_id']." AND facilities_type='".$data['facilities_type']."' AND book_date='".$data['book_date']."'";
						$res3  = getData($query3);
						while($r1 = mysqli_fetch_array($res3))
						{
								$loc_id = $r1['location_id'];
						
						}
//						$loc_id = $bookingfunc->get_location_id_via_booking_table($data["facilities_type"], $data["book_date"], $starth, $data["book_id"]);
						$vo_name = getVOName($loc_id);
			
						$bookobj[$cnt] = array(
			                        "location" => $vo_name,
			               			"book id" => $data["book_id"],
			              				"facility name" => $type_name,
			                        "pax" => $data["book_pax"],
			                        "addon" => $data["book_addon"],
			                        "time" => $f_time,
			                        "duration" => $duration." Hour(s)",
			               			"book date" => $data["book_date"]
			
			               		);
			
						//$cnt++;
					}
			
					$f_book = array("Facility Booking Details" => $bookobj);
			                echo json_encode($f_book);
			
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
      $data_q = "SELECT location_desc FROM location_info WHERE id='$void'";
	   $data_r = getData($data_q);

		$fdata = mysqli_fetch_row($data_r);
		return $fdata[0];
}

/*function //enable_cors()() 
{
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: GET, POST');
	header("Access-Control-Allow-Headers: X-Requested-With");	
	date_default_timezone_set('asia/singapore');
	//date_default_timezone_set('UTC');
}*/
function validate_input($input) 
{	
  $input = trim($input);
  //$input = stripslashes($input);
  $input = addslashes($input);
  $input = htmlspecialchars($input);
  return $input;
}

?>