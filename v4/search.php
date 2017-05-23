<?php

header('Content-Type: application/json');
ob_start();
$i=0;
$search =$_POST['search'];	
$mysqli = new mysqli("myvoffice.me", "myvoff_entrp", "V2PM@.@tGr!Z", "myvoff_vos");
$result = $mysqli->query("SELECT * FROM location_info WHERE location_desc like '%".$search."%'")
while($row = $result->fetch_array()){
			$data['location_name'][$i] = $row['location_desc'];
			$i++;
}



	/*$search = validate_input($_POST['search']);	
	$query  = "SELECT * FROM location_info WHERE location_desc like '%".$search."%'";
	$res    = getData($query);
	$count_res = mysqli_num_rows($res);
	if($count_res > 0)
	{
		while($row = mysqli_fetch_array($res))
		{
			$data['location_name'][$i] = $row['location_desc'];
			$data['timezone'][$i]      = $row['time_zone'];
			$i++;
		}	
	
	}
	else 
	{
		$data = "No locations found";
	}
//	echo json_encode($data);
	return $data;*/
ob_end_clean();
echo json_encode($data);
?>