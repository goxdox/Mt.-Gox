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
	if(data.xml)
	{
		$('#liqXml').value=data.xml;
		$('#liqSig').value=data.sig;
		
		//$('#liqForm').submit();
			
	}
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

			$('#status').text('Calling Liqpay...');
			
			//$.post("/code/liqpay/checkout.php", $("#usdForm").serialize(), onServer , "json" );
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
<form id="usdForm" action="/code/liqpay/checkout.php" onsubmit="return onUSD()" >
<fieldset>
 <legend>Add $ US Dollars</legend>
 Mt Gox now uses <a href="http://liqpay.com" target="_blank">LiqPay.com</a> for adding and withdrawing USD. LiqPay is easy to use and you can fund your account with a credit card.
<table class="btcx_table" >
<tr><td>Amount</td><td><input type="text"  name="amount" class="required number" min="1" /></td></tr> 
<tr><td>Mobile Number</td><td><input type="text"  name="phone" class="required"  /></td></tr>
<tr align="center"><td>Method</td><td>
	<input type="radio" name="method" value="card" checked /> Credit Card<br> 
	<input type="radio" name="method" value="liqpay" /> Liqpay Balance</td></tr>
<tr align="center"><td colspan=2  ><input type="submit" value="Add Dollars"  /></td></tr>
<tr><td colspan=2 ><i>Added funds should be available in a few minutes.</i></td></tr>
</table>
</fieldset>
</form>
<form id="liqForm" action='https://www.liqpay.com/?do=clickNbuy' method='POST'>
      <input type='hidden' id="liqXml" name='operation_xml'  />
      <input type='hidden' id="liqSig" name='signature'  />
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


