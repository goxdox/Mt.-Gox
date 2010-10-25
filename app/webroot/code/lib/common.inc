<?php 
define('SPREAD',1);
define('HALF_SPREAD',1);
define('ROUNDING',4);
define('BASIS',1000);

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
				$usdAmount=$price*$amount*HALF_SPREAD;
				$sql="UPDATE Users set BTC=BTC+$amount, USD=USD-$usdAmount where userid=$buyerID";
				if(mysql_query($sql))
				{
					$usdAmount=$price*$amount/HALF_SPREAD;
					$sql="UPDATE Users set BTC=BTC-$amount, USD=USD+$usdAmount where userid=$sellerID";
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
								$sql="INSERT INTO Activity (UserID,DeltaBTC,DeltaUSD,Type,TypeID,BTC,USD,Date) values ($buyerID,$amount,-$usdAmount,2,$tradeID,$btc,$usd,$time)";
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
											$sql="INSERT INTO Activity (UserID,DeltaBTC,DeltaUSD,Type,TypeID,BTC,USD,Date) values ($sellerID,-$amount,$usdAmount,1,$tradeID,$btc,$usd,$time)";
											if(mysql_query($sql))
											{
												$sql="Commit";
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

function addOrder($table,$uid,$amount,$price,$status,$time)
{
	global $result;
	
	if($amount>0)
	{
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
		mysql_query($sql);
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
						$sql="Update Asks set Status=1,Amount=$btc where OrderID=$orderID";
						mysql_query($sql);
						
						$amountLeft=$amount-$btc;
						addOrder('Asks',$uid,$amountLeft,$price,2,$date);
						
						$btc=0;
					}else
					{
						$btc -= $amount;
						if($status==2)
						{
							$sql="Update Asks set Status=1 where OrderID=$orderID";
							mysql_query($sql);
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
						$sql="Update Bids set Status=1,Amount=$btc where OrderID=$orderID";
						mysql_query($sql);
						
						$amountLeft=$amount-$btc;
						if($amountLeft>0)
						{
							addOrder('Bids',$uid,$amountLeft,$price,2,$date);
						}
						
						$usd=0;
					}else
					{
						$usd -= $amount*$price;
						if($status==2)
						{
							$sql="Update Bids set Status=1 where OrderID=$orderID";
							mysql_query($sql);
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
			
		}else $result=array( 'error' => "User not found." );
		
	}else $result['error']="SQL Failed.";
}


?>