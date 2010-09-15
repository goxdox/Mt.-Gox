<?php 
/*
if( !isset($_SESSION['UserID']) || $_SESSION['UserID'] != 1)
{
	$set= isset($_SESSION['UserID']);
	$id= $_SESSION['UserID'];
	die("No $set  $id ");
} 
*/ 
?>
<script>
function onCmd($cmd)
{
	$.post("/code/admin.php", { "cmd": $cmd }, onServer  );
}

function onServer(data)
{
	//$('#error').text(data.error);
	$('#status').text(data);
	//$('#debug').text(data.debug);
}

function onWith()
{
	$('#status').text="Withdrawing...";
	$('#error').text="";
	
	$.post("/code/admin/with.php", $('#form1').serialize() , onServer , "json" );

	return(false);
}

function onMasspay()
{
	$('#status').text="Withdrawing...";
	$('#error').text="";
	
	$.post("/code/admin.php", $('#mpForm').serialize() , onServer , "json" );

	return(false);
}

</script>

<div id="status"></div>
<div id="error"></div>
<div id="debug"></div>
<fieldset>
 <legend>Admin</legend>
<table class="btcx_table">
<tr><td>Stop bitcoind</td><td><input type="button" value="Stop"  onClick="onCmd('stop')" /></td></tr>
<tr><td>Process</td><td><input type="button" value="Process"  onClick="onCmd('process')" /></td></tr>
<tr><td>Info</td><td><input type="button" value="Info"  onClick="onCmd('info')" /></td></tr>
<tr><td>Stats</td><td><input type="button" value="Stats"  onClick="onCmd('stats')" /></td></tr>
</table>
</fieldset>

<form id="mpForm" onsubmit="return onMasspay()"  >
<input type="hidden" name="cmd" value="masspay" />
<fieldset>
 <legend>Paypal MassPay</legend>
 This doesn't hit the DB at all.
<table class="btcx_table">
<tr><td>Email</td><td><input type="text" name="email" id="email"  /></td></tr>
<tr><td>Amount</td><td><input type="text" name="amount" id="amount" /></td></tr>
<tr><td colspan=2><input type="submit" value="Do it!" /></td></tr>
</table> 
</fieldset>
</form>

<form id="form1" onsubmit="return onWith()"  >
<fieldset>
 <legend>USD Withdrawal</legend>
<table class="btcx_table">
<tr><td>UserID</td><td><input type="text" name="userid" id="userid"  /></td></tr>
<tr><td>Amount</td><td><input type="text" name="amount" id="amount" /></td></tr>
<tr><td colspan=2><input type="submit" value="Do it!" /></td></tr>
</table> 
</fieldset>
</form>

