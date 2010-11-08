<?php
include('../../../noserve/config.inc');
include('../lib/functions.inc');

include('../lib/common.inc');


db_connect();
	
$sql="SELECT * From Ticker";
$data=mysql_query($sql);
if($data)
{
	$row=mysql_fetch_array($data);
	if($row)
	{		
		$high=round( $row['High'],ROUNDING);
		$low=round( $row['Low'],ROUNDING);
		$vol=round( $row['Volume']/BASIS,0);
		$buy=round( $row['HighBuy'],ROUNDING);
		$sell=round( $row['LowSell'],ROUNDING);
		$lastPrice=round( $row['LastPrice'],ROUNDING);
		
		$result['ticker']= array( 'high' => $high, 'low' => $low, 'vol' => $vol, 'buy' => $buy , 'sell' => $sell, 'last' => $lastPrice);
	}else $result=array( 'error' => "No Ticker data?" );
}else
{
	$result['error'] = "SQL Failed.";
	$result['debug'] = $sql;
}


echo( json_encode($result));


?>
