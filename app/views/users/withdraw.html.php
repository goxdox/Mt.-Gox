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
Mt Gox uses <a href="http://libertyreserve.com/?ref=<?= $LR_ACCOUNT_NUMBER ?>" target="_blank">LibertyReserve.com</a> for withdrawing USD. 
You will be charged 1% to withdraw by Liberty Reserve.<p>
<p>
If you live in the US or EU we can send it to you by direct deposit.<br> 
Minimum withdraw amount in the US by direct deposit is $800. There is no fee.<br>
There is no minimum in the EU but we charge a 2% fee to withdraw.<br>
Send us an email for details.
<p>
<table class="btcx_table">
<tr><td>Currency</td><td><input type="radio" id="btcRadio" name="group1" value="BTC" checked />Bitcoins<br><input type="radio" id="usdRadio" name="group1" value="USD" />US Dollars</td></tr>
<tr><td>Amount to Withdraw</td><td><input type="text" name="amount" id="amount" class="number required" /></td></tr>
<tr class="usd" ><td>Liberty Reserve Account</td><td><input type="text" name="account" /></td></tr>
<tr class="btc" ><td>Bitcoin Address</td><td  width="300px" ><input type="text" name="btca" id="btca" /></td></tr>
<tr><td colspan=3><input type="button" value="Send Request" onClick="onWith()"/></td></tr>
</table>
<div id="error"></div>
<div id="status"></div>
</fieldset>
</form>     

