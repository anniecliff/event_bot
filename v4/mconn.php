<?php

include "mdb.php";
$dbm = mysql_connect (DB_HOST, DB_USER, DB_PASSWORD) or die ('I cannot connect to the database because: ' . mysql_error());
mysql_select_db (DB_NAME) or die("Could not select database \n");


?>
