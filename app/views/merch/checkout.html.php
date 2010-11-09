<script type="text/javascript" language="javascript" src="/js/jquery.validate.js"></script>
<script type="text/javascript" language="javascript">

$(document).ready(function(){
	$("#form1").validate();
  });

function onSave()
{
	var valid=$("#form1").valid();
	if(valid)
	{
		$('#status').text="Logging in...";
		$('#error').text="";
		
		$.post("/code/gateway/customerConfirm.php", $('#form1').serialize() , onServer , "json" );	
	}
	return(false);
}


function onServer(data)
{
	if(data.result==1) window.location="<?= $return ?>";
	else
	{
		$('#error').text(data.error);
		$('#status').text(data.status);
	}
}

</script>

<div id="ticker">Use Mt Gox to send or recieve money for free! <a href="/">learn more</a></div>
<div id="page">

<p></p>
<div class="container">
	<h2><?= $business ?></h2>
	<hr>
	
	
	<form id="form1" onsubmit="return onSave()"  >
	<input type="hidden" name="notify_url" value="<?= $notify_url ?>">
		<input type="hidden" name="merchID" value="<?= $merchID ?>">
		<input type="hidden" name="currency_code" value="<?= $currency_code ?>">	
	    <input type="hidden" name="custom" value="<?= $custom ?>" >
		<input type="hidden" name="return" value="<?= $return ?>">
		<input type="hidden" name="amount" value="<?= $amount ?>"> 
	<fieldset>
	 <legend>Confirm Transaction</legend>
	 Mt.Gox securely processes payments for <?= $business ?>. You can finish paying in a few clicks.
	 <table class="btcx_table" width="100%" >
		<tr><td><?= $item_name ?> </td><td><?= $dollarName ?> <?= $amount ?> <?= $btcName ?></td></tr>
	</table>
	 <h3>Do you really want to send <?= $dollarName ?> <?=$amount ?> <?= $btcName ?> to <?=$business ?> ?</h3>
	
	<?php 
	if(!$gUserID){
	?>
	<table class="btcx_table">
	<thead><tr><th colspan=2>Login to your Mt. Gox account to pay</th></tr></thead>
	<tr><td>User Name</td><td><input type="text" name="username"  class="required" minlength="2" /></td></tr>
	<tr><td>Password</td><td><input type="password" name="password" class="required" minlength="5"/></td></tr>
	<tr><td colspan=2><input type="submit" value="Confirm Payment" /></td></tr>
	</table> 
	<a href="/users/forgot" >Forgot your password?</a>
	<?php }else { ?>
	<input type="submit" value="Confirm Payment" />
	<?php } ?>
	<div id="status"></div>
	<div id="error"></div>
	</fieldset>
	</form>
</div>

</div>
