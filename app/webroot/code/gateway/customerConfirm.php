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

db_connect();
if( (isset($_POST['merchID']) && $_POST['merchID']>0) &&
	(isset($_POST['currency_code'])) &&
	(isset($_POST['amount']) && $_POST['amount']>0) )
{
	if(isset($_SESSION['UserID']))
	{
		$userID=(int)($_SESSION['UserID']);
		$sql="SELECT userid,username,btc,usd,fundsHeld,paypalTrust from Users where userid=$userID";
		
	}else 
	{
		if(isset($_POST['username']) && isset($_POST['password']) )
		{
			$name=mysql_real_escape_string($_POST['username']);
			$pass=mysql_real_escape_string($_POST['password']);
			
			// check these against the db
			$md5pass=md5($pass);
			$clean_name=strtolower($name);
			$sql = "select userid,username,btc,usd,fundsHeld,paypalTrust from Users where CleanName='$clean_name' and password='$md5pass'";	
		}else $result['error'] = "Need to log in.";
	}
	if(isset($sql))
	{
		mysql_query("BEGIN");
		try
		{
			if(!$data=mysql_query($sql)) throw new Exception("SQL Error");
			if(!$row=mysql_fetch_array($data)) throw new Exception("User not found");
			
			$userID=$row['userid'];
			$amount=$_POST['amount']*BASIS;
			$merchID=$_POST['merchID'];
			$currency_code=$_POST['currency_code'];
			$btcHeld=$row['btc'];
			$usdHeld=$row['usd'];
			$minFundsHeld=$row['fundsHeld']*(1-$row['paypalTrust']);
			$time=time();
			$customerName=$row['username'];
			$custom=mysql_real_escape_string($_POST['custom']);
			
			$sql="SELECT username,usd,btc from Users where userID=$merchID";
			if(!$data=mysql_query($sql)) throw new Exception("SQL Error");
			if(!$row=mysql_fetch_array($data)) throw new Exception("Merchant not found");
			$mUsdHeld=$row['usd'];
			$mBtcHeld=$row['btc'];
			$merchName=$row['username'];
			$txn_id=generateRandomString(8);
			
			if($currency_code=="USD")
			{
				if($usdHeld >= $amount )
				{
					$accountTotal=($usdHeld-$amount)+$btcHeld*.06;
					if($accountTotal < $minFundsHeld)
					{
						$allowedUSD=round(($usdHeld -($minFundsHeld-$btcHeld*.06))/BASIS,2);
						throw new Exception("To reduce fraud we hold a certain of portion of PayPal payments in reserve for 30 days. You are currently only able to withdraw $allowedUSD");
					}else
					{
						$sql="UPDATE Users set USD=USD-$amount where userid=$userID";
						if(!mysql_query($sql)) throw new Exception("SQL Error $sql");
						$sql="UPDATE Users set USD=USD+$amount where UserID=$merchID";
						if(!mysql_query($sql)) throw new Exception("SQL Error $sql");
						
						$usdHeld -= $amount;
						$sql="INSERT into Activity (UserID,DeltaUSD,Type,TypeData,BTC,USD,Date) values ($userID,-$amount,7,'$merchName',$btcHeld,$usdHeld,$time)";
						if(!mysql_query($sql)) throw new Exception("SQL Error $sql");
						$mUsdHeld += $amount;
						$sql="INSERT into Activity (UserID,DeltaUSD,Type,TypeData,BTC,USD,Date) values ($merchID,$amount,7,'$customerName',$mBtcHeld,$mUsdHeld,$time)";
						if(!mysql_query($sql)) throw new Exception("SQL Error $sql");
						
						$sql="INSERT INTO MerchantOrders (MerchantID,CustomerID,currency,Amount,AmountRecv,Custom,txn_id,Status,Date) values ($merchID,$userID,1,$amount,$amount,'$custom','$txn_id',1,$time)";
						if(!mysql_query($sql)) throw new Exception("SQL Error $sql");
						
						mysql_query('commit');
						
						checkBidOrders($uid);
						checkAskOrders($merchID);
						$result['result']=1;
					}
				}else throw new Exception("Insufficient Funds.");
				
			}else if($currency_code=="BTC")
			{
				if($btcHeld>=$amount)
				{
					$accountTotal=$usdHeld+($btcHeld-$amount)*.06;
					if($accountTotal < $minFundsHeld)
					{
						$allowedBTC= (int)(($btcHeld -(($minFundsHeld-$usdHeld)/.06))/BASIS);
						throw new Exception("To reduce fraud we hold a certain of portion of PayPal payments in reserve for 30 days. You are currently only able to withdraw $allowedBTC BTC");
					}else
					{
						
						$sql="UPDATE Users set BTC=BTC-$amount where UserID=$userID";
						if(!mysql_query($sql)) throw new Exception("SQL Error $sql");
						$sql="UPDATE Users set BTC=BTC+$amount where UserID=$merchID";
						if(!mysql_query($sql)) throw new Exception("SQL Error $sql");
						
						$btcHeld -= $amount;
						$sql="INSERT into Activity (UserID,DeltaBTC,Type,TypeData,BTC,USD,Date) values ($userID,-$amount,7,'$merchName',$btcHeld,$usdHeld,$time)";
						if(!mysql_query($sql)) throw new Exception("SQL Error $sql");
						$mBtcHeld += $amount;
						$sql="INSERT into Activity (UserID,DeltaBTC,Type,TypeData,BTC,USD,Date) values ($merchID,$amount,7,'$customerName',$mBtcHeld,$mUsdHeld,$time)";
						if(!mysql_query($sql)) throw new Exception("SQL Error $sql");

						$sql="INSERT INTO MerchantOrders (MerchantID,CustomerID,currency,Amount,AmountRecv,Custom,txn_id,Status,Date) values ($merchID,$userID,2,$amount,$amount,'$custom','$txn_id',1,$time)";
						if(!mysql_query($sql)) throw new Exception("SQL Error $sql");
					
						
						mysql_query('commit');
						
						checkAskOrders($uid);
						checkBidOrders($merchID);
						
						$result['result']=1;
					}
					
					 
				}else throw new Exception("Insufficient Funds.");
			}else throw new Exception("Invalid Currency");
			
			if(isset($_POST['notify_url']))
			{
				$notify_url=$_POST['notify_url'];
				notifyMerch($notify_url,$customerName,$custom,$txn_id,$currency_code,$amount);
			}
			
		}catch(Exception $e)
		{
			mysql_query("rollback");
			if($e->getMessage()=="SQL Error") logMsg("SQL Error: $sql");
			$result['error'] = $e->getMessage();
		}	
	} // already set the error
}else $result['error']="Invalid";

echo( json_encode($result));


?>