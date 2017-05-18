<?php
require_once ('Query.php'); 
require 'flight/Flight.php';

/*Flight::route('/getLocation', function()
{
   enable_cors();	
   services_included();	
	$returnarray=getLocation();
	header('Content-type:application/json;charset=utf-8');
	echo json_encode($returnarray);

});
*/

//function getLocation() {
$sql = "SELECT * FROM location_info JOIN location_facilities";
$data = getData($sql);
print_r($data);
/*return $data;
}*/

?>