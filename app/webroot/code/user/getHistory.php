<?php
include('../../../noserve/config.inc');
include('../lib/functions.inc');
include('../lib/session.php');
include('../lib/common.inc');

// make sure:
// user is logged in 
if(isset($_SESSION['UserID']))
{
	$uid=(int)($_SESSION['UserID']);
	if(isset($_POST['start'])) $start=$_POST['start'];
	else $start=0;
	
	db_connect();
	$result = array();
	
	$sql="SELECT deltaBTC,deltaUSD,type,typeData,btc,usd,date FROM Activity where UserID=$uid order by date desc limit $start,30";
	$data=mysql_query($sql);
	if($data)
	{
		$count=0;
		while($row=mysql_fetch_array($data,MYSQL_ASSOC))
		{
			$row['btc']=round($row['btc']/BASIS,2);
			$row['usd']=round($row['usd']/BASIS,2);
			$row['deltaBTC']=round($row['deltaBTC']/BASIS,2);
			$row['deltaUSD']=round($row['deltaUSD']/BASIS,2);
			$row['type']=(int)$row['type'];
			
			$result['history'][$count]=$row;
	
			$count++;
		}
	}else $result['error']='SQL Failed.';
	
	
}else
{ // not found in db
	$result=array( 'error' => "Not logged in." );
}

echo( json_encode($result));

?>