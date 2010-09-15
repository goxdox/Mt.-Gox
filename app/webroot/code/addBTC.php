<?php
include('../../noserve/config.inc');
include('lib/functions.inc');
include('lib/session.php');
include('lib/common.inc');
include('lib/bitcoin.inc');

// make sure:
// user is logged in 
// valid amount
// valid address


if(isset($_SESSION['UserID']))
{
	$uid=(int)($_SESSION['UserID']);
	$amount=BASIS*(float)$_POST['amount'];    
		
	db_connect();
	
	$time=time();
	
	//$addr="16cdCJbC2PHY9nhxrMCFdRws1ozFZNbEEE";
	try {
	//$addr=BC_getNewAddr($uid);

	$sql="INSERT INTO AddBTC (UserID,Amount,Date) values ($uid,$amount,$time)";
	if( mysql_query($sql))
	{
		$addID=getSingleDBValue("SELECT LAST_INSERT_ID()");
		$addr=BC_getNewAddr("$addID");
		$sql="UPDATE AddBTC set RecvAddr='$addr' where AddID=$addID";
		mysql_query($sql);
		
		$result=array( 'btcAddr' => $addr );
	}else $result=array( 'error' => "SQL Failed." );
	}catch(Exception $e)
	{
		$result=array( 'error' => "Exception!" );
	}
}else
{ // not found in db
	$result=array( 'error' => "Not logged in." );
}

echo( json_encode($result));

?>