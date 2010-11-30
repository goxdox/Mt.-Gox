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
	$('#error').text('Note: You must be logged in to add funds!');
}

	
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
 Mt Gox uses <a href="http://libertyreserve.com/?ref=<?= $LR_ACCOUNT_NUMBER ?>" target="_blank">LibertyReserve.com</a> for adding and withdrawing USD. Liberty Reserve is easy to use. 
 We suggest you use <a href="http://exchangezone.com" target="_blank">ExchangeZone.com</a> to buy and sell your Liberty Reserve dollars.
 Liberty Reserve charges a 1% fee for transfers. 
<p>We can also accept wire transfers. Please send us an email if interested.
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


<fieldset>
 <legend>Add Euros</legend>
This process is done by bank transfer with minimal fees but is still manual. Send us an email.
</fieldset>




Please <script>document.write('<A href="mailto:' + 'info' + '@' + 'mt' + 'gox.com' + '">' + 'suggest' + '</a>')</script> other types of funding methods!


