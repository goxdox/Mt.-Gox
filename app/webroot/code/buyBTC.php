<?php
include('../../noserve/config.inc');
include('lib/functions.inc');
include('lib/session.php');
include('lib/common.inc');
include('lib/trade.php');

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
	
	$amount=BASIS*(float)$_POST['amount'];    
	$price=(float)$_POST['price'];
	
	if($amount>9*BASIS && $price>0)
	{
		$lastPrice=0;
		
		$result=array();
		$result['status']="";
		
		$usdNeeded=$amount*$price;
		
		$usdHeld=getSingleDBValue("SELECT USD From Users where userid=$uid");
		$time=time();
		if($usdHeld<$usdNeeded)
		{
			$heldAmount=($usdNeeded-$usdHeld)/$price;
			addOrder('Bids',$uid,$heldAmount,$price,2);
			$amount=$usdHeld/$price;	
			$result['status'] .= "<br>You don't have that much USD. What remains is stored in your open orders.";
		}
		
		if($price>=.37) 
		{
			$sql="UPDATE Options set value=0 where name=1";
			mysql_query($sql);
		}
		
		$amount=findSeller($uid,$amount,$price,$time,true);
		if($amount>0) 
		{
			$result['status'] .="<br>Your entire order can't be filled at that price. What remains is stored in your open orders.";
		}
		
		checkBidOrders($uid);
		
		updateTicker($lastPrice);
		
		getOrders($uid);

	}else $result=array( 'error' => "Invalid Amount." );
}else
{ // not found in db
	$result['error'] = "Not logged in. <a href='/users/login'>Log in</a>";
}

echo( json_encode($result));

?>