<?php
include('../../noserve/config.inc');
include('lib/functions.inc');
include('lib/session.php');
include('lib/common.inc');

// make sure:
// user is logged in 
// valid amount
// valid price
// they have enough BTC
	// no: add an offer
// is there a valid buyer?
	// no: add an offer
	// yes: sell all we can 
// BTC left? Sell more



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
		$uid=getSingleDBValue($sql);
	}
}else
{
	$uid=(int)($_SESSION['UserID']);
}

if($uid)
{
	try{
		$amount=BASIS*(float)$_POST['amount'];    
		$price=(float)$_POST['price'];
		$lastPrice=0;
		if($amount>9*BASIS && $price>0)
		{
			
			$result=array();
			$result['status']="";
			
			$btcHeld=getSingleDBValue("SELECT BTC From Users where userid=$uid");
			$btcHeld=round($btcHeld,ROUNDING);
			$time=time();
			if($btcHeld<$amount)
			{
				addOrder('Asks',$uid,$amount-$btcHeld,$price,2,$time);
				$amount=$btcHeld;
				$result['status'] .="<br>You don't have that much BTC. What remains is stored in your open orders.";	
			}
			
			$amount=findBuyer($uid,$amount,$price,$time,true);
			if($amount>0)
			{
				$result['status'] .="<br>Your entire order can't be filled at that price. What remains is stored in your open orders.";
			}
			
			checkAskOrders($uid);
			
			updateTicker($lastPrice);
			
			getOrders($uid);
	
		}else $result['error']="Invalid Amount.";
	}catch(GoxException $e)
	{
		$result['error']= $e->getMessage();
		$e->log();
	}
}else
{ // not found in db
	$result=array( 'error' => "Not logged in." );
}

echo( json_encode($result));

?>