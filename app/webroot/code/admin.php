<?php
include('../../noserve/config.inc');
include('lib/functions.inc');
include('lib/session.php');
include('lib/common.inc');
include('lib/bitcoin.inc');
include('paypal/masspay.php');
// make sure:
// user is logged in 
// valid amount
// valid address

function printStats()
{
	$sql="SELECT sum(USD)/1000,sum(BTC)/1000,count(*) from Users";
	$data=mysql_query($sql);
	$row=mysql_fetch_array($data);
	$usd=$row[0];
	$btc=$row[1];
	$ave=$usd/$btc;
	$num=$row[2];
	$sql="SELECT LastPrice,Volume from Ticker";
	$data=mysql_query($sql);
	$row=mysql_fetch_array($data);
	
	$pace=$row[1]*365*.018*$row[0];
	
	return("USD($usd) BTC($btc) $ave Num Users($num) Pace: $pace");
}


if($_SESSION['UserID']==1)
{
	$uid=(int)($_SESSION['UserID']);
	$cmd=$_POST['cmd'];

	$result=array();
	switch($cmd)
	{
		case 'stop':
			BC_shutdown();
		break;
		
		case 'process':
			db_connect();
			BC_process_AddFunds();
			BC_process_Merch();
		break;
		
		case 'info':
			$result['status']=BC_info();
			break;
			
		case 'masspay':
			$email=$_POST['email'];
			$amount=$_POST['amount'];
			
			$result['status']=paypalWithdraw($email,$amount);
			break;		
			
		case 'stats':
			$result['status']=printStats();
		break;
	}
	
}else
{ // not found in db
	$result['error'] ="Not logged in.";
}

echo( json_encode($result));

?>