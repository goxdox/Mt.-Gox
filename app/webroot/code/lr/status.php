<?
include('../../../noserve/config.inc');
include("../lib/functions.inc");
include("../lib/common.inc");

include("functions.php");


logMsg("lr payment");

global $LR_SECURITY_WORD;
global $LR_ACCOUNT_NUMBER;
global $LR_STORE_NAME;


function validate($txn_id)
{
	$sql="SELECT count(*) from LROrders where txn_id='$txn_id'";
	if(getSingleDBValue($sql))
	{
		return(false);
	}else
	{
		global $LR_SECURITY_WORD;
		global $LR_ACCOUNT_NUMBER;
		global $LR_STORE_NAME;
		//$request = new LRAPIRequest($LR_STORE_NAME, $LR_SECURITY_WORD);
		return(validateTransaction($txn_id, $LR_ACCOUNT_NUMBER, $LR_STORE_NAME, $LR_SECURITY_WORD));
	}
}

// You can recall any sent parameter using these sample codes:
// $_REQUEST["some_baggage_field"];
// $_REQUEST["lr_store"];
// $_REQUEST["lr_paidto"];
// $_REQUEST["lr_paidby"];
// and others..

// Building a string to be hashed
$str = 
  $_REQUEST["lr_paidto"].":".
  $_REQUEST["lr_paidby"].":".
  stripslashes($_REQUEST["lr_store"]).":".
  $_REQUEST["lr_amnt"].":".
  $_REQUEST["lr_transfer"].":".
  $_REQUEST["lr_currency"].":".
  $LR_SECURITY_WORD;
  

//Calculating hash
$hash = strtoupper(hash("sha256", $str));

//Let's check that all parameters exist and match and that the hash 
//string we computed matches the hash string that was sent by LR system.
if(isset($_REQUEST["lr_paidto"]) &&  
    $_REQUEST["lr_paidto"] == strtoupper($LR_ACCOUNT_NUMBER) &&
    isset($_REQUEST["lr_store"]) && 
    stripslashes($_REQUEST["lr_store"]) == $LR_STORE_NAME &&
    isset($_REQUEST["lr_encrypted"]) &&
    $_REQUEST["lr_encrypted"] == $hash) 
{
	$userID=$_REQUEST["lr_merchant_ref"];
	$amount=$_REQUEST["lr_amnt"];
	$fee=$_REQUEST["lr_fee_amnt"];
	$buyerLR=$_REQUEST["lr_paidby"];
	$txn_id=$_REQUEST["lr_transfer"];
	
	logMsg("valid lr $userID $amount");
	
	db_connect();
	
	if(validate($txn_id)) 
	{
		$time=time();
		$sql = "INSERT INTO LROrders (transactionID, userID, lrAccount, amount, fee, date) values ($txn_id, $userID, $buyerLR, $amount, $fee, $time)";
	
		$result = mysql_query($sql);
		if(!$result) { logMsg("status: $query failed"); exit(); }
		$sql="SELECT LAST_INSERT_ID()";
		$orderID=getSingleDBValue($sql);
		logMsg("4.1");
		mysql_query('BEGIN');
		try{
			$netAmount=($amount-$fee)*BASIS;
			$sql="Update Users set USD=USD+$netAmount where userid=$userID";
			if(!mysql_query($sql)) throw new Exception($sql);
			logMsg("4.2");
			$sql="SELECT USD,BTC from Users where UserID=$userID";
			if(!($data=mysql_query($sql))) throw new Exception($sql);
			if(!($row=mysql_fetch_array($data)))  throw new Exception("User not found");
			$usd=$row[0];
			$btc=$row[1];
			$sql="INSERT into Activity (UserID,deltaUSD,type,TypeID,TypeData,BTC,USD,Date) values ($userID,$netAmount,11,$orderID,$buyerLR,$btc,$usd,$time)";
			if(!mysql_query($sql)) throw new Exception($sql);
			logMsg("4.3");
			mysql_query('COMMIT');
		}catch(Exception $e)
		{
			mysql_query("rollback");
			logMsg("status failed: $sql");  
		}
	}else 
	{
		logMsg("lr hacking");
	}
	logMsg("4.4");
}
else 
{
	logMsg("invalid lr");
}

?>
