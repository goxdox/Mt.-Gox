<?php
include('../../../noserve/config.inc');
include('../lib/functions.inc');
include('../lib/session.php');
include('../lib/common.inc');


if(isset($_SESSION['UserID']))
{
	$adminID=(int)($_SESSION['UserID']);
	if($adminID==1)
	{
		$amount=BASIS*(float)	$_POST['amount'];
		$uid=(int)$_POST['userid'];
		if($uid && $amount)
		{
			db_connect();
			$sql="SELECT USD from Users where userID=$uid";
			$usdHeld=getSingleDBValue($sql);
			if($usdHeld<$amount)
			{
				$result['error'] = "You don't have this much USD.";
			}else
			{
				$time=time();
				$sql="INSERT INTO USDRecord (FromID,ToID,Amount,Reason,Date) values ($uid,0,$amount,4,$time)";
				mysql_query($sql);
				
				$sql="UPDATE Users set USD=USD-$amount where UserID=$uid";
				mysql_query($sql);
				
				checkBidOrders($uid);
				$result['status'] = "Everything is ok";
			}
			
		}
		
	}
}else
{ // not found in db
	$result=array( 'error' => "Not logged in." );
}

echo( json_encode($result));

?>