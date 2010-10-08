<?php
include('../../../noserve/config.inc');
include('../lib/functions.inc');
include('../lib/session.php');
include('../lib/common.inc');

global $GOX_BOT_ID;

if(isset($_SESSION['UserID']))
{
	$adminID=(int)($_SESSION['UserID']);
	if($adminID==1)
	{
		$userID=(int)$_POST['userid'];
		if($userID)
		{
			db_connect();
			mysql_query("begin");
			try{
			
				$sql="SELECT BTC,USD from Users where userID=$userID";
				if(!$data=mysql_query($sql))  throw new Exception("SQL Error: $sql");
				if(!$row=mysql_fetch_array($data)) throw new Exception("Invalid UserID $userID");
				
				$userBTC=$row['BTC'];
				$userUSD=$row['USD'];
				
				$sql="SELECT BTC,USD from Users where userID=$GOX_BOT_ID";
				if(!$data=mysql_query($sql))  throw new Exception("SQL Error: $sql");
				if(!$row=mysql_fetch_array($data)) throw new Exception("Invalid gox $GOX_BOT_ID");
				
				$goxBTC=$row['BTC'];
				$goxUSD=$row['USD'];
				
				
				$time=time();
				$sql="UPDATE Users set USD=0,BTC=0 where userid=$userID";
				if(!mysql_query($sql)) throw new Exception("SQL Error $sql");
				$sql="UPDATE Users set USD=USD+$userUSD,BTC=BTC+$userBTC where UserID=$GOX_BOT_ID";
				if(!mysql_query($sql)) throw new Exception("SQL Error $sql");
				
				$goxUSD += $userUSD;
				$goxBTC += $userBTC;
				$sql="INSERT into Activity (UserID,DeltaUSD,DeltaBTC,Type,BTC,USD,Date) values ($userID,-$userUSD,-$userBTC,8,0,0,$time)";
				if(!mysql_query($sql)) throw new Exception("SQL Error $sql");
				
				$sql="INSERT into Activity (UserID,DeltaUSD,DeltaBTC,Type,TypeID,BTC,USD,Date) values ($GOX_BOT_ID,$userUSD,$userBTC,8,$userID,$goxBTC,$goxUSD,$time)";
				if(!mysql_query($sql)) throw new Exception("SQL Error $sql");
				
				
				mysql_query('commit');
				
				checkBidOrders($userID);
				checkAskOrders($userID);
				
				checkBidOrders($GOX_BOT_ID);
				checkAskOrders($GOX_BOT_ID);
				$result['status'] = "Claimed!";
			}catch(Exception $e)
			{
				mysql_query("rollback");
				
				$result['error'] = $e->getMessage();
			}
			
		}
		
	}
}else
{ // not found in db
	$result=array( 'error' => "Not logged in." );
}

echo( json_encode($result));

?>