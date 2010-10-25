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
	
});

function onSend()
{
	if(userID)
	{
		if($("#sendForm").valid())
		{
			$('#status').text('Sending...');
			$('#error').text('');
			
			$.post("/code/user/send.php", $("#sendForm").serialize(), onServer , "json" );
		}
	}else
	{
		$('#error').text('You must be logged in.');
	}
	return(false);
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

	if(result.usds)
	{
		$('.usds').text(result.usds);
	}

}
</script>

<form id="sendForm" onsubmit="return onSend()" >
<fieldset>
<legend>Send Money</legend>
Send USD or BTC for free to anyone with an email address.
<p>
You can send money to anyone with an email address. If they have a Mt Gox account the funds will show up in their account. If they don't have one then they will be emailed instructions for claiming their money. 
<table class="btcx_table">
<tr><td>Currency</td><td><input type="radio" id="btcRadio" name="currency" value="BTC" checked />Bitcoins<br><input type="radio" id="usdRadio" name="currency" value="USD" />US Dollars</td></tr>
<tr><td>Amount to Send</td><td><input type="text" name="amount" class="number required" /></td></tr>
<tr><td>Email of Receiver</td><td><input type="text" name="email" class="email required" /></td></tr>
<tr><td>Note to Receiver</td><td><textarea cols="40" rows="5" name="note"></textarea></td></tr>
<tr><td colspan=3><input type="submit" value="Send Money" /></td></tr>
</table>
<div id="error"></div>
<div id="status"></div>
</fieldset>
</form>     

