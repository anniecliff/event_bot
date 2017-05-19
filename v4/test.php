<?php
/*require_once ('Query.php'); 
require 'flight/Flight.php';

$sql = "SELECT * FROM location_info JOIN location_facilities";
$data = getData($sql);
echo json_encode($data);*/
header('Content-Type: application/json');
ob_start();
echo "hello";
ob_end_clean();
?>