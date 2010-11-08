<?php 
define('SPREAD',1);
define('HALF_SPREAD',1);
define('ROUNDING',4);
define('BASIS',1000);
define('GOX_FEE',.0065);

class GoxException extends Exception
{
	public $mSql=0;
	
	public function __construct($msg,$sql=0)
	{
		parent::__construct($msg);
		$this->mSql=$sql;
	}
	
  	public function log()
    {
    	if($this->mSql) logMsg($this->getFile().'('.$this->getLine().') '.$this->mSql);
    }
}

function recordTrade($buyerID,$sellerID,$amount,$price,$time)
{	
	if($amount>0)
	{
		$sql="BEGIN";
		mysql_query($sql);
		
		$sql="INSERT INTO Trades (BuyerID,SellerID,Amount,Price,Date) values ($buyerID,$sellerID,$amount,$price,$time)";
		if(mysql_query($sql))
		{
			$sql="SELECT LAST_INSERT_ID()";
			$tradeID=getSingleDBValue($sql);
			if($tradeID)
			{
				$usdAmount=(int)($price*$amount*HALF_SPREAD);
				$btcFee=(int)($amount*GOX_FEE);
				$usdFee=(int)($usdAmount*GOX_FEE);
				
				$feedBTCAmount=$amount-$btcFee;
				$feedUSDAmount = $usdAmount -$usdFee;
				
				$sql="UPDATE Users set BTC=BTC+$feedBTCAmount, USD=USD-$usdAmount where userid=$buyerID";
				if(mysql_query($sql))
				{
					$sql="UPDATE Users set BTC=BTC-$amount, USD=USD+$feedUSDAmount where userid=$sellerID";
					if(mysql_query($sql))
					{
						$sql="SELECT USD,BTC from Users where UserID=$buyerID";
						if($data=mysql_query($sql))
						{
							$row=mysql_fetch_array($data);
							if($row)
							{
								$usd=$row[0];
								$btc=$row[1];
								$sql="INSERT INTO Activity (UserID,DeltaBTC,DeltaUSD,Type,TypeID,BTC,USD,Date) values ($buyerID,$feedBTCAmount,-$usdAmount,2,$tradeID,$btc,$usd,$time)";
								if(mysql_query($sql))
								{
									$sql="SELECT USD,BTC from Users where UserID=$sellerID";
									if($data=mysql_query($sql))
									{
										$row=mysql_fetch_array($data);
										if($row)
										{
											$usd=$row[0];
											$btc=$row[1];
											$sql="INSERT INTO Activity (UserID,DeltaBTC,DeltaUSD,Type,TypeID,BTC,USD,Date) values ($sellerID,-$amount,$feedUSDAmount,1,$tradeID,$btc,$usd,$time)";
											if(mysql_query($sql))
											{
												$sql="Commit";
												mysql_query($sql);
												
												global $GOX_BOT_ID;
												
												$sql="UPDATE Users set BTC=BTC+$btcFee, USD=USD+$usdFee where userid=$GOX_BOT_ID";
												logMsg($sql);
												mysql_query($sql);
												return;		
											}					
										}
									}
								}
							}
						}
					}
				}
			}
		}
		$sql="rollback";
		mysql_query($sql);
	}
}

function tradeNotifyUser($uid,$type,$amount,$price)
{
	global $result;
	
	$sql="SELECT Email,TradeNotify from Users where UserID=$uid";
	if($data= mysql_query($sql))
	{
		$row=mysql_fetch_array($data);
		if($row)
		{
			$email=$row[0];
			$notify=$row[1];
			if($notify)
			{
				$pAmount=round($amount,2);
				$pPrice=round($price,ROUNDING);
				$msg= "You $type $pAmount BTC for $pPrice!\r\n";
				$msg .= "----------\r\n";
				$msg .= "You are recieving this email because you opted in in your mtgox.com account\r\n";
				$msg .= "You can unsubscribe in your account settings or by clicking this link: http://www.mtgox.com/code/unsubscribe.php?email=$email";
				
				$headers = "From: support@mtgox.com\r\n";
				$headers .= "Reply-To: <support@mtgox.com>\r\n";
				$headers .= "Return-Path: <support@mtgox.com>\r\n";
							
				mail($email,"Trade Notification",$msg,$headers);
				//$result['debug']="$msg";
				
			}
		}	
	}
}

function addOrder($table,$uid,$amount,$price,$status)
{
	global $result;
	
	if($amount>0)
	{
		$time=time();
		if($status)
			$sql="SELECT OrderID from $table where userid=$uid and round(price,5)=$price and status=$status";
		else 
		{
			$sql="SELECT OrderID from $table where userid=$uid and round(price,5)=$price";
			$status=1;
		}
		$oldID=getSingleDBValue($sql);
		if($oldID)
		{
			$sql="UPDATE $table set Amount=Amount+$amount,Date=$time,Status=$status where OrderID=$oldID";
		}else
		{
			$sql="INSERT INTO $table (UserID,Amount,Price,Status,Date) values ($uid,$amount,$price,$status,$time)";
		}
		if(!mysql_query($sql)) throw new GoxException("SQL Error.",$sql);
	}
}

function updateTicker($lastPrice)
{
	global $result;
	$pLastPrice=$lastPrice*BASIS;
	
	$sell=getSingleDBValue("SELECT min(Price) from Asks where status=1")*SPREAD;
	$buy=getSingleDBValue("SELECT max(Price) from Bids where status=1")/SPREAD;
	$pSell=round( $sell , ROUNDING);
	$pBuy=round( $buy, ROUNDING);
	if($buy>$sell)
	{
		// TODO: there is a trade we should make
	}
	$startDate=time()-(60*60*24); // last 24 hours
	$sql="SELECT MAX(Price), MIN(Price), SUM(Amount) from Trades where Date>$startDate";
	$data=mysql_query($sql);
	if($data)
	{
		$row=mysql_fetch_array($data);
		if($row)
		{
			$high=$row[0];
			$low=$row[1];
			$vol=$row[2];
			
			if($lastPrice)
			{
				$lastPrice=round($lastPrice,ROUNDING);
				$sql="UPDATE Ticker set high=$high, low=$low, volume=$vol, highbuy=$buy, lowsell=$sell, lastprice=$lastPrice";
			}else $sql="UPDATE Ticker set high=$high, low=$low, volume=$vol, highbuy=$buy, lowsell=$sell";
			if(!mysql_query($sql)) $result['debug'] = $sql;
			
			$pHigh=round( $high,ROUNDING);
			$pLow=round( $low,ROUNDING);
			$pVol=round($vol/BASIS,0);
			$result['ticker']= array( 'high' => $pHigh, 'low' => $pLow, 'vol' => $pVol, 'buy' => $pBuy , 'sell' => $pSell );
			
			if($lastPrice)
			{				
				$result['ticker']['last']=$pLastPrice;
			}
		}else 
		{
			$result['error'] = "SQL Failed.";
			$result['debug'] = $sql;
		}
		
	}else
	{
		$result['error'] = "SQL Failed.";
		$result['debug'] = $sql;
	}
}


function getTypeOrders($table,$type,$uid,$count)
{
	global $result;
	
	
	$sql="SELECT * FROM $table where UserID=$uid";
	$data=mysql_query($sql);
	
	if($data)
	{
		while($row=mysql_fetch_array($data))
		{
			$id=$row['OrderID'];
			$amount=round($row['Amount']/BASIS,2);
			$price=round($row['Price'],ROUNDING);
			$status=$row['Status'];
			$date=$row['Date'];
				
			//ID, Name, Desc, # members, $ in treasury, Time Till Vote, Time Till Execute, if you are a memeber, group options?
			$offer=array('oid' => $id, 'type' => $type, 'amount' => $amount, 'price' => $price, 'status' => $status, 'date' => $date );
			$result['orders'][$count]=$offer;
	
			$count++;
		}
	}else $result=array( 'error' => "SQL Failed." );
	return($count);
}

function getOrders($uid)
{
	global $result;
	$count=0; 
	$result['orders']= array();
	
	
	$count=getTypeOrders('Bids',2,$uid,$count);
	getTypeOrders('Asks',1,$uid,$count);
	
	getFunds($uid);
}

// make sure the status of all orders is still correct
// see if any of the newly changed offers can now transact
function checkAskOrders($uid)
{
	if($uid)
	{
		global $result;
		
		getFunds($uid);
		$btc=(int)($result['btcs']*BASIS);
		$time=time();
		
		$sql="SELECT OrderID,Amount,Price,Status,Date from Asks where UserID=$uid order by Price";
		$data=mysql_query($sql);
		if($data)
		{
			while($row=mysql_fetch_array($data))
			{
				$orderID=$row[0];
				$amount=$row[1];
				$price=$row[2];
				$status=$row[3];
				$date=$row[4];
				
				if($btc>0)
				{
					if($amount>$btc)
					{
						if($status==1)
						{	
							$sql="Update Asks set Status=1,Amount=$btc where OrderID=$orderID";
							mysql_query($sql);
						}else 
						{
							$sql="DELETE from Asks where OrderID=$orderID";
							mysql_query($sql);
							
							findBuyer($uid,$btc,$price,$time,false);
						}
						
						
						$amountLeft=$amount-$btc;
						
						addOrder('Asks',$uid,$amountLeft,$price,2);
						
						$btc=0;
					}else
					{
						$btc -= $amount;
						if($status==2)
						{
							$sql="DELETE from Asks where OrderID=$orderID";
							mysql_query($sql);
							
							findBuyer($uid,$amount,$price,$time,false);
						}
					}
				}else 
				{
					$sql="Update Asks set Status=2 where OrderID=$orderID";
					mysql_query($sql);
				}
			}
		}else $result['error']="SQL Failed.";
	}
}

function checkBidOrders($uid)
{
	if($uid)
	{
		global $result;
		
		getFunds($uid);
		$usd=(int)($result['usds']*BASIS);
		$time=time();
		
		
		$sql="SELECT OrderID,Amount,Price,Status,Date from Bids where UserID=$uid order by Price desc";
		$data=mysql_query($sql);
		if($data)
		{
			while($row=mysql_fetch_array($data))
			{
				$orderID=$row[0];
				$amount=$row[1];
				$price=$row[2];
				$status=$row[3];
				$date=$row[4];
				
				if($usd>0)
				{
					if($amount*$price>$usd)
					{
						$btc=$usd/$price;
						
						if($status==1)
						{	
							$sql="Update Bids set Status=1,Amount=$btc where OrderID=$orderID";
							mysql_query($sql);
						}else 
						{
							$sql="DELETE from Bids where OrderID=$orderID";
							mysql_query($sql);
							
							findSeller($uid,$btc,$price,$time,false);
						}
						
						$amountLeft=$amount-$btc;
						if($amountLeft>0)
						{
							addOrder('Bids',$uid,$amountLeft,$price,2);
						}
						
						$usd=0;
					}else
					{
						$usd -= $amount*$price;
						if($status==2)
						{
							$sql="DELETE from Bids where OrderID=$orderID";
							mysql_query($sql);
							
							findSeller($uid,$amount,$price,$time,false);
						}
					}
				}else 
				{
					$sql="Update Bids set Status=2 where OrderID=$orderID";
					mysql_query($sql);
				}
			}
		}else $result['error']="SQL Failed.";	
	}
}

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
		
		addOrder('Bids',$buyerID,$amount,$buyPrice,0);
	}
	return($amount);	
}

// returns amount left unbought
// throws GoxException
function findBuyer($uid,$amount,$sellPrice,$time)
{
	global $result;
	global $lastPrice;
	
	$buyPrice=$sellPrice*SPREAD;
	
	// TODO: this needs to also sort by time in cases of ties
	$sql="SELECT * from Bids where Status=1 and round(Price,6)>=$buyPrice order by Price desc";
	if(!$data=mysql_query($sql)) throw new GoxException("SQL Error.",$sql);

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
			if(!mysql_query($sql)) throw new GoxException("SQL Error.",$sql);
			$tradeAmount=$amount;
		}else
		{ // this sale covers the bid
			$sql="DELETE FROM Bids where OrderID=$bidID";
			if(!mysql_query($sql)) throw new GoxException("SQL Error.",$sql);
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
	
	addOrder('Asks',$uid,$amount,$sellPrice,0);
	
	return($amount);	
}

function getFunds($uid)
{
	global $result;
	
	$sql="SELECT USD,BTC FROM Users where UserID=$uid";
	$data=mysql_query($sql);
	if($data)
	{
		$row=mysql_fetch_array($data);
		if($row)
		{
			$btc=round($row['BTC']/BASIS,2);
			$usd=round($row['USD']/BASIS,2);
			if($usd<0)$usd=0;
			if($btc<0)$btc=0;
			
			$result['usds']= $usd;
			$result['btcs']= $btc;
			$_SESSION['usd'] = $usd;
			$_SESSION['btc'] = $btc;
			
		}else $result['error'] = "User not found.";
		
	}else $result['error']="SQL Failed.";
}


?>