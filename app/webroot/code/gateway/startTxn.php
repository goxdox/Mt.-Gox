<?php
include('../../../noserve/config.inc');
include('../lib/functions.inc');
include('../lib/session.php');
include('../lib/common.inc');
include('../lib/bitcoin.inc');

 

if( isset($_REQUEST['merchID']) && 
	isset($_REQUEST['amount']) && 
	$_REQUEST['amount']>0 )
{
	$amount=(float)$_REQUEST['amount']; 
	$custom=mysql_real_escape_string($_REQUEST['custom']); 
	$merchID=(int)$_REQUEST['merchID'];    
		
	if(isset($_SESSION['UserID']))
	{
		$uid=(int)($_SESSION['UserID']);
		$name=$_SESSION['UserName'];
		if($_SESSION['btc']>=$amount) $result['funds']=1;
		
		$result['name'] = $name;
	}else
	{
		$name=0;
		$uid=0;
	}
	
	db_connect();
	
	$time=time();
	
	$txn_id=generateRandomString(8);
	
	try {
	
		$sql="INSERT INTO MerchantOrders (MerchantID,CustomerID,Amount,Custom,txn_id,Date) values ($merchID,$uid,$amount,'$custom','$txn_id',$time)";
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

if($name)
{ 
	if($funds) $line="<a href='https://mtgox.com/users/confirm/$orderID' style='font-size: .8em;'>Hi $name pay instantly with your Mt. Gox account</a>";
	else $line="<a href='https://mtgox.com/users/addFunds' style='font-size: .8em;'>Hi $name add funds to your Mt. Gox account so you can pay instantly.</a>";
}else $line= "<a href='http://mtgox.com' style='font-size: .6em;'>Make your own Mt Gox account for quicker and easier transfers</a>";

?>


<html>
<body>
<a href="http://bitcoin.org" target="_blank" style="font-size: .8em;" >What is bitcoin?</a><p><p>
Send <?php echo($amount); ?> Bitcoins to this address: <p>
<?php echo($addr); ?>
<p><hr><p><p>
<?php echo($line); ?>
</body>
</html>
