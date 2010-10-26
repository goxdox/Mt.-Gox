<?php 


function findSeller($buyerID,$amount,$buyPrice,$time,$showResults)
{
	if($amount>0)
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
					if($showResults) $result['status'] .= "Trying to sell from yourself?";
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
				if(!$showResults) tradeNotifyUser($buyerID,"Bought",$pTradeAmount,$lastPrice);
				
				checkBidOrders($sellerID);
				if($showResults) $result['trades'][$tradeCount]="Bought $pTradeAmount BTC for $lastPrice.";
				$tradeCount++;
				$amount=$amount-$askAmount;
				if($amount<=0) return(0);
			}
	
		}else $result['error']="SQL Error.";
		
		addOrder('Bids',$buyerID,$amount,$buyPrice,0,$time);
	}
	return($amount);	
}


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
			else addOrder('Asks',$uid,$amount,$sellPrice,0,$time);
		}

	}else $result['error']="SQL Error.";
	
	return($amount);	
}

?>