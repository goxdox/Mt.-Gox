<?php
include('../../noserve/config.inc');
include('lib/functions.inc');
include('lib/session.php');
include('lib/common.inc');

// make sure:
// user is logged in 


if(isset($_SESSION['UserID']))
{
	$uid=(int)($_SESSION['UserID']);
	
	db_connect();
	$result = array();
	
	getOrders($uid);
	getFunds($uid);
}else
{ // not found in db
	$result=array( 'error' => "Not logged in." );
}

echo( json_encode($result));

?>