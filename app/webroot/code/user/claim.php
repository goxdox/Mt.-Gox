<?php 
include('../../../noserve/config.inc');
include("../lib/functions.inc");
include("../lib/common.inc");



db_connect();

$username=mysql_real_escape_string($_POST["username"]);
if(isset($_POST["password"])) $password=mysql_real_escape_string($_POST["password"]);
else $password=0;
$email=mysql_real_escape_string($_POST["email"]);
$token=mysql_real_escape_string($_POST["token"]);
$clean=strtolower($username);
$time=time();

mysql_query("BEGIN");
try{
	$sql="SELECT sendID,amount,currency,fromID from SendMoney where token='$token' and toEmail='$email' and status=1";
	if(!$data=mysql_query($sql) )  throw new Exception("SQL Error");
	if(!$row=mysql_fetch_array($data)) throw new Exception("Claim not found.");
	
	$sendID=$row["sendID"];
	$amount=$row['amount'];
	$currency=$row['currency'];
	$merchID=$row["fromID"];
	$userID=0;
	
	if($currency==1)
	{
		$usd=$amount;
		$btc=0;
		$deltaBTC=0;
		$deltaUSD=$amount;
	}else if($currency==2)
	{
		$usd=0;
		$btc=$amount;
		$deltaBTC=$amount;
		$deltaUSD=0;
	}else throw new Exception("Unknown Currency");

	if($password)
	{	// create a new account
		$sql="SELECT userID From Users where CleanName='$clean'";
		$userID=getSingleDBValue($sql);
		if(!$userID)
		{
			$md5pass=md5($password);		
			$newToken=generateRandomString(20);
			$ip = mysql_real_escape_string($_SERVER['REMOTE_ADDR']);
			
			$sql="INSERT INTO Users (Username,CleanName,Password,email,merchToken,usd,btc,signUpIP,Date) values ('$username','$clean','$md5pass','$email','$newToken',$usd,$btc,'$ip',$time)";
			if(! mysql_query($sql) )  throw new Exception("SQL Error");
			
			$userID=getSingleDBValue("SELECT LAST_INSERT_ID()");
			
			$result['status'] = "Registered!  <a href='/login'>Log in now</a>";
		}else 
		{
			$sql=0;
			throw new Exception("Sorry that User Name is already taken.");
		}	
	}else 
	{ // attach to an old account
		$sql="SELECT userID,btc,usd From Users where CleanName='$clean'";
		if(!$data=mysql_query($sql) )  throw new Exception("SQL Error");
		if(!$row=mysql_fetch_array($data)) throw new Exception("Sorry that User Name wasn't found.");
		$usd=$row['usd']+$deltaUSD;
		$btc=$row['btc']+$deltaBTC;
		$userID=$row['userID'];
		if($userID)
		{
			$sql="UPDATE Users set USD=USD+$deltaUSD,BTC=BTC+$deltaBTC where userid=$userID";
			if(! mysql_query($sql) )  throw new Exception("SQL Error");	
		}
	}
	
	$sql="UPDATE EmailMap set status=2 where Userid=$userID and email='$email'";
	if(!$data=mysql_query($sql) )  throw new Exception("SQL Error");
		
	if(mysql_affected_rows()==0)
	{
		$sql="INSERT INTO EmailMap (UserID, Email,Status, Date) values ($userID,'$email',2,$time)";
		if(!mysql_query($sql) )  throw new Exception("SQL Error");
	}
	
	
	$sql="UPDATE SendMoney set status=2 where SendID=$sendID";
	if(! mysql_query($sql) )  throw new Exception("SQL Error");
	
	$merchName=getSingleDBValue("SELECT username from Users where userid=$merchID");
	
	$sql="INSERT into Activity (UserID,DeltaUSD,DeltaBTC,Type,TypeData,BTC,USD,Date) values ($userID,$deltaUSD,$deltaBTC,10,'$merchName',$btc,$usd,$time)";
	if(!mysql_query($sql)) throw new Exception("SQL Error");
	
	mysql_query("commit");
	$result['status'] = "Funds Claimed!";
}catch(Exception $e)
{
	mysql_query("rollback");
	if($sql) logMsg("claim: $sql");
	$result['error'] = $e->getMessage();
	$result['status'] = "";
}		

echo( json_encode($result));

?>