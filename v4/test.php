<?php
//echo json_encode($data);
header('Content-Type: application/json');
ob_start();
/*$servername = "myvoffice.me";
$username = "myvoff_entrp";
$password = "V2PM@.@tGr!Z";
$database = "myvoff_vos";
// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
echo "Connected successfully";
    mysqli_select_db($database);

$sql = "SELECT * FROM location_info";
$data = mysqli_query($conn,$sql);*/
$mysqli = new mysqli("myvoffice.me", "myvoff_entrp", "V2PM@.@tGr!Z", "myvoff_vos");
$result = $mysqli->query("SELECT * FROM location_info");
$row = $result->mysqli_fetch_all();
//echo htmlentities($row['_message']);
$output = "hello ";
ob_end_clean();
echo json_encode($row);
?>