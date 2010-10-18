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
	
	$phone= ereg_replace( '[^0-9]+', '', $phone );
	$phone="+$phone";
	
	$method=$_POST['method'];

	$xml="<request>      
		<version>1.2</version>
        <action>send_money</action>
		<result_url>http://mtgox.com/thanks</result_url>
		<server_url>http://mtgox.com/code/liqpay/ipn.php</server_url>
		<merchant_id>$LIQPAY_MERCHANT_ID</merchant_id>
		<order_id>$userID</order_id>
		<amount>$amount</amount>
		<currency>USD</currency>
		<description>Funding Mt Gox</description>
		<default_phone>$phone</default_phone>
		<pay_way>$method</pay_way> 
		</request>";
	
	logMsg($xml);
	
	$xml_encoded = base64_encode($xml); 
	$lqsignature = base64_encode(sha1($LIQPAY_SIG.$xml.$LIQPAY_SIG,1));
	

	$url="https://www.liqpay.com/?do=clickNbuy";
	$strRequest="operation_xml=$xml_encoded&signature=$lqsignature";
	$ret=httpPost($url,$strRequest);
	
	logMsg($ret);
	
	$result['status'] ="You should recieve a SMS to $phone from liqpay shortly.";

}else
{ // not found in db
	$result['error'] ="Not logged in.";
}

echo( json_encode($result));

?>