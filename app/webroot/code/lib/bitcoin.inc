<?php 
require_once 'jsonRPCClient.php';


function BC_getNewAddr($label)
{
	$bitcoind = new jsonRPCClient('http://jed:test@127.0.0.1:8332');	
	$ret=$bitcoind->getnewaddress($label);
	return($ret);
	
}


function BC_shutdown()
{
	$bitcoind = new jsonRPCClient('http://jed:test@127.0.0.1:8332');	
	$ret=$bitcoind->stop();
}

function BC_info()
{
	$bitcoind = new jsonRPCClient('http://jed:test@127.0.0.1:8332');	
	$ret=$bitcoind->getinfo();
	return($ret);
}

function BC_sendFunds($uid,$amount,$addr,$usd,$btc)
{
	global $result;
	
	$bitcoind = new jsonRPCClient('http://jed:test@127.0.0.1:8332');	
	$res=$bitcoind->sendtoaddress("$addr",$amount/BASIS);
	
	if($res=="sent")
	{	
		mysql_query('begin');
			
		try{
			$time=time();
			$btc -= $amount;
			$deltaBTC= -$amount;
			$sql="INSERT INTO Activity (UserID,DeltaBTC,Type,TypeData,BTC,USD,Date) values ($uid,$deltaBTC,4,'$addr',$btc,$usd,$time)"; 
			if(!mysql_query($sql)) throw new Exception("SQL Error");
			
			$sql="UPDATE Users set BTC=BTC-$amount where UserID=$uid";
			if(!mysql_query($sql)) throw new Exception("SQL Error");
			
			mysql_query('commit');
		}catch(Exception $e)
		{
			mysql_query('rollback');
			$result['error']= $e->getMessage();
		}
		
		checkAskOrders($uid);
	}else 
	{
		logMsg($res);
		$result['error']="Withdrawal not processed: $res";
	}
}

// Should be called periodically
// look through all the pending transactions and see if any have cleared
// remove old ones from DB
function BC_process_AddFunds()
{
	global $result;
	
	$count=0;
	$oldest=(60*60*24*10);
	$sql="UPDATE AddBTC set Status=2 where Status=0 and Date<$oldest";
	if(mysql_query($sql))
	{
		$sql="SELECT AddID,UserID,RecvAddr from AddBTC where status=0";
		$data=mysql_query($sql);
		if($data)
		{
			while($row=mysql_fetch_array($data))
			{	
				$addID=$row[0];
				$uid=$row[1];
				$addr=$row[2];
				
				$bitcoind = new jsonRPCClient('http://jed:test@127.0.0.1:8332');	
				//$bitcoind->getinfo();
				$total=BASIS*$bitcoind->getreceivedbylabel("$addID",5);
				$time=time();
				
				if($total>0)
				{
					mysql_query('begin');
					try{
						$sql="UPDATE AddBTC set Status=1,Amount=$total where AddID=$addID";
						if(!mysql_query($sql)) throw new Exception("SQL Error");
						$sql="UPDATE Users set BTC=BTC+$total where UserID=$uid";
						if(!mysql_query($sql)) throw new Exception("SQL Error");
						
						$sql="SELECT USD,BTC from Users where UserID=$uid";
						if(!($data=mysql_query($sql))) throw new Exception("SQL Error");
						if(!($row=mysql_fetch_array($data))) throw new Exception("SQL Error");
						$usd=$row[0];
						$btc=$row[1];
						
						$sql="INSERT INTO Activity (UserID,DeltaBTC,Type,TypeID,TypeData,BTC,USD,Date) values ($uid,$total,3,$addID,'$addr',$btc,$usd,$time)"; 
						if(!mysql_query($sql)) throw new Exception("SQL Error: $sql");
						
						mysql_query('commit');
						
						checkAskOrders($uid);
					}catch(Exception $e)
					{
						mysql_query('rollback');
						$result['error']= $e->getMessage();
					}
				}
			}	
		}else $result['error']="SQL Error";
	}else $result['error']="SQL Error";
}

function BC_process_Merch()
{
	global $result;
	
	$count=0;
	$oldest=(60*60*24*10);
	$sql="UPDATE MerchantOrders set Status=2 where Status=0 and Date<$oldest";
	if(mysql_query($sql))
	{
		$sql="SELECT OrderID,MerchantID,Amount,custom,notifyURL,txn_id from MerchantOrders where status=0";
		$data=mysql_query($sql);
		if($data)
		{
			while($row=mysql_fetch_array($data))
			{	
				$orderID=$row[0];
				$merchID=$row[1];
				$amount=round($row[2],4);
				$custom=$row[3];
				$notifyURL=$row[4];
				$txn_id=$row[5];
				//echo("$addID");
				
				$bitcoind = new jsonRPCClient('http://jed:test@127.0.0.1:8332');	
				//$bitcoind->getinfo();
				$total=BASIS*round( $bitcoind->getreceivedbylabel("m$orderID",5),4);
				
				if($total>0)
				{
					if($total==$amount)
					{
						$sql="UPDATE MerchantOrders set Status=1 where OrderID=$orderID";			
					}else
					{
						$sql="UPDATE MerchantOrders set Status=3, AmountRecv=$total where OrderID=$orderID";
					}
					if($notifyURL)
					{
						notifyMerch($notifyURL,"",$custom,$txn_id,'BTC',$total);
					}
					mysql_query($sql);
					$sql="UPDATE Users set BTC=BTC+$total where UserID=$merchID";
					mysql_query($sql);
					
					checkAskOrders($merchID);
				}
			}	
		}else $result['error']="SQL Error";
	}else $result['error']="SQL Error";
}

?>