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

function onClaim()
{
	$('#status').text="Withdrawing...";
	$('#error').text="";
	
	$.post("/code/admin/claim.php", $('#claimForm').serialize() , onServer , "json" );

	return(false);
}

function onReverse()
{
	$('#status').text="Reverseing...";
	$('#error').text="";
	
	$.post("/code/admin/reverse.php", $('#reverseForm').serialize() , onServer , "json" );

	return(false);
}

function onChange()
{
	$('#changeStatus').html('<div id="status"></div>');
	$('#status').text="Changing...";
	$('#error').text="";
	
	$.post("/code/admin/changeUSD.php", $('#form1').serialize() , onServer , "json" );

	return(false);
}

function onLRWith()
{
	$('#status').text="Withdrawing...";
	$('#error').text="";
	
	$.post("/code/admin/lrWithdraw.php", $('#lrForm').serialize() , onServer , "json" );

	return(false);
}

function onAddBTC()
{
	$('#status').text="Changeing...";
	$('#error').text="";
	
	$.post("/code/admin/changeBTC.php", $('#addBTC').serialize() , onServer , "json" );

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

<form id="lrForm" onsubmit="return onLRWith()"  >
<fieldset>
 <legend>LR Withdraw</legend>
 This will enter in DB as if the user requested this.
<table class="btcx_table">
<tr><td>UserID</td><td><input type="text" name="userid"   /></td></tr>
<tr><td>Amount</td><td><input type="text" name="amount" /></td></tr>
<tr><td>LR Account</td><td><input type="text" name="account" /></td></tr>
<tr><td colspan=2><input type="submit" value="Do it!" /></td></tr>
</table> 
</fieldset>
</form>


<form id="form1" onsubmit="return onChange()"  >
<fieldset>
 <legend>USD Add Balance</legend>
 Add or subtract balance here. It will make an entry in activity and checkorders
<table class="btcx_table">
<tr><td>UserID</td><td><input type="text" name="userid" id="userid"  /></td></tr>
<tr><td>Amount</td><td><input type="text" name="amount" id="amount" /></td></tr>
<tr><td colspan=2><input type="submit" value="Do it!" /></td></tr>
</table> 
<div id='changeStatus'></div>
</fieldset>
</form>

<form id="addBTC" onsubmit="return onAddBTC()"  >
<fieldset>
 <legend>BTC Add Balance</legend>
 Add or subtract balance here. It will make an entry in activity and checkorders
<table class="btcx_table">
<tr><td>UserID</td><td><input type="text" name="userid" id="userid"  /></td></tr>
<tr><td>Amount</td><td><input type="text" name="amount" id="amount" /></td></tr>
<tr><td colspan=2><input type="submit" value="Do it!" /></td></tr>
</table> 
</fieldset>
</form>

<form id="reverseForm" onsubmit="return onReverse()"  >
<fieldset>
 <legend>Reverse Trade</legend>
<table class="btcx_table">
<tr><td>TradeID</td><td><input type="text" name="tradeid" /></td></tr>
<tr><td colspan=2><input type="submit" value="Do it!" /></td></tr>
</table> 
</fieldset>
</form>

<form id="claimForm" onsubmit="return onClaim()"  >
<fieldset>
 <legend>Claim User's funds</legend>
<table class="btcx_table">
<tr><td>UserID</td><td><input type="text" name="userid" id="userid"  /></td></tr>
<tr><td colspan=2><input type="submit" value="Take all funds" /></td></tr>
</table> 
</fieldset>
</form>

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

