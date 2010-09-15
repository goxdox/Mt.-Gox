<?php
include('../../../noserve/config.inc');
include('../lib/functions.inc');
include('../lib/session.php');
include('gateway.inc');

// make sure:
// user is logged in 
// valid amount
// user has enough BTC
//


if(isset($_SESSION['UserID']))
{
	$uid=(int)($_SESSION['UserID']);
	$orderID=(int)($_POST['orderID']);
	
	$sql="SELECT MerchantID,Amount,Custom,txn_id from btcx.MerchantOrders where OrderID=$orderID and CustomerID=$uid and status=0";
	if( $data=mysql_query($sql))
	{
		if($row=mysql_fetch_array($data))
		{
			$amount=$row['Amount'];
			$merchID=$row['MerchantID'];
			
			if($amount>0)
			{
				$btcHeld=getSingleDBValue("Select BTC from Users where UserID=$uid");
				if($btcHeld>$amount)
				{
					$sql="UPDATE Users set BTC=BTC-$amount where UserID=$uid";
					mysql_query($sql);
					$sql="UPDATE Users set BTC=BTC+$amount where UserID=$merchID";
					mysql_query($sql);
					$sql="INSERT INTO BTCRecord (FromID,ToID,Amount,Reason,Date) values ($uid,$merchID,$amount,2,$time)";
					mysql_query($sql);
					
					checkAskOrders($uid);
					checkAskOrders($merchID);
					
					notifyMerch($merchID,$row['Custom'],$row['txn_id']);
					 
				}else $result['error']="Insufficient Funds.";
			}else $result['error']="Invalid";
			
		}else $result['error']="Order Not found.";	
	}else $result['error']='SQL Error';
	
	
}else
{
	$result['error'] = "Not Logged in";
}


echo( json_encode($result));

?>