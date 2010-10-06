<?php
include('../../../noserve/config.inc');
include('../lib/functions.inc');
include('../lib/session.php');
include('../lib/common.inc');

// make sure:
// user is logged in 

$result=array();

if(isset($_SESSION['UserID']))
{
	$uid=(int)($_SESSION['UserID']);
	
	db_connect();
	$newToken=generateRandomString(20);
	$sql="UPDATE Users set merchToken='$newToken' where userID=$uid";
	mysql_query($sql);
	$result['token']=$newToken;
	
}else
{ // not found in db
	$result['error']="You must be logged in.";
}

echo( json_encode($result));

?>