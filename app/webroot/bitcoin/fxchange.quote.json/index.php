<?php 
include('../../../noserve/config.inc');
include('../../code/lib/functions.inc');
include('../../code/lib/common.inc');


db_connect();
$bid=0;
$ask=0;
	
$sql="SELECT HighBuy,LowSell From Ticker";
$data=mysql_query($sql);
if($data)
{
	$row=mysql_fetch_array($data);
	if($row)
	{	
		$bid=$row[0];
		$ask=$row[1];
	}
}

$result['time']=time();
$result['pair']="BCUSD";
$result['bid']=$bid;
$result['ask']=$ask;


echo( json_encode($result));

//[ { "time": 1279085779, "pair": "BCUSD", "bid": .00013, "ask": .000135 }, { "time": 1279085779, "pair": "BCCHF", "bid": .00220, "ask": .00230 } ]
?>