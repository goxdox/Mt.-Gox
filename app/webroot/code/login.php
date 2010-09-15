<?php 
include('../../noserve/config.inc');
include("lib/functions.inc");
include('lib/session.php');
	
if(isset($_POST['name']) && isset($_POST['pass']) )
{
	$name=mysql_real_escape_string($_POST['name']);
	$pass=mysql_real_escape_string($_POST['pass']);
	
	db_connect();

	// check these against the db
	$md5pass=md5($pass);
	$clean_name=strtolower($name);
	$sql = "select userid,btc,usd,MerchOn from Users where CleanName='$clean_name' and password='$md5pass'";
	$data=mysql_query($sql);
	if($data)
	{
		$row=mysql_fetch_array($data);
		if($row)
		{	
			$userID=$row[0];
			$btc=$row[1];
			$usd=$row[2];
			$merchon=$row[3];
			$_SESSION['UserID'] = $userID;
			$_SESSION['UserName'] = $name;
			$_SESSION['btc'] = round($btc,2);
			$_SESSION['usd'] = round($usd,2);
			$_SESSION['Merch']= $merchon;
			
			$serverName=$_SERVER["SERVER_NAME"];
			$result=array( 'loc' => "https://$serverName");
		
		}else $result=array( 'error' => "Sorry Username and Password don't match.");
	}else
	{
		$result['error'] = "SQL Failed.";
		$result['debug'] = $sql;
	}
}else $result=array( 'error' => "Invalid." );
	
echo( json_encode($result));
?>