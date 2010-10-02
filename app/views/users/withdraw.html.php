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

	$('#paypalRadio').change(function() {
		  setPaypalRadio();
		});

	$('#checkRadio').change(function() {
		  setCheckRadio();
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

function setCheckRadio()
{
	$(".paypal").hide();
	$(".check").show();
}

function setPaypalRadio()
{
	$(".check").hide();
	$(".paypal").show();
}



function onWith()
{
	if(userID)
	{
		if($("#withForm").valid())
		{
			var amount=$('#sellAmount').val();
			var price=$('#sellPrice').val();
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


<h2>Withdraw Funds from your Account</h2>
Paypal charges a withdraw fee of 2% or $1 whichever is less. So it is better for you to withdraw larger amounts at a time.  
<p></p>

<form id="withForm" action="" >
<fieldset>
<legend>Request Withdrawal</legend>
<table class="btcx_table">
<tr><td>Currency</td><td><input type="radio" id="btcRadio" name="group1" value="BTC" checked />Bitcoins<br><input type="radio" id="usdRadio" name="group1" value="USD" />US Dollars</td></tr>
<tr><td>Amount to Withdraw</td><td><input type="text" name="amount" id="amount" class="number required" /></td></tr>
<tr class="usd" ><td>Method</td><td><input type="radio" id="paypalRadio" name="method" value="paypal" checked />Paypal<br><input type="radio" id="checkRadio" name="method" value="check" />Check</td></tr>
<tr class="usd paypal" ><td>Paypal Email</td><td><input type="text" name="email" id="email" class="email" /></td></tr>
<tr class="usd check" ><td>Address</td><td><textarea cols="40" rows="5" name="address"></textarea></td></tr>
<tr class="btc" ><td>Bitcoin Address</td><td><input type="text" name="btca" id="btca" /></td></tr>
<tr class="usd check" ><td>Special Instructions</td><td><textarea cols="40" rows="5" name="special"></textarea></td></tr>
<tr><td colspan=3><input type="button" value="Send Request" onClick="onWith()"/></td></tr>
</table>
<div id="error"></div>
<div id="status"></div>
</fieldset>
</form>     

