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

function onUSD()
{
	if(userID)
	{
		if($("#usdForm").valid())
		{
			$("#custom").val(userID);

			$('#status').text('Loading...');
			//$("#usdForm").submit();
			//alert($("#usdForm").serialize());
			return(true);
		}
	}else
	{
		$('#error').text('Really you must be logged in to add funds!');
	}
	return(false);
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
<hr>
<form name="usdForm" id="usdForm" action="https://www.paypal.com/cgi-bin/webscr" method="post" onsubmit="return onUSD()" >	
	<input type="hidden" name="notify_url" value="http://mtgox.com/code/paypal/ipn.php">
	<input type="hidden" name="cmd" value="_xclick">

	<input type="hidden" name="business" value="paypal@theFarWilds.com">
	<input type="hidden" name="currency_code" value="USD">
	
	<input type="hidden" name="item_name" id="item_name" value="Mt Gox Bitcoin Exchange">		
    <input type="hidden" id="custom" name="custom" value="0" >

	<input type="hidden" name="no_note" value="1">
	<input type="hidden" name="no_shipping" value="1">

	<input type="hidden" name="return" value="https://mtgox.com/users/thanks"> 
<fieldset>
 <legend>Add $ US Dollars</legend>
 We currently accept credit cards or paypal. <br>
 Keep in mind that both credit cards and paypal charge a processing fee.<br>
 Other options coming soon...
<table class="btcx_table" >
<tr><td>Amount:</td><td><input type="text" id="pp_amount" name="amount" class="required number" min="1" ></input></td></tr> 
<tr align="center"><td colspan=2  ><input type="submit" value="Add Dollars"  /></td></tr>
<tr><td colspan=2 ><span class="notice" >IMPORTANT: You will not be able to withdraw these funds or the BTC bought with them for 1 month.</span> This is to limit paypal charge backs. You can still buy and sell with these funds in the meantime. I'm working on other funding solutions. <b>
	<i>Added funds should be available in a few minutes.</i></td></tr>
</table>
</fieldset>
</form>

<hr>
	
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



Please <script>document.write('<A href="mailto:' + 'info' + '@' + 'mt' + 'gox.com' + '">' + 'suggest' + '</a>')</script> other types of funding methods!


