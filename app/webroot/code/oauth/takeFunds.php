<?php
include('../../../noserve/config.inc');
include('../lib/functions.inc');
include('../lib/common.inc');
include('../lib/session.php');
include('gateway.inc');

// make sure:
// merch has a correct token
// valid amount
// user has enough BTC
// user has authed for enough

db_connect();
if( (isset($_POST['merchID']) && $_POST['merchID']>0) &&
	(isset($_POST['userID']) && $_POST['userID']>0) &&
	isset($_POST['token']) &&
	(isset($_POST['amount']) && $_POST['amount']>0) )
{
	$userID=$_POST['userID'];
	$merchID=$_POST['merchID'];
	$token=$_POST['token'];
	$amount=$_POST['amount']*BASIS;
	
	$sql="SELECT AmountLeft from PreAuth where UserID=$userID and MerchID=$merchID and Token=$token and status=1";
	$amountLeft=getSingleDBValue($sql);
	
	if($amountLeft>=$amount)
	{ 
		$sql="SELECT username,btc,usd,fundsHeld,paypalTrust from Users where userid=$userID";
	
		mysql_query("BEGIN");
		try
		{
			if(!$data=mysql_query($sql)) throw new GoxException("SQL Error",$sql);
			if(!$row=mysql_fetch_array($data)) throw new GoxException("User not found");
			
			$btcHeld=$row['btc'];
			$usdHeld=$row['usd'];
			$minFundsHeld=$row['fundsHeld']*(1-$row['paypalTrust']);
			$time=time();
			$customerName=$row['username'];
			$custom=mysql_real_escape_string($_POST['custom']);
			
			$sql="SELECT username,usd,btc from Users where userID=$merchID";
			if(!$data=mysql_query($sql)) throw new GoxException("SQL Error",$sql);
			if(!$row=mysql_fetch_array($data)) throw new GoxException("Merchant not found");
			$mUsdHeld=$row['usd'];
			$mBtcHeld=$row['btc'];
			$merchName=$row['username'];
			$txn_id=generateRandomString(8);
			
			
				if($btcHeld>=$amount)
				{
					$accountTotal=$usdHeld+($btcHeld-$amount)*.06;
					if($accountTotal < $minFundsHeld)
					{
						$allowedBTC= (int)(($btcHeld -(($minFundsHeld-$usdHeld)/.06))/BASIS);
						throw new GoxException("Insufficient funds");
					}else
					{
						
						$sql="UPDATE Users set BTC=BTC-$amount where UserID=$userID";
						if(!mysql_query($sql)) throw new GoxException("SQL Error",$sql);
						$sql="UPDATE Users set BTC=BTC+$amount where UserID=$merchID";
						if(!mysql_query($sql)) throw new GoxException("SQL Error",$sql);
						
						$btcHeld -= $amount;
						$sql="INSERT into Activity (UserID,DeltaBTC,Type,TypeData,BTC,USD,Date) values ($userID,-$amount,7,'$merchName',$btcHeld,$usdHeld,$time)";
						if(!mysql_query($sql)) throw new GoxException("SQL Error",$sql);
						$mBtcHeld += $amount;
						$sql="INSERT into Activity (UserID,DeltaBTC,Type,TypeData,BTC,USD,Date) values ($merchID,$amount,7,'$customerName',$mBtcHeld,$mUsdHeld,$time)";
						if(!mysql_query($sql)) throw new GoxException("SQL Error",$sql);

						$sql="INSERT INTO MerchantOrders (MerchantID,CustomerID,currency,Amount,AmountRecv,Custom,txn_id,Status,Date) values ($merchID,$userID,2,$amount,$amount,'$custom','$txn_id',1,$time)";
						if(!mysql_query($sql)) throw new GoxException("SQL Error",$sql);
					
						
						mysql_query('commit');
						
						checkAskOrders($uid);
						checkAskOrders($merchID);
						
						$result['result']=1;
					}
					
					 
				}else throw new GoxException("Insufficient Funds.");
			
		}catch(GoxException $e)
		{
			mysql_query("rollback");
			$result['error'] = $e->getMessage();
			$e->log();
		}
	}else $result['error']="Amount over Authorization";
}else $result['error']="Invalid";

echo( json_encode($result));


?>