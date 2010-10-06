<?php
include('../../../noserve/config.inc');
include('../lib/functions.inc');
include('../lib/common.inc');
include('../lib/session.php');
include('gateway.inc');

// make sure:
// user is logged in 
// valid amount
// user has enough BTC
//
// $postVars ="merchID=$gGoxMerchID&token=$goxToken&item=$itemName&receiver=$receiver&currency=$currency&amount=$amount";

db_connect();
if( (isset($_POST['merchID']) && $_POST['merchID']>0) &&
	(isset($_POST['currency'])) &&
	(isset($_POST['token'])) &&
	(isset($_POST['receiver'])) &&
	(isset($_POST['item'])) &&
	(isset($_POST['amount']) && $_POST['amount']>0) )
{
	$merchID=(int)$_POST['merchID'];
	$token=mysql_real_escape_string($_POST['token']);
	$currency=$_POST['currency'];
	$itemName=mysql_real_escape_string($_POST['item']);
	
	$sql="SELECT btc,usd,fundsHeld,paypalTrust from Users where userid=$merchID and merchToken='$token' and payAPIOn=1";
	
	mysql_query("BEGIN");
	try
	{
		if(!$data=mysql_query($sql)) throw new Exception("SQL Error");
		if(!$row=mysql_fetch_array($data)) throw new Exception("invalid");
		
		$merchBTC=$row['btc'];
		$merchUSD=$row['usd'];
		$minFundsHeld=$row['fundsHeld']*(1-$row['paypalTrust']);
		
		
		$userName=mysql_real_escape_string($_POST['receiver']);
		$cleanName=strtolower($userName);
		$sql="SELECT userID,usd,btc from Users where cleanname='$cleanName'";
		if(!$data=mysql_query($sql)) throw new Exception("SQL Error");
		if(!$row=mysql_fetch_array($data)) throw new Exception("none");
		$userBTC=$row['btc'];
		$userUSD=$row['usd'];
		
		$time=time();
		
		if($currency=="USD")
		{
			if($merchUSD >= $amount )
			{
				$accountTotal=($merchUSD-$amount)+$merchBTC*.06;
				if($accountTotal < $minFundsHeld)
				{
					throw new Exception("held");
				}else
				{
					$sql="UPDATE Users set USD=USD-$amount where userid=$merchID";
					if(!mysql_query($sql)) throw new Exception("SQL Error");
					$sql="UPDATE Users set USD=USD+$amount where UserID=$userID";
					if(!mysql_query($sql)) throw new Exception("SQL Error");
					
					$merchUSD -= $amount;
					$sql="INSERT into Activity (UserID,DeltaUSD,Type,TypeData,BTC,USD,Date) values ($merchID,-$amount,7,'$userName',$merchBTC,$merchUSD,$time)";
					if(!mysql_query($sql)) throw new Exception("SQL Error $sql");
					$userUSD += $amount;
					$sql="INSERT into Activity (UserID,DeltaUSD,Type,TypeData,BTC,USD,Date) values ($userID,$amount,7,'$itemName',$userBTC,$userUSD,$time)";
					if(!mysql_query($sql)) throw new Exception("SQL Error $sql");
					
					
					mysql_query('commit');
					
					checkBidOrders($userID);
					checkBidOrders($merchID);
					echo("ok");
				}
			}else throw new Exception("broke");
			
		}else if($currency_code=="BTC")
		{
			if($btcHeld>=$amount)
			{
				$accountTotal=$usdHeld+($btcHeld-$amount)*.06;
				if($accountTotal < $minFundsHeld)
				{
					throw new Exception("held");
				}else
				{
					$sql="UPDATE Users set BTC=BTC+$amount where UserID=$userID";
					if(!mysql_query($sql)) throw new Exception("SQL Error $sql");
					$sql="UPDATE Users set BTC=BTC-$amount where UserID=$merchID";
					if(!mysql_query($sql)) throw new Exception("SQL Error $sql");
					
					$merchBTC -= $amount;
					$userBTC += $amount;
					$sql="INSERT into Activity (UserID,DeltaBTC,Type,TypeData,BTC,USD,Date) values ($userID,$amount,7,'$itemName',$btcHeld,$usdHeld,$time)";
					if(!mysql_query($sql)) throw new Exception("SQL Error $sql");
					
					$sql="INSERT into Activity (UserID,DeltaBTC,Type,TypeData,BTC,USD,Date) values ($merchID,-$amount,7,'$userName',$merchBTC,$merchUSD,$time)";
					if(!mysql_query($sql)) throw new Exception("SQL Error $sql");
				
					
					mysql_query('commit');
					
					checkAskOrders($uid);
					checkAskOrders($merchID);
					
					echo("ok");
				}
				
				 
			}else throw new Exception("broke");
		}else throw new Exception("invalid");	
	}catch(Exception $e)
	{
		mysql_query("rollback");
		
		echo($e->getMessage());
	}
}else echo("invalid");


?>