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


function findSeller($buyerID,$amount,$buyPrice,$time)
{
	global $result;
	global $lastPrice;
	
	$sellPrice=$buyPrice/SPREAD;
	
	// TODO: this needs to also sort by time in cases of ties
	$sql="SELECT * from Asks where Status=1 and round(Price,6)<=$sellPrice order by Price";
	$data=mysql_query($sql);
	if($data)
	{
		$tradeCount=0;
		
		while($row=mysql_fetch_array($data))
		{
			$askAmount=$row['Amount'];
			$sellerID=$row['UserID'];
			$askID=$row['OrderID'];
			$askPrice=$row['Price'];
			$lastPrice=$askPrice*HALF_SPREAD;
			
			if($sellerID == $buyerID )
			{
				$result['status'] .= "Trying to sell from yourself?";
				return($amount);
			}
			
			if($askAmount>$amount)
			{ // this ask covers the buy
				$sql="UPDATE Asks set Amount=Amount-$amount where OrderID=$askID";
				mysql_query($sql);
				$tradeAmount=$amount;
			}else
			{ // this buy covers the ask
				$sql="DELETE FROM Asks where OrderID=$askID";
				mysql_query($sql);
				$tradeAmount=$askAmount;				
			}
			$pTradeAmount=round($tradeAmount/BASIS,2);
			recordTrade($buyerID,$sellerID,$tradeAmount,$lastPrice,$time);
			tradeNotifyUser($sellerID,"Sold",$pTradeAmount,$lastPrice/HALF_SPREAD);
			checkBidOrders($sellerID);
			$result['trades'][$tradeCount]="Bought $pTradeAmount BTC for $lastPrice.";
			$tradeCount++;
			$amount=$amount-$askAmount;
			if($amount<=0) return(0);
		}

	}else $result['error']="SQL Error.";
	
	return($amount);	
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
			addOrder('Bids',$uid,$heldAmount,$price,2,$time);
			$amount=$usdHeld/$price;	
			$result['status'] .= "<br>You don't have that much USD. What remains is stored in your open orders.";
		}
		
		$amount=findSeller($uid,$amount,$price,$time);
		if($amount>0) 
		{
			addOrder('Bids',$uid,$amount,$price,0,$time);
			$result['status'] .="<br>Your entire order can't be filled at that price. What remains is stored in your open orders.";
		}
		
		checkBidOrders($uid);
		
		updateTicker($lastPrice);
		
		getOrders($uid);

	}else $result=array( 'error' => "Invalid Amount." );
}else
{ // not found in db
	$result=array( 'error' => "Not logged in." );
}

echo( json_encode($result));

?>