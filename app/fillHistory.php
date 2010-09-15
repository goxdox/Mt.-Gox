<?php 
include('noserve/config.inc');
include('webroot/code/lib/functions.inc');
include('webroot/code/lib/common.inc');

db_connect();

$sql="SELECT * FROM Trades";
$data=mysql_query($sql);
while($row=mysql_fetch_array($data))
{
	$buyerID=$row['BuyerID'];
	$sellerID=$row['SellerID'];
		
	$deltaUSD=$row['Amount']*$row['Price'];
	$date=$row['Date'];
	$tradeID=$row['TradeID'];
	
	$sql="INSERT INTO Activity (userid,deltaBTC,deltaUSD,type,typeID,btc,usd,date) values ($buyerID,$amount,-$deltaUSD,2,$tradeID,0,0,$date";
	mysql_query($sql);
	$sql="INSERT INTO Activity (userid,deltaBTC,deltaUSD,type,typeID,btc,usd,date) values ($sellerID,-$amount,$deltaUSD,1,$tradeID,0,0,$date";
	mysql_query($sql);
}

//$sql="SELECT * FROM AddBTC where status="


?>