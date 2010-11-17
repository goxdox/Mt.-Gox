<?php
include('../../../noserve/config.inc');
include('../lib/functions.inc');
include('../lib/common.inc');
define('NUM_PERIODS',500);

/*
Format for History data:
result[plot][n][open,high,low,close,volume]
result[date]  	// end date
result[period]  	// period length in min

*/

function refreshData($filename,$json)
{	
	$time=time();
	$period=$json['period'];
	$startDate=$json['date'];
	$endDate=$startDate+$period;
	$index=count($json['plot'])-1;
	$lastValidPrice=$json['plot'][$index][3];
	
	while($endDate<$time)
	{
		$sql="SELECT MAX(Price),MIN(Price),SUM(Amount) from Trades where Date<$endDate and Date>$startDate";
		$data=mysql_query($sql);
		if($data)
		{
			$row=mysql_fetch_array($data);
			if($row)
			{
				$high=(float)$row[0];
				if(!$high) $high=$lastValidPrice;
				
				$low=(float)$row[1];
				if(!$low) $low=$lastValidPrice;
				
				$volume=(float)$row[2];
				
				$sql="SELECT Price From Trades where Date<$endDate and Date>$startDate order by date desc limit 1";
				$close=(float)getSingleDBValue($sql);
				if(!$close) $close=$lastValidPrice;
				else $lastValidPrice=$close;
				
				$sql="SELECT Price From Trades where Date<$endDate and Date>$startDate order by date limit 1";
				$open=(float)getSingleDBValue($sql);
				if(!$open) $open=$lastValidPrice;
				
				$json['plot'][$index]=array( 0 => $open, 1 => $high, 2=> $low,  3 => $close, 4 => round($volume/BASIS,0) );
				$index++;
			}
		}
		
		$startDate=$endDate;
		$endDate=$startDate+$period;
	}
	// keep the history to only the last N periods
	if($index>NUM_PERIODS)
	{
		$json=array_slice($json,$index-NUM_PERIODS);
	}
	$json['date']=$endDate;
	
	$resultStr=json_encode($json);
	
	$fd = fopen($filename, "w");
	if($fd)
	{
		fwrite($fd, $resultStr);
		fclose($fd);
	}
	die($resultStr);
}

$timeScale=$_POST['timeScale'];

if($timeScale==0 || $timeScale==1 || $timeScale==5 || $timeScale==15 || $timeScale==30 || $timeScale==60 || $timeScale==1440)
{
	db_connect();
	
	if($timeScale)
	{
		$time=time();
		$filename=DATA_DIR."history.$timeScale.json";
		if(file_exists($filename)) 
		{
			if(($time-filemtime($filename))/60 > $timeScale)
			{ 	// data in cache file is old
				$contents=file_get_contents($filename);
				$json=json_decode($contents,true);
				
				refreshData($filename,$json);
			}else 
			{
				echo(file_get_contents($filename));
				die();
			}
		}else 
		{ // need to make a new cache file
			
			$time=time();
			
			$json['period']=$timeScale*60;
			$remainder=$time%$json['period'];
			$json['date']=$time-$remainder-$json['period']*NUM_PERIODS;
			$json['plot'][0]=array( 0 => 0, 1 => 0, 2=> 0,  3 => 0, 4 => 0 );
			
			refreshData($filename,$json);
		}
	}else
	{
		$result['period']=0;
		$startTime=time()-24*60*60;
	
		$sql="SELECT Price,Date,Amount From Trades where Date>$startTime order by Date";
		
		$data=mysql_query($sql);
		if($data)
		{
			$result['plot'][0] = array( 0 => 0, 1 => 0, 2 => 0 );
			
			$price=0;
			$count=0;
			while($row=mysql_fetch_array($data))
			{	
				$price=(float)$row[0];
				$date=(int)$row[1];
				$volume=(int)$row[2];
				
				$result['plot'][$count] = array( 0 => $date, 1 => $price, 2 => $volume );
				$count++;
			}	
		}else $result['error']="SQL Failed.";
	}
}else $result['error']="Unsupported";

echo( json_encode($result));

?>