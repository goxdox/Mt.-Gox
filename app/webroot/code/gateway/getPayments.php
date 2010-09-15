<?php
include('../../../noserve/config.inc');
include('../lib/functions.inc');
include('../lib/session.php');
include('../lib/common.inc');

// make sure:
// user is logged in 

$result=array();

if(isset($_SESSION['UserID']))
{
	$uid=(int)($_SESSION['UserID']);
	
	db_connect();
	$sql="SELECT Amount,AmountRecv,Custom,Status,Date FROM MerchantOrders where MerchantID=$uid and status != 2";
	$data=mysql_query($sql);
	
	if($data)
	{
	  $count=0;
		while($row=mysql_fetch_array($data))
		{
		  			
			$amount=$row['Amount'];
			$amountr=$row['AmountRecv'];
			$status=$row['Status'];
			$custom=$row['Custom'];
			$date=$row['Date'];
				
			$offer=compact('amount','amountr','status','custom','date');
			$result['payments'][$count]=$offer;
	
			$count++;
		}
	}else $result=array( 'error' => "SQL Failed." );
	
}else
{ // not found in db
	$result=array( 'error' => "Not logged in." );
}

echo( json_encode($result));

?>