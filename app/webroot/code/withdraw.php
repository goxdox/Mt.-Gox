<?php
include('../../noserve/config.inc');
include('lib/functions.inc');
include('lib/session.php');
include('lib/common.inc');
include('lib/bitcoin.inc');
include('paypal/masspay.php');
include('lr/functions.php');

// only allow them to withdraw $1000 a day
function amountLeftToday($userID,$inBTC)
{
	$sql="SELECT LastPrice from Ticker";
	$lastPrice=getSingleDBValue($sql);
	
	$dayago=time()-(24*60*60);
	$sql="SELECT sum(deltaBTC),sum(deltaUSD) from Activity where userid=$userID and (type=4 or type=5 or type=7 or type=10) and deltaUSD<1 and deltaBTC<1 and date>$dayago";
	//logMsg($sql);
	if($data=mysql_query($sql))
	{
		if($row=mysql_fetch_array($data))
		{
			
			$btc=$row[0];
			if($btc=='NULL') $btc=0;
			else $btc=-$btc;
			$usd=$row[1];
			if($usd=='NULL') $usd=0;
			else $usd=-$usd;
			//logMsg("$btc $usd");
			
			$usd=($btc*$lastPrice)+$usd;
			$left=1000000-$usd;
			if($inBTC) return($left/$lastPrice);
			return($left);
		}else return(1000000);
	}	
	return(0);
}

function withdrawBTC($userID)
{
	global $result;
	
	if(isset($_POST['btca']) && isset($_POST['amount']))
	{
		$uid=$userID;
		
		$amount=BASIS*(float)$_POST['amount'];
		$btca=mysql_real_escape_string($_POST['btca']);
		
		$sql="SELECT SUM(deltaBTC) from Activity where userid=$userID and (type=3 or type=4 or type=10 or type=7)";
		$addedBTC=getSingleDBValue($sql);
		if($addedBTC>=$amount) $left=$amount;
		else $left=amountLeftToday($uid,true);
		
		if($left>0) 
		{
			
			if($amount>$left)
			{
				$amount=$left;
				$pAmount=round($amount/BASIS,2);
				$result['error'] = "To comply with US regulations you are only allowed to withdraw a maximum of $1000 within a 24 hour period. $pAmount withdrawn. Please try the rest of your withdrawal tomorrow.";
			}
			
			
			try{
				$sql="SELECT USD,BTC,fundsHeld,paypalTrust from Users where userID=$uid";
				if($data=mysql_query($sql))
				{
					if($row=mysql_fetch_array($data))
					{
						$usdHeld=$row['USD'];
						$minFundsHeld=$row['fundsHeld']*(1-$row['paypalTrust']);
						$btcHeld=$row['BTC'];
						if($btcHeld<$amount)
						{
							$result['error'] = "You don't have this much BTC.";
						}else
						{
							$accountTotal=$usdHeld+($btcHeld-$amount)*.06;
							if($accountTotal < $minFundsHeld)
							{
								$allowedBTC= (int)(($btcHeld -(($minFundsHeld-$usdHeld)/.06))/BASIS);
								if($allowedBTC<0) $allowedBTC=0;
								$result['status'] = "To reduce fraud we hold a certain of portion of PayPal payments in reserve for 30 days. You are currently only able to withdraw $allowedBTC BTC";
							}else
							{
								BC_sendFunds($uid,$amount,$btca,$usdHeld,$btcHeld);
								
								$result['status'] = "Your funds are on their way...";
							}
						}
					}else $result['error'] = "User not found.";
				}else $result['error'] = 'SQL Error';
			}catch(Exception $e)
			{
				$time=time();
				$sql="INSERT INTO ErrorLog (ErrorType,Msg,Date) values ('withdraw','$btca $amount $uid',$time)";
				mysql_query($sql);
				//logMsg($e);
				$result['error'] = "Invalid Bitcoin Address: '$btca'";
			}
		}else $result['error'] = "To comply with US regulations you are only allowed to withdraw a maximum of $1000 within a 24 hour period. Please try your withdraw tomorrow.";
	}else $result['error'] = "Must enter in a Bitcoin Address.";
}

function withdrawLR($userID)
{
	global $result;
	
	if(isset($_POST['account']) && isset($_POST['amount']))
	{
		$account=$_POST['account'];
		$amount=BASIS*(float)$_POST['amount'];
		$email=mysql_real_escape_string($_POST['account']);
	
		$left=amountLeftToday($userID,false);
		
		if($left>0) 
		{
			if($amount>$left)
			{
				$amount=$left;
				$pAmount=round($amount/BASIS,2);
				$result['error'] = "To comply with US regulations you are only allowed to withdraw a maximum of $1000 within a 24 hour period. $pAmount withdrawn. Please try the rest of your withdrawal tomorrow.";
			}
			mysql_query("BEGIN");
			try
			{
				$sql="SELECT USD,BTC,FundsHeld,paypalTrust from Users where userID=$userID FOR UPDATE";
				if(!($data=mysql_query($sql))) throw new GoxException("SQL Error",$sql);
				if(!($row=mysql_fetch_array($data))) throw new GoxException("User not found.");

				if($btcHeld<-100) throw new GoxException("Please email support.");
				$usdHeld=$row['USD'];
				$minFundsHeld=$row['FundsHeld']*(1-$row['paypalTrust']);
				$btcHeld=$row['BTC'];
				if($usdHeld<$amount)
				{
					throw new GoxException("You don't have this much USD.");
				}else
				{
					$accountTotal=($usdHeld-$amount)+$btcHeld*.4;
					if($accountTotal < $minFundsHeld)
					{
						$allowedUSD=round(($usdHeld -($minFundsHeld-$btcHeld*.4))/BASIS,2);
						throw new GoxException("To reduce fraud we hold a certain of portion of PayPal payments in reserve for 30 days. You are currently only able to withdraw $allowedUSD");
					}else
					{
						// A=F+P  , F=P*.02 or F=1 , A=P(.02+1)
						$pAmount=((float)$amount)/((float)BASIS);
						$payment=$pAmount/1.01;
						$fee=$pAmount-$payment;
						$payment=round($payment,2);
						
						$sql="UPDATE Users set USD=USD-$amount where userid=$userID";
						if(!mysql_query($sql)) throw new GoxException("SQL Error",$sql);
						$usdHeld -= $amount;
						$time=time();
						$sql="INSERT into Activity (UserID,DeltaUSD,Type,TypeData,BTC,USD,Date) values ($userID,-$amount,5,'$email',$btcHeld,$usdHeld,$time)";
						if(!mysql_query($sql)) throw new GoxException("SQL Error",$sql);
						$LRRet=LRWithdraw($account,$payment);
						if($LRRet==1) throw new GoxException("Invalid Liberty Reserve Address: '$account'");
						if($LRRet==2) throw new GoxException("Withdraw via Liberty Reserve is currently offline. Please try again tomorrow. Sorry for the inconvenience.");
						if($LRRet==3) throw new GoxException("Problem Withdrawing. Please email: support@mtgox.com");
						
						mysql_query('commit');
						$result['status'] = "Your funds are on their way...";
						
						checkBidOrders($userID);
					}
				}
			}catch(GoxException $e)
			{
				mysql_query("rollback");
				$result['error'] = $e->getMessage();
				$e->log();
			}
		}else $result['error'] = "To comply with US regulations you are only allowed to withdraw a maximum of $1000 within a 24 hour period. Please try your withdraw tomorrow.";
	}else $result['error'] = "Must enter in a Liberty Reserve Account.";
}

function withdrawPaypal()
{
	global $result;
	
	if(isset($_POST['email']) && isset($_POST['amount']))
	{
		$uid=(int)($_SESSION['UserID']);
		
		$amount=BASIS*(float)$_POST['amount'];
		$email=mysql_real_escape_string($_POST['email']);
		
		mysql_query("BEGIN");
		try
		{
			$sql="SELECT USD,BTC,FundsHeld,paypalTrust from Users where userID=$uid FOR UPDATE";
			if(!($data=mysql_query($sql))) throw new Exception("SQL Error");
			if(!($row=mysql_fetch_array($data))) throw new Exception("User not found.");
				
			$usdHeld=$row['USD'];
			$minFundsHeld=$row['FundsHeld']*(1-$row['paypalTrust']);
			$btcHeld=$row['BTC'];
			if($usdHeld<$amount)
			{
				throw new Exception("You don't have this much USD.");
			}else
			{
				$accountTotal=($usdHeld-$amount)+$btcHeld*.06;
				if($accountTotal < $minFundsHeld)
				{
					$allowedUSD=round(($usdHeld -($minFundsHeld-$btcHeld*.06))/BASIS,2);
					throw new Exception("To reduce fraud we hold a certain of portion of PayPal payments in reserve for 30 days. You are currently only able to withdraw $allowedUSD");
				}else
				{
					// A=F+P  , F=P*.02 or F=1 , A=P(.02+1)
					$pAmount=((float)$amount)/((float)BASIS);
					$payment=$pAmount/1.02;
					$fee=$pAmount-$payment;
					if($fee>1) $payment=$pAmount-1;
					$payment=round($payment,2);
					
					$sql="UPDATE Users set USD=USD-$amount where userid=$uid";
					if(!mysql_query($sql)) throw new Exception("SQL Error");
					$usdHeld -= $amount;
					$time=time();
					$sql="INSERT into Activity (UserID,DeltaUSD,Type,TypeData,BTC,USD,Date) values ($uid,-$amount,5,'$email',$btcHeld,$usdHeld,$time)";
					if(!mysql_query($sql)) throw new Exception("SQL Error");
					paypalWithdraw($email,$payment);
					mysql_query('commit');
					$result['status'] = "Your funds are on their way...";
					checkBidOrders($uid);
				}
			}
		}catch(Exception $e)
		{
			mysql_query("rollback");
			$result['error'] = $e->getMessage();
		}
	}else $result['error'] = "Must enter in a Bitcoin Address.";
}

db_connect();

if(!isset($_SESSION['UserID']))
{
	if(isset($_POST['name']) && isset($_POST['pass']))
	{
		$name=mysql_real_escape_string($_POST['name']);
		$pass=mysql_real_escape_string($_POST['pass']);
		
		// check these against the db
		$md5pass=md5($pass);
		$clean_name=strtolower($name);
		$sql = "select userid from Users where CleanName='$clean_name' and password='$md5pass'";
		$userID=getSingleDBValue($sql);
	}else $userID=0;
}else
{
	$userID=(int)($_SESSION['UserID']);
}

if($userID)
{
	$type=$_POST['group1'];
	if($type=="BTC")
	{
		withdrawBTC($userID);		
	}else
	{ // paypal
		withdrawLR($userID);
		
		/*
		//$method=$_POST['method'];
		//if($method=='paypal') withdrawPaypal();
		//else
		{
			$ip=$_SERVER['REMOTE_ADDR'];
			//$username=$_SESSION['Username'];
			
			$post=print_r($_POST,true);
			$session=print_r($_SESSION,true);
			//$session =implode(",", $_SESSION);
			//$post = implode(",", $_POST);
			$time=time();
			
			$msg= "Withdrawl by:\r\n";
			$msg .= "$session\r\n";
			$msg .= $post;
			$msg .= "\r\nIP: $ip Time: $time";
			
			$headers = "From: support@mtgox.com\r\n";
			$headers .= "Reply-To: <support@mtgox.com>\r\n";
			$headers .= "Return-Path: <support@mtgox.com>\r\n";
						
			mail("jed@thefarwilds.com","Withdrawl",$msg,$headers);
			
			$result['status'] = "Your withdraw request will be processed shortly.";
		} */
	}

}else
{ // not found in db
	$result['error'] = "Not logged in.";
}


echo( json_encode($result));

?>