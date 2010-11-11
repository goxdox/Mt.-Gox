
<fieldset>
<legend>Payment Notification Example</legend>
Here is an example of instant payment notification in PHP.<p>
This page will be called by us after a successful <a href="/examples/checkout">checkout</a>. 
<div class="code_box" >&lt;?php<xmp>$goxMerchID=0; // get this from your settings page;

$txn_id = htmlentities($_POST['txn_id']);
$goxName = htmlentities($_POST['payer_username']);
$amount = htmlentities($_POST['amount']);
$currency = htmlentities($_POST['currency_code']);
$custom= $_POST['custom'];

function httpsPost($url, $strRequest)
{
	$ch=curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1) ;
	curl_setopt($ch, CURLOPT_POSTFIELDS, $strRequest);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
} 

// make sure it is mtgox that is posting to the notification page
$strRequest = "txn_id=$txn_id&merchID=$goxMerchID&amount=$amount";
$result=httpsPost("https://mtgox.com/code/gateway/checkTxn.php",$strRequest);
if( strcmp($result, "ok") == 0) 
{
	// You should check for duplicate transactions here
	// this will ensure that some how an attacker isn't posting 
	// 	valid transactions over and over to you

	// Order is valid do what you need to do	
}else
{
	// Order is invalid. Don't ship.	
}
</xmp>?&gt;
</div> 
This simply posts to our checkout page.
Parameters:
<ul>
	<li>notify_url - (optional) where Mt Gox will call back on a successful transaction</li>
	<li>business - your Mt Gox user name</li>
	<li>currency_code - USD or BTC</li>
	<li>amount - Amount of transaction</li>
	<li>item_name - String displayed to user on checkout and in their Account history</li>
	<li>custom - Custom string only sent to your notify_url</li>
	<li>return - URL user is directed to after confirming the transaction</li>
</ul>
You will see all checkout activity in your Account History and on your <a href="/merch">Merchant Page</a>.<p>
If you want instant notification anytime a checkout happens you can enable <a href="/examples/ipn">Payment Notifications</a>.
</fieldset>