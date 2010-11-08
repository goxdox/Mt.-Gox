<?php
include('../../../noserve/config.inc');
include('../lib/common.inc');
include('../lib/functions.inc');
include('../lib/session.php');


$result=array();
$result['asks']=array();
$result['bids']=array();
//$result['plot']['start']=100000;
//$result['plot']['data']= array( 0 => 1, 1 => 3, 2 => 2);

db_connect();

$sql="SELECT LastPrice From Ticker";
$price=getSingleDBValue($sql);
$minPrice=$price*.75;
$maxPrice=$price*1.7;

$sql="SELECT value from Options where name=1";
if(getSingleDBValue($sql)==1)
{
	$doit=true;
}else $doit=false;

$sql="SELECT Amount,Price From Asks where status=1 and price<$maxPrice and price>$minPrice order by Price";
$data=mysql_query($sql);
if($data)
{
	$count=0;
	$result['asks'][$count] = array();
	while($row=mysql_fetch_array($data))
	{	
		$amount=(float)$row[0]/BASIS;
		$price=(float)$row[1];
		
		if($price>.37)
		{
			$result['asks'][$count] = array( 0 => .37, 1 => 15000 );
			$count++;
		}
		
		if($count && $price==$result['asks'][$count-1][0])
		{
			$result['asks'][$count-1][1] += $amount;
		}else
		{
			$result['asks'][$count] = array( 0 => $price, 1 => $amount );
			$count++;
		}
	}
}else $result=array( 'error' => "SQL Failed." );

$sql="SELECT Amount,Price From Bids where status=1 and price<$maxPrice and price>$minPrice order by Price";
$data=mysql_query($sql);
if($data)
{
	$count=0;
	$result['bids'][$count] = array();
	while($row=mysql_fetch_array($data))
	{	
		$amount=$row[0]/BASIS;
		$price=(float)$row[1];
		
		if($count && $price==$result['bids'][$count-1][0])
		{
			$result['bids'][$count-1][1] += $amount;
		}else
		{
			$result['bids'][$count] = array( 0 => $price, 1 => $amount );
			$count++;
		}
	}
}else $result['error'] = "SQL Failed.";


$data=mysql_query($sql);
if($data)
{
	$count=0;
	$result['bids'][$count] = array();
	while($row=mysql_fetch_array($data))
	{	
		$amount=$row[0]/BASIS;
		$price=(float)$row[1];
		
		if($count && $price==$result['bids'][$count-1][0])
		{
			$result['bids'][$count-1][1] += $amount;
		}else
		{
			$result['bids'][$count] = array( 0 => $price, 1 => $amount );
			$count++;
		}
	}
}else $result['error'] = "SQL Failed.";




echo( json_encode($result));


?>
