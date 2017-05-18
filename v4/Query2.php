<?php
	include 'DBCon2.php';
	function setData($qry)
	{
		$obj=new DBCon();
		$res=$obj->submitQuery($qry);
		return $res;
	}
	function getData($qry)
	{
		$obj=new DBCon();
		$res=$obj->selectQuery($qry);
		return $res;
	}
?>