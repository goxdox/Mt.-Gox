<?php
include('../../noserve/config.inc');
include('lib/functions.inc');
include('lib/session.php');

// make sure:
// user is logged in 
// valid amount
// valid address
$result=array();

if(isset($_SESSION['UserID']))
{
	$uid=(int)($_SESSION['UserID']);
	$email=mysql_real_escape_string($_POST['email']);
	$noteurl=mysql_real_escape_string($_POST['noteurl']);
	
	if(isset($_POST['notify']) && $_POST['notify']=='on')
		$notify=1;
	else $notify=0;
	
	if(isset($_POST['merch']) && $_POST['merch']=='on')
		$merch=1;
	else $merch=0;
	
	
	    
	//$result['debug']=print_r($_POST);
		
	db_connect();
	
	$sql="UPDATE Users set Email='$email', TradeNotify=$notify, MerchOn=$merch, MerchNotifyURL='$noteurl' where UserID=$uid";
	if( mysql_query($sql))
	{
		$_SESSION['Merch']=$merch;
		$result=array( 'status' => "Settings Changed" );
	}else $result=array( 'error' => "SQL Failed." );
	
}else
{ // not found in db
	$result['error']="Not logged in.";
}

echo( json_encode($result));

?>