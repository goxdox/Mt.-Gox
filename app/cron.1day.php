<?php 
include('noserve/config.inc');
include('webroot/code/lib/functions.inc');
include('webroot/code/lib/common.inc');
include('webroot/code/lib/bitcoin.inc');



// should be run every day
$result=array();


$filename="/var/www/www.mtgox.com/gox/app/webroot/data/allHistory.json";
$contents=file_get_contents($filename);
if($contents)
{
  $json=json_decode($contents,true);
  //echo( print_r($json));
	$t=$json['plot'];
	//echo( print_r($t));
	$index=count($t);
	$startDate=$json['start']+(60*60*24)*$index;
}else 
{
	$index=0;
	$startDate=1279424586;
}
			

db_connect();

$endDate=$startDate+(60*60*24);
if($endDate<time())
{
	//High FLOAT,Low FLOAT,Volume FLOAT,Date INT UNSIGNED
	$sql="SELECT MAX(Price),MIN(Price),SUM(Amount) from Trades where Date<$endDate and Date>$startDate";
	$data=mysql_query($sql);
	if($data)
	{
		
		$row=mysql_fetch_array($data);
		if($row)
		{
			
			$high=(float)$row[0];
			$low=(float)$row[1];
			$volume=(float)$row[2];
			
			$sql="SELECT Price From Trades where Date<$endDate order by date desc limit 1";
			$close=(float)getSingleDBValue($sql);
			
			$sql="SELECT Price From Trades where Date<$endDate and Date>$startDate order by date limit 1";
			$open=(float)getSingleDBValue($sql);
			
			
			$json['plot'][$index]=array( 0 => $open, 1 => $high, 2=> $low,  3 => $close );
			$json['vol'][$index]= $volume/BASIS;
			$json['start']=1279424586;
			$json['period']=(60*60*24);
			
			$fd = fopen($filename, "w");
			if($fd)
			{
				fwrite($fd, json_encode($json));
				fclose($fd);
			}
		}
	}
}

include('webroot/code/paypal/updateAvailable.php');


?>