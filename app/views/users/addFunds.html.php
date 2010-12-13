<script type="text/javascript" language="javascript" src="/js/jquery.validate.js"></script>
<script type="text/javascript" language="javascript" src="/js/jquery.ui.js"></script>
<link rel="stylesheet" type="text/css" href="/css/jquery.ui.css" />
<script type="text/javascript" language="javascript">
var gBTCAmount=0;

$(document).ready(function(){

	//document.getElementByID("custom").value=userID;
	$("#usdForm").validate();
	
	$("#btcForm").validate({
		  rules: {
			btcAmount: {
				number: true,
		      	required: true,
		      	min: 1
	    	}
		  }
		});
	
if(!userID)
{
	$('#error').html('<div style="text-align: center">You must <a href="/users/login">log in</a> to add Funds.</div>');
}

showUSDMethod('lrForm');

	
});

function onServer(data)
{
	$('#error').text(data.error);
	$('#status').text(data.status);
	if(data.btcAddr)
	{
		var dialog = $('#dialog')
		.html('Send '+gBTCAmount+' Bitcoins to this address: <p>'+ data.btcAddr)
		.dialog({
			height: 530,
			width: 400,
			title: 'One Last Step!'
		});
		
	}
}

function showUSDMethod(methodName)
{
	$("#lrForm").hide();
	$("#ccForm").hide();
	$("#cashForm").hide();
	$("#otherForm").hide();
	$("#euroForm").hide();
	$("#paxumForm").hide();
	$("#wireForm").hide();

	$("#"+methodName).show();
		
}


function onBTC()
{
	if(userID)
	{
		if($("#btcForm").valid())
		{
			gBTCAmount=$('#btcAmount').val();
			$.post("/code/addBTC.php", { "amount": gBTCAmount }, onServer , "json" );
		}
	}else
	{
		$('#error').text('Really you must be logged in to add funds!');
	}
	return(false);
}

</script>

<div id="dialog" ></div>

<div id="status"></div>
<div id="error"></div>
In order to trade on the exchange you must add either US Dollars (USD) or Bitcoins (BTC). You can redeem your funds at anytime.
<p>
<hr>

<fieldset>
 <legend>Add $ US Dollars</legend> 
<table class="btcx_table">
<tr><td>Select a Funding Method</td><td></td></tr>
<tr><td>
<input type="radio" name="group1" onclick="showUSDMethod('lrForm')" checked />Liberty Reserve<br>
<input type="radio" name="group1" onclick="showUSDMethod('ccForm')" />Credit Card<br>
<input type="radio" name="group1" onclick="showUSDMethod('cashForm')" />Cash or Check<br>
<input type="radio" name="group1" onclick="showUSDMethod('wireForm')" />Wire or ACH<br>
<input type="radio" name="group1" onclick="showUSDMethod('paxumForm')" />Paxum<br>
<input type="radio" name="group1" onclick="showUSDMethod('euroForm')" />Euro Bank Transfer<br>
<input type="radio" name="group1" onclick="showUSDMethod('otherForm')" />Other<br>
</td><td>

<div id="wireForm">
If your bank supports sending ACH payments this is the cheapest way to fund your Mt Gox account.<p> 
My bank charges $15 for an incoming wire from the US and $50 for an international one.<p>
Please email me if interested in his funding method.
</div>

<div id="cashForm">
You can mail check or cash to our official exchanger and he will send you Mt Gox credit.<p>
<div class="center"><a href="http://bitcoinmorpheus.tumblr.com/post/2301381008/how-to-buy-bitcoin-and-mt-gox-usd-with-cash-in-the">Cash Exchanger</a></div>
</div>

<div id="otherForm">
<a href="http://www.bitcoin.org/smf/index.php?topic=1561.0">This thread</a> has people willing to sell you Mt Gox $ in various ways. If you post there I'm sure you can get a 1:1 exchange rate from paypal or something else.
</div>

<div id="paxumForm">
<a href="http://paxum.com" target="_blank">Paxum.com</a> is a fairly straigtforward way to fund your account. <p>
Simply send payment to <b>paxum@mtgox.com</b> and I will credit your account.
</div>

<div id="euroForm">
Hopefully this will be set up soon...
</div>

<div id="ccForm">
You can use <a href="http://bitcoingateway.com" target="_blank">BitcoinGateway.com</a> to add funds with a credit card.<p>
</div>

<div id="lrForm" >
<a href="http://libertyreserve.com/?ref=<?= $LR_ACCOUNT_NUMBER ?>" target="_blank">LibertyReserve.com</a> can be used for adding and withdrawing USD. 
<form method="post" action="https://sci.libertyreserve.com/?ref=<?= $LR_ACCOUNT_NUMBER ?>">
 <input type="hidden" name="lr_acc" value="<?= $LR_ACCOUNT_NUMBER ?>">
 <input type="hidden" name="lr_store" value="<?= $LR_STORE_NAME ?>">    
 <input type="hidden" name="lr_currency" value="LRUSD">
 <input type="hidden" name="lr_comments" value="MtGox.com Funding">
 <input type="hidden" name="lr_merchant_ref" value="<?= $gUserID ?>">      
<table class="btcx_table" >
<tr align="center"><td><input type="submit" value="Add Dollars with Liberty Reserve"  /></td></tr>
<tr><td colspan=2 ><i>Added funds should be available in a few minutes.</i></td></tr>
</table>
</form>
</div>

</td></tr>
</table>


</fieldset>


<form id="btcForm" onsubmit="return onBTC()" >
<fieldset>
 <legend>Add Bitcoins</legend>
<table class="btcx_table">
<tr><td>Amount:</td><td><input type="text" name="btcAmount" id="btcAmount" ></input></td></tr>
<tr><td colspan=2><input type="submit" value="Send Bitcoins" /></td></tr>
<tr><td colspan=2 ><i>Please be patient, it may take a few hours for the Bitcoin network to confirm the transaction.</i></td></tr>
</table>
</fieldset>
</form>

<!--
<fieldset>
 <legend>Add Euros</legend>
This process is done by bank transfer with minimal fees but is still manual. Send us an email.
</fieldset>
-->



Please <script>document.write('<A href="mailto:' + 'info' + '@' + 'mt' + 'gox.com' + '">' + 'suggest' + '</a>')</script> other types of funding methods!


