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
		$amount=BASIS*(float)$_POST['amount'];
		$userID=(int)$_POST['userid'];
		if($userID && $amount)
		{
			$time=time();
			db_connect();
			mysql_query("begin");
			try{
				
				$sql="SELECT BTC,USD from Users where userID=$userID";
				if(!$data=mysql_query($sql))  throw new Exception("SQL Error: $sql");
				if(!$row=mysql_fetch_array($data)) throw new Exception("Invalid UserID $userID");
				
				$userBTC=$row['BTC']+$amount;
				$userUSD=$row['USD'];
				
				$sql="UPDATE Users set BTC=BTC+$amount where UserID=$userID";
				if(!mysql_query($sql)) throw new Exception("SQL Error $sql");
				
				
				$sql="INSERT into Activity (UserID,DeltaUSD,DeltaBTC,Type,BTC,USD,Date) values ($userID,0,$amount,9,$userBTC,$userUSD,$time)";
				if(!mysql_query($sql)) throw new Exception("SQL Error $sql");
				
				mysql_query('commit');
				
				checkBidOrders($userID);
				
				$result['status'] = "Added!";
			}catch(Exception $e)
			{
				mysql_query("rollback");
				
				$result['error'] = $e->getMessage();
			}
			
		}
		
	}else $result['error'] = "Not admin.";
}else
{ // not found in db
	$result['error'] = "Not logged in." ;
}

echo( json_encode($result));

?>