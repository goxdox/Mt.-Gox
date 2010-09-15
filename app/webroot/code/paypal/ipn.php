<?php
include('../../../noserve/config.inc');
include("../lib/functions.inc");

logMsg("start ipn.php");

// read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-validate';

foreach($_POST as $key => $value) {
	$value = urlencode(stripslashes($value));
	$req .= "&$key=$value";
}

// TODO: actually make the post. for some reason the post isn't getting a reply from them?
// post back to PayPal system to validate
$header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
// TEMP $fp = fsockopen('ssl://www.paypal.com', 80, $errno, $errstr, 30);
$fp=1; // TEMP

logMsg("ipn.php: $errno  $errstr");

if(!$fp) 
{
	logMsg("Http error");
	// HTTP ERROR
} else 
{
	// TEMP fputs($fp, $header . $req);
	// TEMP while(!feof($fp)) 
	{
		// TEMP $res = fgets ($fp, 1024);
		$res="VERIFIED";  // TEMP
		
		//logMsg($res);
		
		if(strcmp ($res, "VERIFIED") == 0) 
		{
			logMsg("VERIFIED");
// check the payment_status is Completed
// check that txn_id has not been previously processed
// check that receiver_email is your Primary PayPal email
// check that payment_amount/payment_currency are correct
// process payment
			#get out what they ordered
			include "variables.php";
			
			if($payment_status=="Completed" && $netAmount)
			{
					
				#see if the txn_id has come up before

				db_connect();
			
				$query = "SELECT * from Orders where txn_id = '$txn_id'";
				$result = mysql_query($query);
				if(!$result) { logMsg("ipn.php: $query failed"); exit(); }
				
				$txn_exists = mysql_num_rows($result);
		
				if(!$txn_exists) 
				{
					# if this is a new txn_id then insert
					include "insert_order.php";
			
				}else 
				{
					logMsg("Duplicate txn_id");
					# maybe update status?
					# this transaction is probably a completion of a pending transaction or something else like this
				}
				
			}else
			{
				logMsg("payment_status != Completed");
			}

		}else if (strcmp ($res, "INVALID") == 0) {
			// log for manual investigation
			logMsg("INVALID");
		}
	}
	// TEMP fclose ($fp);
}

logMsg("done\r\n");
?>


