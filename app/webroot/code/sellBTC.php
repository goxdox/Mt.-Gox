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

function findBuyer($uid,$amount,$sellPrice,$time)
{
	global $result;
	global $lastPrice;
	
	$buyPrice=$sellPrice*SPREAD;
	
	// TODO: this needs to also sort by time in cases of ties
	$sql="SELECT * from Bids where Status=1 and round(Price,6)>=$buyPrice order by Price desc";
	$data=mysql_query($sql);
	if($data)
	{
		$tradeCount=0;
		
		while($row=mysql_fetch_array($data))
		{
			$bidAmount=$row['Amount'];
			$buyerID=$row['UserID'];
			$bidID=$row['OrderID'];
			$bidPrice=$row['Price'];
			
			if($buyerID == $uid )
			{
				$result['status']="<br>Trying to buy from yourself?";
				return($amount);
			}
			
			$lastPrice=$bidPrice/HALF_SPREAD;
			
			
			if($bidAmount>$amount)
			{ // this bid covers the sell
				$sql="UPDATE Bids set Amount=Amount-$amount where OrderID=$bidID";
				mysql_query($sql);
				$tradeAmount=$amount;
			}else
			{ // this sale covers the bid
				$sql="DELETE FROM Bids where OrderID=$bidID";
				mysql_query($sql);
				$tradeAmount=$bidAmount;
			}
			
			recordTrade($buyerID,$uid,$tradeAmount,$lastPrice,$time);
			$pTradeAmount=round($tradeAmount/BASIS,2);
			tradeNotifyUser($buyerID,"Bought",$pTradeAmount,$lastPrice*HALF_SPREAD);
						
			$result['trades'][$tradeCount]="Sold $pTradeAmount BTC for $lastPrice.";
			$tradeCount++;
			
			$amount=$amount-$bidAmount;
			checkAskOrders($buyerID);
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
		
		$amount=findBuyer($uid,$amount,$price,$time);
		if($amount>0)
		{
			addOrder('Asks',$uid,$amount,$price,0,$time);
			$result['status'] .="<br>Your entire order can't be filled at that price. What remains is stored in your open orders.";
		}
		
		checkAskOrders($uid);
		
		updateTicker($lastPrice);
		
		getOrders($uid);

	}else $result=array( 'error' => "Invalid Amount." );
}else
{ // not found in db
	$result=array( 'error' => "Not logged in." );
}

echo( json_encode($result));

?>