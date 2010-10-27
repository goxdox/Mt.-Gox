<?php
include('../../../noserve/config.inc');
include('../lib/functions.inc');
include('../lib/session.php');
include('../lib/common.inc');
include('../lr/functions.php');


function withdrawLR($userID)
{
	global $result;
	
	if(isset($_POST['account']) && isset($_POST['amount']))
	{
		$account=$_POST['account'];
		$amount=BASIS*(float)$_POST['amount'];
		$email=mysql_real_escape_string($_POST['account']);
		
		db_connect();
		mysql_query("BEGIN");
		try
		{
			$sql="SELECT USD,BTC,FundsHeld,paypalTrust from Users where userID=$userID FOR UPDATE";
			if(!($data=mysql_query($sql))) throw new GoxException("SQL Error",$sql);
			if(!($row=mysql_fetch_array($data))) throw new GoxException("User not found.");
				
			$usdHeld=$row['USD'];
			$minFundsHeld=$row['FundsHeld']*(1-$row['paypalTrust']);
			$btcHeld=$row['BTC'];
			if($usdHeld<$amount)
			{
				throw new GoxException("You don't have this much USD.");
			}else
			{
				$accountTotal=($usdHeld-$amount)+$btcHeld*.06;
				if($accountTotal < $minFundsHeld)
				{
					$allowedUSD=round(($usdHeld -($minFundsHeld-$btcHeld*.06))/BASIS,2);
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
					if(!LRWithdraw($account,$payment))  throw new GoxException("Problem Withdrawing. Please email: support@mtgox.com");
					
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
	}else $result['error'] = "Must enter in a Liberty Reserve Account.";
}

if(isset($_SESSION['UserID']))
{
	$adminID=(int)($_SESSION['UserID']);
	if($adminID==1)
	{
		withdrawLR($_POST['userid']);
	}

}else
{ // not found in db
	$result['error'] = "Not logged in.";
}


echo( json_encode($result));

?>