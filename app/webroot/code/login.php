<?php 
include('../../noserve/config.inc');
include("lib/functions.inc");
include('lib/session.php');
include('lib/common.inc');

if(isset($_POST['name']) && isset($_POST['pass']) )
{
	$name=mysql_real_escape_string($_POST['name']);
	$pass=mysql_real_escape_string($_POST['pass']);
	$clean_name=strtolower($name);
	if($clean_name=='espire') die("stop it. email me");
/*	
	if(isset($_POST['remember']))
	{
		$rem=1;
	}else $rem=0;
*/	
	db_connect();

	// check these against the db
	$md5pass=md5($pass);
	
	$sql = "select userid,btc,usd,MerchOn from Users where CleanName='$clean_name' and password='$md5pass'";
	$data=mysql_query($sql);
	if($data)
	{
		$row=mysql_fetch_array($data);
		if($row)
		{	
			$userID=$row[0];
			$btc=round( $row[1]/BASIS,2);
			$usd=round( $row[2]/BASIS,2);
			if($usd<0)$usd=0;
			if($btc<0)$btc=0;
			
			$merchon=$row[3];
			$_SESSION['UserID'] = $userID;
			$_SESSION['UserName'] = $name;
			$_SESSION['btc'] = $btc;
			$_SESSION['usd'] = $usd;
			$_SESSION['Merch']= $merchon;
			
			$serverName=$_SERVER["SERVER_NAME"];
			$result=array( 'loc' => "https://$serverName");
			
			$ip = mysql_real_escape_string($_SERVER['REMOTE_ADDR']);
			$sql="UPDATE Users set LastLogIP='$ip' where userID='$userID'";
			mysql_query($sql);
			logMsg($sql);
		
		}else $result=array( 'error' => "Sorry Username and Password don't match.");
	}else
	{
		$result['error'] = "SQL Failed.";
		$result['debug'] = $sql;
	}
}else $result=array( 'error' => "Invalid." );
	
echo( json_encode($result));
?>