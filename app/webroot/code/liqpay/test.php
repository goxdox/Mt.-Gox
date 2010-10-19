<?
include('../../../noserve/config.inc');
include('../lib/functions.inc');
include('../lib/session.php');
include('../lib/common.inc');

global $LIQPAY_MERCHANT_ID;
global	$LIQPAY_SIG;



$url="https://www.liqpay.com/?do=clickNbuy";
$method='card';
$phone='+20123145121';


<request>
                <version>1.2</version>
        <action>send_money</action>
                <result_url>http://mtgox.com/thanks</result_url>
                <server_url>http://mtgox.com/code/liqpay/ipn.php</server_url>
                <merchant_id>i9268561596</merchant_id>
                <order_id>515</order_id>
                <amount>1</amount>
                <currency>USD</currency>
                <description>Funding Mt Gox</description>
                <default_phone>4917650011855</default_phone>
                <pay_way>liqpay</pay_way>
<action>send_money</ action>
                </request>
                
	$xml="<request>      
		<version>1.2</version>
        <action>send_money</action>
		<result_url>http://mysite.com/lqanswer.php</result_url>
		<server_url>http://mysite.com/lqanswer.php</server_url>
		<merchant_id>$LIQPAY_MERCHANT_ID</merchant_id>
		<order_id>ORDER_1234</order_id>
		<amount>10</amount>
		<currency>USD</currency>
		<description>Description</description>
		<default_phone>13472565876</default_phone>
		<pay_way>card</pay_way> 
		</request>
		";
	
	
	$xml_encoded = base64_encode($xml); 
	$lqsignature = base64_encode(sha1($LIQPAY_SIG.$xml.$LIQPAY_SIG,1));
	


echo("<form action='$url' method='POST'>
      <input type='hidden' name='operation_xml' value='$xml_encoded' />
      <input type='hidden' name='signature' value='$lqsignature' />
	<input type='submit' value='Pay'/>
	</form>");
?>
	
