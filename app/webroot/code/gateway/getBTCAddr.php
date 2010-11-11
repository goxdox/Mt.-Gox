<?php
include('../../../noserve/config.inc');
include('../lib/functions.inc');
include('../lib/common.inc');
include('../lib/bitcoin.inc');

if(isset($_REQUEST['merchID']) && isset($_REQUEST['amount']))
{
	db_connect();
	
	$amount=(float)$_REQUEST['amount']; 
	$custom=mysql_real_escape_string($_REQUEST['custom']); 
	$merchID=(int)$_REQUEST['merchID'];    
	$notify_url=mysql_real_escape_string($_REQUEST['notify_url']); 
	
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
		}else die("SQL Failed"); //$result['error']="SQL Failed.";
	}catch(Exception $e)
	{
		 die("Exception"); //$result['error']="Exception!";
	}
}else  die("Invalid"); //$result['error']="Invalid";

$line= "Make your own Mt Gox account for quicker and easier transfers.";

?>

<html>
<body>
Send <?php echo($amount); ?> Bitcoins to this address: <p>
<?php echo($addr); ?>
<p><hr><p><p>
<?php echo($line); ?>
</body>
</html>
