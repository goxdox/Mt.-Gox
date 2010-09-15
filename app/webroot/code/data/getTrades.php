<?php
include('../../../noserve/config.inc');
include('../lib/functions.inc');
include('../lib/session.php');

$result=array();

db_connect();

$startTime=time()-24*60*60*2;

$sql="SELECT Amount,Price,Date From Trades where Date>$startTime order by Date";

$data=mysql_query($sql);
if($data)
{
	$count=0;
	while($row=mysql_fetch_array($data))
	{	
		$amount=$row[0]/1000;
		$price=(float)$row[1];
		$date=(int)$row[2];
		$result[$count]['date']=$date;
		$result[$count]['price']=$price;
		$result[$count]['amount']=$amount;
		$count++;
	}	
}

echo( json_encode($result));

?>