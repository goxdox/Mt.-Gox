<?php
include('../../../noserve/config.inc');
include('../lib/functions.inc');
include('../lib/session.php');
include('../lib/common.inc');

// make sure:
// user is logged in 
// valid amount
// valid address

global $LIQPAY_MERCHANT_ID;
global $LIQPAY_SIG;


if(isset($_SESSION['UserID']))
{
	$userID=(int)($_SESSION['UserID']);
	$amount=(float)$_POST['amount'];
	$phone=$_POST['phone'];
	
	$phone= preg_replace("/[^a-zA-Z0-9s]/", '', $phone);
	
	$randStr=generateRandomString(9);
	
	$method=$_POST['method'];

	$xml="<request>      
		<version>1.2</version>
        <action>send_money</action>
		<result_url>http://mtgox.com/thanks</result_url>
		<server_url>http://mtgox.com/code/liqpay/ipn.php</server_url>
		<merchant_id>$LIQPAY_MERCHANT_ID</merchant_id>
		<order_id>$userID.$randStr</order_id>
		<amount>$amount</amount>
		<currency>USD</currency>
		<description>Funding Mt Gox</description>
		<default_phone>$phone</default_phone>
		<pay_way>$method</pay_way> 
		</request>";
	
	//logMsg($xml);
	
	$xml_encoded= $result['xml'] = base64_encode($xml); 
	$lqsignature=$result['sig'] = base64_encode(sha1($LIQPAY_SIG.$xml.$LIQPAY_SIG,1));
	
}else
{ // not found in db
	$result['error'] ="Not logged in.";
}

/*
echo("<form action='https://www.liqpay.com/?do=clickNbuy' method='POST'>
      <input type='hidden' name='operation_xml' value='$xml_encoded' />
      <input type='hidden' name='signature' value='$lqsignature' />
	<input type='submit' value='Pay'/>
	</form>");
	*/
echo( json_encode($result));

?>