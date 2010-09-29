<?php
include('../../noserve/config.inc');
include('lib/functions.inc');
include('lib/session.php');
include('lib/common.inc');
include('lib/bitcoin.inc');
include('paypal/masspay.php');

function withdrawBTC()
{
	global $result;
	
	if(isset($_POST['btca']) && isset($_POST['amount']))
	{
		$uid=(int)($_SESSION['UserID']);
		
		$amount=BASIS*(float)	$_POST['amount'];
		$btca=mysql_real_escape_string($_POST['btca']);
		
		db_connect();
		
		
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
			$sql="INSERT INTO ErrorLog (ErrorType,Msg,Date) values ('withdraw','$btca $amount $uid $e',$time)";
			mysql_query($sql);
			$result['error'] = "Problem Connecting to bitcoind. Please try again shortly. $e";
		}
	}else $result['error'] = "Must enter in a Bitcoin Address.";
}

function withdrawPaypal()
{
	global $result;
	
	if(isset($_POST['email']) && isset($_POST['amount']))
	{
		$uid=(int)($_SESSION['UserID']);
		
		$amount=BASIS*(float)$_POST['amount'];
		$email=mysql_real_escape_string($_POST['email']);
		
		db_connect();
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

if(isset($_SESSION['UserID']))
{
	$type=$_POST['group1'];
	if($type=="BTC")
	{
		withdrawBTC();		
	}else
	{ // paypal
		$method=$_POST['method'];
		if($method=='paypal') withdrawPaypal();
		else
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
		}
	}

}else
{ // not found in db
	$result=array( 'error' => "Not logged in." );
}


echo( json_encode($result));

?>