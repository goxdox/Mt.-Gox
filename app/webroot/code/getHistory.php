<?php
include('../../noserve/config.inc');
include('lib/functions.inc');
include('lib/session.php');


$result=array();
$result['plot']=array();
//$result['plot']['period']=(60*60);
//$result['plot']['start']=100000;
//$result['plot']['data']= array( 0 => 1, 1 => 3, 2 => 2);

db_connect();

$startTime=time()-24*60*60*2;

$sql="SELECT Price,Date From Trades where Date>$startTime order by Date";



$data=mysql_query($sql);
if($data)
{
	$price=0;
	$count=1;
	while($row=mysql_fetch_array($data))
	{	
		$price=(float)$row[0];
		$date=(int)$row[1]*1000;
		if($count==1) $result['plot']['data'][0] = array( 0 => $startTime*1000, 1 => $price );
		$result['plot']['data'][$count] = array( 0 => $date, 1 => $price );
		$count++;
	}
	$date=(int)time()*1000;
	$result['plot']['data'][$count] = array( 0 => $date, 1 => $price );
	
}else $result=array( 'error' => "SQL Failed." );

echo( json_encode($result));

?>