<?php 

if($notify)
{
	$notify='<input type="checkbox" name="notify" id="notify" checked="true" />';
}else $notify='<input type="checkbox" name="notify" id="notify" />';


if($merch)
{
	$merch='<input type="checkbox" name="merch" id="merch" onClick="onMerch()" checked="true" />';
}else $merch='<input type="checkbox" name="merch" onClick="onMerch()" id="merch" />';

if($payAPIOn)
{
	$payAPI='<input type="checkbox" name="payAPI" id="payAPI" onClick="onPayAPI()" checked />';
}else $payAPI='<input type="checkbox" name="payAPI" id="payAPI" onClick="onPayAPI()" />';

?>
<script type="text/javascript" language="javascript" src="/js/jquery.validate.js"></script>
<script type="text/javascript" language="javascript">

$(document).ready(function(){
	$("#setForm").validate();
	$("#passForm").validate();

	//$("#merch").bind('click',onMerch());

	onMerch();
	onPayAPI();
	
  });

function onMerch()
{
	//alert($('#merch').val());
	
	if($('#merch').attr('checked'))
	{
		$('#noteRow').show();
	}else $('#noteRow').hide();
}

function onSave()
{
	var valid=$("#setForm").valid();
	if(valid)
	{
		$('#status').text("Changing...");
		$('#error').text("");
	
		var name=$("#username").val();
		var pass=$('#password').val();
		var email=$('#email').val();
		
		$.post("/code/changeSettings.php", $("#setForm").serialize() , onServer , "json" );
	}

	return(false);
}

function onPass()
{
	var valid=$("#passForm").valid();
	if(valid)
	{
		$('#status').text("Changing...");
		$('#error').text("");
	
		var newpass=$("#passwordn").val();
		var oldpass=$('#passwordo').val();
		
		$.post("/code/user/changePass.php", { "newpass": newpass , "oldpass": oldpass }, onServer , "json" );
	}else return(false);
}

function onToken()
{
	$.post("/code/gateway/changeToken.php", { }, onServer , "json" );
	return(false);
}

function onPayAPI()
{
	if($('#payAPI').attr('checked'))
	{
		$('#tokenForm').show();
	}else $('#tokenForm').hide();
}

function onServer(data)
{
	if(data.status) $('#error').text(data.error);
	else $('#error').text('');
	
	if(data.status) $('#status').text(data.status);
	else $('#status').text('');

	if(data.token) $('#token').text(data.token);
}
</script>


<div id="status"></div>
<div id="error"><?php echo($error); ?></div>
<form id="setForm" onsubmit="return onSave()"  >
<fieldset>
<legend>Change Settings</legend>
<table class="btcx_table">
<tr><td>Email</td><td><input type="text" name="email" id="email" class="email" value="<?=$email ?>" /></td></tr>
<tr><td>Email Trade Notifications</td><td><?php echo($notify); ?></td></tr>
<!--
<tr><td>Display Merchant Services<br><a href="/merch/about">About Merchant Services</a></td><td><?php echo($merch); ?></td></tr>
<tr class="merch" id="noteRow" ><td>Your Merchant ID</td><td><?= $gUserID ?></td></tr>
<tr class="merch" ><td>Enable <a href="/merch/paymentAPI">Payment API</a></td><td><?php echo($payAPI); ?></td></tr>
-->
<tr><td colspan=2><input type="submit" value="Change" /></td></tr>
</table>
</fieldset>
</form> 

<form id="tokenForm" onsubmit="return onToken()" class="merch" >
<fieldset>
<legend>Merchant Token</legend>
Keep this token private. If Payment API is enabled above this token can be used to automatically send payment to people.<br>
You can generate a new token anytime you feel your old token has been compromised.
<table class="btcx_table">
<tr><td>Merchant ID</td><td><?= $gUserID ?></td></tr>
<tr><td>Current Token</td><td id="token"><?= $token ?></td></tr>
<tr><td colspan=2><input type="submit" value="Generate New Token" /></td></tr>
</table>
</fieldset>
</form> 
    

<form id="passForm" action="" >
<fieldset>
<legend>Change Password</legend>
<table class="btcx_table">
<tr><td>Old Password</td><td><input type="password" name="passwordo" id="passwordo" class="required" /></td></tr>
<tr><td>New Password</td><td><input type="password" name="passwordn" id="passwordn" class="required" minlength="5" /></td></tr>
<tr><td>New Password Again</td><td><input type="password" name="passwordna" id="passwordna" equalTo="#passwordn" /></td></tr>
<tr><td colspan=2><input type="button" value="Change" onClick="onPass()"/></td></tr>
</table>
</fieldset>
</form>     