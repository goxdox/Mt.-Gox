<?php
include('../../../noserve/config.inc');
include("../lib/functions.inc");
include("../lib/common.inc");

logMsg("start ipn.php");

global $LIQPAY_MERCHANT_ID;
global $LIQPAY_SIG;

function insertOrder($txn_id,$userID,$amount)
{	
	logMsg("4");
	$time=time();
	$sql = "INSERT INTO LiqpayOrders (txn_id, userID, amount, date) values ($txn_id, $userID, $amount, $time)";

	$result = mysql_query($sql);
	if(!$result) { logMsg("ipn.php: $query failed"); exit(); }
	$sql="SELECT LAST_INSERT_ID()";
	$orderID=getSingleDBValue($sql);
	logMsg("4.1");
	mysql_query('BEGIN');
	try{
		$netAmount=$amount*BASIS;
		$sql="Update Users set USD=USD+$netAmount where userid=$userID";
		if(!mysql_query($sql)) throw new Exception($sql);
		logMsg("4.2");
		$sql="SELECT USD,BTC from Users where UserID=$userID";
		if(!($data=mysql_query($sql))) throw new Exception($sql);
		if(!($row=mysql_fetch_array($data)))  throw new Exception("User not found");
		$usd=$row[0];
		$btc=$row[1];
		$sql="INSERT into Activity (UserID,deltaUSD,type,TypeID,BTC,USD,Date) values ($userID,$netAmount,6,$orderID,$btc,$usd,$time)";
		if(!mysql_query($sql)) throw new Exception($sql);
		logMsg("4.3");
		mysql_query('COMMIT');
	}catch(Exception $e)
	{
		mysql_query("rollback");
		logMsg("ipn.php: $sql failed"); 
		exit(); 
	}
	logMsg("4.4");
}

function parseTag($rs, $tag) 
{            
   $rs = str_replace("\n", "", str_replace("\r", "", $rs));
   $tags = '<'.$tag.'>';
   $tage = '</'.$tag;
   $start = strpos($rs, $tags)+strlen($tags);
   $end = strpos($rs, $tage);
   return substr($rs, $start, ($end-$start)); 
}
 
 db_connect();

$insig = $_POST['signature'];
$resp = base64_decode($_POST['operation_xml']);
logMsg($resp);

$orderIDArray=explode('.',parseTag($resp, 'order_id') );
logMsg("1");
$userID = $orderIDArray[0];
logMsg("2: $userID");
$status = parseTag($resp, 'status');

//$payrez['response_description'] = parseTag($resp, 'response_description');
$txn_id = parseTag($resp, 'transaction_id');
//$payrez['pay_details'] = parseTag($resp, 'pay_details');
//$payrez['pay_way'] = parseTag($resp, 'pay_way');
$amount = parseTag($resp, 'amount');

$gensig = base64_encode(sha1($LIQPAY_SIG.$resp.$LIQPAY_SIG,1));
logMsg("3: $status $txn_id $amount");


if ($insig == $gensig)
{
	if($status == 'failure' )
	{
		logMsg("liqpay failure: $userID");
	}else if($status == 'success')
	{
		$sql="SELECT count(*) from LiqpayOrders where txn_id='$txn_id'";
		if(getSingleDBValue($sql))
		{
			logMsg("liqpay duplicate: $userID");
		}else insertOrder($txn_id,$userID,$amount);
	}else if($status=='wait_secure')
	{
		logMsg("liqpay wait_secure: $userID");
	}
}else
{
	logMsg("Invalid liqpay sig: $userID"); 
}

logMsg("done"); 
?>


