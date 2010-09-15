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
	}
	
}else
{ // not found in db
	$result=array( 'error' => "Not logged in." );
}

echo( json_encode($result));

?>