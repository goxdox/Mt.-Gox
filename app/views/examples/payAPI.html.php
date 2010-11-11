<fieldset>
<legend>Payment API Example</legend>
Here is an example of using the payment API from PHP.
<div class="code_box" ><xmp>
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

function sendFunds($goxName,$currency,$amount)
{
	$goxToken="get this from your settings page";
	$goxMerchID=0; // get this from your settings page;
	
	$itemName =urlencode('Note to reciever');
	$receiver = urlencode($goxName);
	$currency = urlencode($currency);
	$amount = urlencode($amount);
	$goxToken = urlencode($goxToken);
	
	$postVars ="merchID=$goxMerchID&token=$goxToken&item=$itemName&receiver=$receiver&currency=$currency&amount=$amount";
	
	$httpResponse=httpsPost("https://mtgox.com/gateway/send.php",$postVars);
	if($httpResponse=='ok') 
	{
		// payment was sent successfully
		return(1);
	}else if($httpResponse=='none') 
	{
		// the username of the receiver wasn't found
		return(0);
	}else 
	{
		// some other error occured
	}
	
	return(0);
}
</xmp>
</div> 
You simply need to post to our https://mtgox.com/gateway/send.php page with the following parameters:
<ul>
	<li>merchID - Your Merchant ID. You can get this <a href="/users/settings">here</a>.</li>
	<li>token - Your Merchant PayAPI Token. You can get this <a href="/users/settings">here</a>.</li>
	<li>currency - USD or BTC</li>
	<li>amount - Amount of transaction</li>
	<li>item - String displayed to you and the receiver in their Account history.</li>
	<li>receiver - Mt Gox user name of the person you are trying to send to.</li>
</ul>
</fieldset>