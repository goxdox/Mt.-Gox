<?php
include('../../../noserve/config.inc');
include('../lib/functions.inc');

//echo(print_r($_POST));

db_connect();

$merchID=(int)$_REQUEST['merchID'];
$amount=round((float)$_REQUEST['amount'],4); 
$txn_id=mysql_real_escape_string($_REQUEST['txn_id']);

if($merchID && $amount && $txn_id)
{
	
	
	$sql="SELECT Amount from MerchantOrders where txn_id='$txn_id' and status=1 and MerchantID=$merchID";
	$dbAmount=round(getSingleDBValue($sql)/BASIS,2);
	if($dbAmount==$amount) 
	{
		echo("ok");
	}else echo("no");
}else echo("invalid");

?>