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
		$tradeID=(int)$_POST['tradeid'];
		if($tradeID)
		{
			db_connect();
			
			mysql_query("begin");
			try{
				
				$sql="SELECT amount,price,buyerID,sellerID from Trades where tradeid=$tradeID";
				if(!$data=mysql_query($sql))  throw new Exception("SQL Error: $sql");
				if(!$row=mysql_fetch_array($data)) throw new Exception("Invalid TradeID $tradeID");
				
				$btc=$row['amount'];
				$price=$row['price'];
				$buyerID=$row['buyerID'];
				$sellerID=$row['sellerID'];
				
				$usd=$btc*$price;
			
				$sql="DELETE From Trades where tradeid=$tradeID";
				if(!mysql_query($sql)) throw new Exception("SQL Error $sql");
				
				$sql="DELETE From Activity where typeid=$tradeID";
				if(!mysql_query($sql)) throw new Exception("SQL Error $sql");
				
			
				$sql="UPDATE Users set USD=USD+$usd, BTC=BTC-$btc where userid=$buyerID";
				if(!mysql_query($sql)) throw new Exception("SQL Error $sql");
				$sql="UPDATE Users set USD=USD-$usd, BTC=BTC+$btc where userid=$sellerID";
				if(!mysql_query($sql)) throw new Exception("SQL Error $sql");
				
				
				mysql_query('commit');
				
				checkBidOrders($buyerID);
				checkAskOrders($buyerID);
				
				checkBidOrders($sellerID);
				checkAskOrders($sellerID);
				$result['status'] = "Reversed!";
			}catch(Exception $e)
			{
				mysql_query("rollback");
				
				$result['error'] = $e->getMessage();
			}
			
		}else $result['error'] = "No trade?";
		
	}else $result['error'] = "Not admin.";
}else
{ // not found in db
	$result['error'] = "Not logged in.";
}

echo( json_encode($result));

?>