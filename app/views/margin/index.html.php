<?php 
/* 
Margin account is enabled if they add funds 
They need to see:
	Margin balance
	Limit they can buy
	Open Orders
	
	Add Order change order or position
 */		
?>
<script type="text/javascript" language="javascript" src="/js/jquery.validate.js"></script>
<script type="text/javascript" language="javascript">
$(document).ready(function(){

	$("#orderForm").validate();	

	$('.marginBalance').text(<?= $marginBalance ?>);
	
	if(!<?= $gUserID ?>)
	{
		$('#error').text('You must be <a href="/users/login">logged in</a> to add or remove funds!');
	}	
});

function onOrder()
{
	if(<?= $gUserID ?>)
	{
		if($("#addForm").valid())
		{
			$('#addStatus').text('Placing Order...');
			$('#addError').text('');
			
			$.post("/code/margin/placeOrder.php", $("#orderForm").serialize(), onOrderResult , "json" );
		}
	}else
	{
		$('#addError').text('You must be logged in.');
	}
	return(false);
}

function onEditOrder(orderID)
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
}

function onWithResult(data)
{
	$('#withError').html(data.error);
	$('#withStatus').html(data.status);
}
</script>

<?= $this->view()->render(array('element' => 'marginBar') ); ?>

<fieldset>
 <legend>Place Order</legend>
All margin trading is done on one pair the BTC/USD. You can be either long or short this pair. 
<form id="orderForm" onsubmit="return onOrder()" >
<table class="btcx_table">
<tr><td><input type="radio" name="long" value="1" checked /> Long</td><td><input type="radio" name="long" value="0" /> Short</td></tr>
<tr><td>Amount</td><td><input type="text" name="amount" class="number required" /></td></tr>
<tr><td>Price</td><td><input type="text" name="price" class="number required" /></td></tr>
<tr><td>Take Profit</td><td><input type="text" name="profit" class="number" /></td></tr>
<tr><td>Stop Loss</td><td><input type="text" name="loss" class="number" /></td></tr>
<tr><td colspan=2><input type="submit" value="Place Order" /></td></tr>
</table>
</form>
<div id="orderError" class="error"></div>
<div id="orderStatus" class="status"></div>
</fieldset>


<fieldset>
 <legend>Your Positions</legend>

</fieldset>

<fieldset>
 <legend>Your Open Orders</legend>

</fieldset>