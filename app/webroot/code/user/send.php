<?php
include('../../../noserve/config.inc');
include('../lib/functions.inc');
include('../lib/common.inc');
include('../lib/session.php');

// make sure:
// user is logged in 
// valid amount
// user has enough Money
// Search for user 
// create an account if they don't exist
// determine who they are sending to.

function findUser($email)
{
	$cleanMail=strtolower($email);
	$sql="Select UserID from EmailMap where email='$cleanMail' and Status>0";
	return( getSingleDBValue($sql) );
}

function checkFunds($currency,$amount,$merchUSD,$merchBTC,$minFundsHeld)
{
	if($currency==1)
	{
		if($merchUSD >= $amount )
		{
			$accountTotal=($merchUSD-$amount)+$merchBTC*.06;
			if($accountTotal < $minFundsHeld)
			{
				throw new Exception("Held");
			}
		}else throw new Exception("Not Enough USD.");
		
	}else if($currency==2)
	{
		if($merchBTC>=$amount)
		{
			$accountTotal=$merchUSD+($merchBTC-$amount)*.06;
			if($accountTotal < $minFundsHeld)
			{
				throw new Exception("Held");
			}
		}else throw new Exception("Not Enough BTC.");
	}else throw new Exception("Unknown Currency");
}

function notifyUser($notify,$email,$note,$merchName,$currency,$pAmount)
{	
	if($notify)
	{
		if($currency==1)
		{
			$msg= "$merchName just sent you ".'$'."$pAmount!\r\n";
		}else 
		{
			$msg= "$merchName just sent you $pAmount BTC!\r\n";
		}
		$msg .= "----- Note from Sender -----\r\n";
		$msg .= "$note\r\n";
		$msg .= "----------\r\n";
		$msg .= "You can opt not to receive email notifications when someone sends you money in your mtgox account settings.";
		
		$headers = "From: support@mtgox.com\r\n";
		$headers .= "Reply-To: <support@mtgox.com>\r\n";
		$headers .= "Return-Path: <support@mtgox.com>\r\n";
					
		mail($email,"Payment received",$msg,$headers);
	}
}

function notifyNew($email,$note,$merchName,$currency,$pAmount,$token)
{	
	if($currency==1)
	{
		$msg= "$merchName just sent you ".'$'."$pAmount!\r\n";
	}else 
	{
		$msg= "$merchName just sent you $pAmount BTC!\r\n";
		$msg= "More info about bitcoins: http://www.bitcoin.org\r\n";
	}
	$msg .= "You can claim your funds by clicking this link: https://mtgox.com/claim?token=$token&email=$email\r\n";
	$msg .= "----- Note from Sender -----\r\n";
	$msg .= "$note\r\n";
	$msg .= "----------\r\n";
	
	$headers = "From: support@mtgox.com\r\n";
	$headers .= "Reply-To: <support@mtgox.com>\r\n";
	$headers .= "Return-Path: <support@mtgox.com>\r\n";
				
	mail($email,"Someone sent you Money",$msg,$headers);
	
}


/////////////////////////////////////////////////////////////////
if(isset($_SESSION['UserID']))
{
	$merchID=(int)($_SESSION['UserID']);
	
	if( (isset($_POST['currency'])) &&
		(isset($_POST['email'])) &&
		(isset($_POST['amount']) && $_POST['amount']>0) )
	{
		db_connect();
		$time=time();
		
		$currency=$_POST['currency'];
		if($currency=="USD") $currency=1; else $currency=2;
		
		$note=mysql_real_escape_string($_POST['note']);
		$email=mysql_real_escape_string($_POST['email']);
		$amount=$_POST['amount']*BASIS;
		$pAmount=round($amount/BASIS,2);
		
		$sql="SELECT username,btc,usd,fundsHeld,paypalTrust from Users where userid=$merchID";
		mysql_query("BEGIN");
		try
		{
			if(!$data=mysql_query($sql)) throw new Exception("SQL Error");
			if(!$row=mysql_fetch_array($data)) throw new Exception("invalid");
			
			$merchName=$row['username'];
			$merchBTC=$row['btc'];
			$merchUSD=$row['usd'];
			$minFundsHeld=$row['fundsHeld']*(1-$row['paypalTrust']);
			
			checkFunds($currency,$amount,$merchUSD,$merchBTC,$minFundsHeld);
			
			$userID=findUser($email);
			if($userID)
			{
				$sql="SELECT usd,btc,sendNotify from Users where userID=$userID";
				
				if(!$data=mysql_query($sql)) throw new Exception("SQL Error");
				if(!$row=mysql_fetch_array($data)) throw new Exception("User not Found");
				$userBTC=$row['btc'];
				$userUSD=$row['usd'];
				$notify=$row['sendNotify'];
			}else 
			{
				$token=generateRandomString(10);
				$sql="INSERT INTO SendMoney (FromID,Currency,Amount,ToEmail,Token,Note,Status,Date) values ($merchID,$currency,$amount,'$email','$token','$note',1,$time)";
				if(!mysql_query($sql)) throw new Exception("SQL Error"); 
				mysql_query('commit');
			}
				
				
			if($currency==1)
			{
				if($userID)
				{
					$sql="UPDATE Users set USD=USD+$amount where UserID=$userID";
					if(!mysql_query($sql)) throw new Exception("SQL Error");
					
					$userUSD += $amount;
					$sql="INSERT into Activity (UserID,DeltaUSD,Type,TypeData,BTC,USD,Date) values ($userID,$amount,10,'$merchName',$userBTC,$userUSD,$time)";
					if(!mysql_query($sql)) throw new Exception("SQL Error");
				}
				
				$sql="UPDATE Users set USD=USD-$amount where userid=$merchID";
				if(!mysql_query($sql)) throw new Exception("SQL Error");
					
				$merchUSD -= $amount;
				$sql="INSERT into Activity (UserID,DeltaUSD,Type,TypeData,BTC,USD,Date) values ($merchID,-$amount,10,'$email',$merchBTC,$merchUSD,$time)";
				if(!mysql_query($sql)) throw new Exception("SQL Error");
					
					
				mysql_query('commit');
				
				checkBidOrders($userID);
				checkBidOrders($merchID);
				
				
				$result['status']='Sent $'."$pAmount to $email.";
					
				
			}else 
			{	
				if($userID)
				{
					$sql="UPDATE Users set BTC=BTC+$amount where UserID=$userID";
					if(!mysql_query($sql)) throw new Exception("SQL Error");
					$userBTC += $amount;
					$sql="INSERT into Activity (UserID,DeltaBTC,Type,TypeData,BTC,USD,Date) values ($userID,$amount,10,'$merchName',$userBTC,$userUSD,$time)";
					if(!mysql_query($sql)) throw new Exception("SQL Error");
				}	
				
				$sql="UPDATE Users set BTC=BTC-$amount where UserID=$merchID";
				if(!mysql_query($sql)) throw new Exception("SQL Error");
				
				$merchBTC -= $amount;
				$sql="INSERT into Activity (UserID,DeltaBTC,Type,TypeData,BTC,USD,Date) values ($merchID,-$amount,10,'$email',$merchBTC,$merchUSD,$time)";
				if(!mysql_query($sql)) throw new Exception("SQL Error");
			
				
				mysql_query('commit');
				
				checkAskOrders($userID);
				checkAskOrders($merchID);
				
				$result['status']="Sent $pAmount BTC to $email.";
			}
				
			if($userID) notifyUser($notify,$email,$note,$merchName,$currency,$pAmount);
			else notifyNew($email,$note,$merchName,$currency,$pAmount,$token);
				
		}catch(Exception $e)
		{
			mysql_query("rollback");
			logMsg("users/send: $sql");
			$result['error'] = $e->getMessage();
		}
	
		
	}else $result['error']="Invalid";
	
}else $result['error']="Not logged in.";

echo( json_encode($result));
?>