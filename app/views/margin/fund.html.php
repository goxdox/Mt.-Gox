<script type="text/javascript" language="javascript" src="/js/jquery.validate.js"></script>
<script type="text/javascript" language="javascript">
$(document).ready(function(){

	$("#addForm").validate();	
	$("#withForm").validate();

	$('.marginBalance').text(<?= $marginBalance ?>);
	
	if(!<?= $gUserID ?>)
	{
		$('#error').text('You must be <a href="/users/login">logged in</a> to add or remove funds!');
	}	
});

function onAdd()
{
	if(<?= $gUserID ?>)
	{
		if($("#addForm").valid())
		{
			$('#addStatus').text('Funding...');
			$('#addError').text('');
			
			$.post("/code/margin/addFunds.php", $("#addForm").serialize(), onAddResult , "json" );
		}
	}else
	{
		$('#addError').text('You must be logged in.');
	}
	return(false);
}

function onWith()
{
	if(<?= $gUserID ?>)
	{
		if($("#withForm").valid())
		{
			$('#withStatus').text('Withdrawing...');
			$('#withError').text('');
			
			$.post("/code/margin/withFunds.php", $("#withForm").serialize(), onWithResult , "json" );
		}
	}else
	{
		$('#withError').text('You must be logged in.');
	}
	return(false);
}


function onAddResult(data)
{
	$('#addError').html(data.error);
	$('#addStatus').html(data.status);
	$('.marginBalance').text(data.margin);
	
}

function onWithResult(data)
{
	$('#withError').html(data.error);
	$('#withStatus').html(data.status);
	$('.marginBalance').text(data.margin);
}
</script>

<?= $this->view()->render(array('element' => 'marginBar') ); ?>
<div id="status"></div>
<div id="error"></div>
<h2>Your current Margin Account Balance: $<span class="marginBalance"></span></h2>
<fieldset>
<legend>Fund your Margin Account</legend>
Your Margin Balance is denominated in USD.
This moves USD from your main Account to your Margin account.
<form id="addForm" onsubmit="return onAdd()" >
<table class="btcx_table">
<tr><td>Amount to Add</td><td><input type="text" name="amount" class="number required" /></td></tr>
<tr><td colspan=2><input type="submit" value="Fund" /></td></tr>
</table>
</form>
<div id="addError" class="error"></div>
<div id="addStatus" class="status"></div>
</fieldset>

<fieldset>
<legend>Withdraw from your Margin Account</legend>
Move USD from your Margin Balance to your Main USD Balance. <br>
Be careful if you have open positions as this could trigger a margin call.
<form id="withForm" onsubmit="return onWith()">
<table class="btcx_table">
<tr><td>Amount to Withdraw</td><td><input type="text" name="amount" class="number required" /></td></tr>
<tr><td colspan=2><input type="submit" value="Withdraw" /></td></tr>
</table>
</form>
<div id="withError" class="error"></div>
<div id="withStatus" class="status"></div>
</fieldset>

