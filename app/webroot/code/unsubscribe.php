<?php 
include('../../noserve/config.inc');
include('lib/functions.inc');

if(isset($_GET['email']))
{
	db_connect();
	
	$email=mysql_real_escape_string($_GET['email']);
	$sql="UPDATE btcx.Users set TradeNotify=0 where Email='$email'";
	mysql_query($sql);
	//echo($sql);
	
	echo("$email will no longer receive trade notifications. Thanks.");
?>



<?php 
}else echo("No email specified?");


?>