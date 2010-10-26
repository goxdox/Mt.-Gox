<script type="text/javascript" language="javascript" src="/js/jquery.validate.js"></script>
<script type="text/javascript" language="javascript">
$(document).ready(function(){

	$("#withForm").validate();
	
	if(userID)
	{
		
	}else
	{
		$('#error').text('You are not logged in.');
	}

	$('#btcRadio').change(function() {
		  setBTCRadio();
		});

	$('#usdRadio').change(function() {
		  setUSDRadio();
		});


	setBTCRadio();
	
});

function setBTCRadio()
{
	$(".btc").show();
	$(".usd").hide();
}

function setUSDRadio()
{
	$(".btc").hide();
	$(".usd").show();
}



function onWith()
{
	if(userID)
	{
		if($("#withForm").valid())
		{
			$('#status').text('Withdrawing...');
			$('#error').text('');
			
			$.post("/code/withdraw.php", $("#withForm").serialize(), onServer , "json" );
		}
	}else
	{
		$('#error').text('You must be logged in.');
	}
	
}

function onServer(result)
{
	if(result.error) $('#error').text(result.error);
	else $('#error').text('');
	
	if(result.status) $('#status').text(result.status);
	else $('#status').text('');

	if(result.btcs)
	{
		$('.btcs').text(result.btcs);
	}
}
</script>


  
<p></p>

<form id="withForm" action="" >
<fieldset>
<legend>Withdrawal Funds</legend>
Mt Gox uses <a href="http://libertyreserve.com/?ref=<?= $LR_ACCOUNT_NUMBER ?>" target="_blank">LibertyReserve.com</a> for adding and withdrawing USD. Liberty Reserve is easy to use. 
We suggest you use <a href="http://exchangezone.com" target="_blank">ExchangeZone.com</a> to buy and sell your Liberty Reserve dollars.
 You will be charged 1% to withdraw by Liberty Reserve.<p>
 Currently all USD withdraw requests are being handled manually. 
<table class="btcx_table">
<tr><td>Currency</td><td><input type="radio" id="btcRadio" name="group1" value="BTC" checked />Bitcoins<br><input type="radio" id="usdRadio" name="group1" value="USD" disabled />US Dollars</td></tr>
<tr><td>Amount to Withdraw</td><td><input type="text" name="amount" id="amount" class="number required" /></td></tr>
<tr class="usd" ><td>Liberty Reserve Account</td><td><input type="text" name="account"  /></td></tr>
<tr class="btc" ><td>Bitcoin Address</td><td><input type="text" name="btca" id="btca" /></td></tr>
<tr><td colspan=3><input type="button" value="Send Request" onClick="onWith()"/></td></tr>
</table>
<div id="error"></div>
<div id="status"></div>
</fieldset>
</form>     

