<?php
include('../../../noserve/config.inc');
include('../lib/functions.inc');
include('../lib/common.inc');
include('../lib/bitcoin.inc');

if(isset($_REQUEST['merchID']) && isset($_REQUEST['amount']))
{
	db_connect();
	
	$amount=(int)($_REQUEST['amount']*BASIS); 
	$custom=mysql_real_escape_string($_REQUEST['custom']); 
	$merchID=(int)$_REQUEST['merchID'];    
	$notify_url=mysql_real_escape_string($_REQUEST['notify_url']); 
	
	//logMsg("getBTCAddr $amount , $custom , $merchID , $notify_url");
	
	$time=time();
	
	$txn_id=generateRandomString(8);
	
	try {
	
		$sql="INSERT INTO MerchantOrders (MerchantID,CustomerID,Amount,Custom,notifyURL,txn_id,Date) values ($merchID,0,$amount,'$custom','$notify_url','$txn_id',$time)";
		if( mysql_query($sql))
		{
			$orderID=getSingleDBValue("SELECT LAST_INSERT_ID()");
			$addr=BC_getNewAddr("m$orderID");
			$sql="UPDATE MerchantOrders set RecvAddr='$addr' where OrderID=$orderID";
			mysql_query($sql);
			$result['btcAddr']=$addr;
		}else $result['error']="SQL Failed.";
	}catch(Exception $e)
	{
		 $result['error']="Exception!";
	}
}else  $result['error']="Invalid";

echo( json_encode($result));

?>

