<?php
include('../../noserve/config.inc');
include('lib/functions.inc');
include('lib/session.php');
include('lib/common.inc');

// make sure:
// user is logged in 

function getBought($uid,$start)
{
	global $result;
	
	$sql="SELECT Amount,Price,Date FROM Trades where BuyerID=$uid order by date desc limit $start,30";
	$data=mysql_query($sql);
	if($data)
	{
		$count=0;
		while($row=mysql_fetch_array($data))
		{
			$amount=$row['Amount']/BASIS;
			$price=round($row['Price'],ROUNDING);
			$date=$row['Date'];
				
			//ID, Name, Desc, # members, $ in treasury, Time Till Vote, Time Till Execute, if you are a memeber, group options?
			$offer=array('amount' => $amount, 'price' => $price, 'date' => $date );
			$result['bought'][$count]=$offer;
	
			$count++;
		}
	}else $result=array( 'error' => "SQL Failed." );
	
}

function getSold($uid,$start)
{
	global $result;
	
	$sql="SELECT Amount,Price,Date FROM Trades where SellerID=$uid order by date desc limit $start,30";
	$data=mysql_query($sql);
	if($data)
	{
		$count=0;
		while($row=mysql_fetch_array($data))
		{
			$amount=$row['Amount']/BASIS;
			$price=round($row['Price'],ROUNDING);
			$date=$row['Date'];
				
			//ID, Name, Desc, # members, $ in treasury, Time Till Vote, Time Till Execute, if you are a memeber, group options?
			$offer=array('amount' => $amount, 'price' => $price, 'date' => $date );
			$result['sold'][$count]=$offer;
	
			$count++;
		}
	}else $result=array( 'error' => "SQL Failed." );
}

if(isset($_SESSION['UserID']))
{
	$uid=(int)($_SESSION['UserID']);
	if(isset($_POST['type'])) $type=$_POST['type'];
	else $type=0;
	if(isset($_POST['start'])) $start=$_POST['start'];
	else $start=0;
	
	db_connect();
	$result = array();
	
	if($type==0)
	{
		getBought($uid,$start);
		getSold($uid,$start);
	}else if($type==1)
	{
		getSold($uid,$start);
	}else if($type==2)
	{
		getBought($uid,$start);
	}
	
}else
{ // not found in db
	$result=array( 'error' => "Not logged in." );
}

echo( json_encode($result));

?>