<?php
include('../../noserve/config.inc');
include('lib/functions.inc');
include('lib/session.php');
include('lib/common.inc');

// make sure:
// user is logged in 
// user owns this order

if(!isset($_SESSION['UserID']))
{
	if(isset($_POST['name']) && isset($_POST['pass']))
	{
		$name=mysql_real_escape_string($_POST['name']);
		$pass=mysql_real_escape_string($_POST['pass']);
		
		// check these against the db
		$md5pass=md5($pass);
		$clean_name=strtolower($name);
		$sql = "select userid from Users where CleanName='$clean_name' and password='$md5pass'";
		$uid=getSingleDBValue($sql);
	}
}else
{
	$uid=(int)($_SESSION['UserID']);
}

if($uid)
{	
	$oid=(int)$_POST['oid'];
	$type=(int)$_POST['type'];
	
	if($oid && $type)
	{
		db_connect();
		
		if($type==1)
		{
			$table="Asks";
		}else
		{
			$table="Bids";
		}
		$sql="DELETE FROM $table where UserID=$uid and OrderID=$oid";
		if( mysql_query($sql) )
		{
			$result=array();
			if($type==1) checkAskOrders($uid);
			else  checkBidOrders($uid);
			getOrders($uid);
			getFunds($uid);
			updateTicker(0);
		}else $result=array( 'error' => "SQL Error." );
		
	}else $result=array( 'error' => "Invalid." );
}else
{ // not found in db
	$result=array( 'error' => "Not logged in." );
}

echo( json_encode($result));

?>