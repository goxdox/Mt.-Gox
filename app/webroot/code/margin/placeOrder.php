<?php
include('../../../noserve/config.inc');
include('../lib/functions.inc');
include('../lib/common.inc');
include('../lib/session.php');
include('margin.inc');
// make sure:
// user is logged in 
// valid amount
// user has enough Money
// profit > price
// loss < price


/////////////////////////////////////////////////////////////////
if(isset($_SESSION['UserID']))
{
	$userID=(int)($_SESSION['UserID']);
	
	if( isset($_POST['long']) && isset($_POST['price']) && isset($_POST['amount']) && $_POST['amount']>0)
	{
		$amount=$_POST['amount']*BASIS;
		$price=$_POST['price'];
		$long=$_POST['long'];
		$loss=$_POST['loss'];
		$profit=$_POST['profit'];
		
		db_connect();
		$time=time();
		
		$sql="SELECT marginBalance from Users where userid=$userID";
		mysql_query("BEGIN");
		try
		{
			if(!$data=mysql_query($sql)) throw new Exception("SQL Error");
			if(!$row=mysql_fetch_array($data)) throw new Exception("invalid");
			
			$marginBalance=$row['marginBalance'];
			
			
			if($marginBalance < $amount ) throw new Exception("Not Enough in your margin Account.");
			 
			
			$sql="UPDATE Users set USD=USD+$amount, marginBalance=marginBalance-$amount where UserID=$userID";
			if(!mysql_query($sql)) throw new Exception("SQL Error");
				
			$usd += $amount;
			$marginBalance -= $amount;
			$sql="INSERT into Activity (UserID,DeltaUSD,Type,BTC,USD,Date) values ($userID,$amount,12,$btc,$usd,$time)";
			if(!mysql_query($sql)) throw new Exception("SQL Error");
					
			mysql_query('commit');
				
			checkBidOrders($userID);
			checkMarginCall($userID);
					
			$result['status']='Funds transferred';
			$result['margin']=round($marginBalance/BASIS,2);			
		}catch(Exception $e)
		{
			mysql_query("rollback");
			logMsg("margin/add: $sql");
			$result['error'] = $e->getMessage();
		}
	}else $result['error']="Invalid";
}else $result['error']="Not logged in.";

echo( json_encode($result));
?>