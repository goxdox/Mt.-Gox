<script type="text/javascript" language="javascript" src="/js/jquery.validate.js"></script>
<script type="text/javascript" language="javascript" src="/js/jquery.ui.js"></script>
<link rel="stylesheet" type="text/css" href="/css/jquery.ui.css" />
<script type="text/javascript" language="javascript">

$(document).ready(function(){
	$("#oldForm").validate();
	$("#newForm").validate();
});

function onServer(data)
{
	$('#error').text(data.error);
	$('#status').text(data.status);

}


function onNew()
{
	if($("#newForm").valid())
	{
		$('#status').text('Signing Up...');
		$('#error').text('');
		
		$.post("/code/user/claim.php", $("#newForm").serialize(), onServer , "json" );
	}
	
	return(false);
}

function onOld()
{
	if($("#oldForm").valid())
	{
		$('#status').text('Linking Email...');
		$('#error').text('');
		
		$.post("/code/user/claim.php", $("#oldForm").serialize(), onServer , "json" );
	}
	
	return(false);
}

</script>

<div id="status"></div>
<div id="error"></div>
<h2><?= $merchName ?> has sent you <?= $amountStr ?>.</h2> 
Note from <?= $merchName ?>:<br>
<?= $note ?><p>

<fieldset>
 <legend>Claim Funds</legend>
Create a Mt Gox account to retrieve your funds.
  <form id="newForm"  onsubmit="return onNew()">
  <input type="hidden" name="email" value="<?= $email ?>" />
  <input type="hidden" name="token" value="<?= $token ?>" />
  
<table class="btcx_table">
<tr><td>User Name</td><td colspan=2><input type="text" name="username" class="required" minlength="2" value='<?= $email ?>' /></td></tr>
<tr><td>Password</td><td colspan=2><input type="password" name="password" id="password" class="required" minlength="5"/></td></tr>
<tr><td>Password Again</td><td colspan=2><input type="password" name="passworda" equalTo="#password" /></td></tr>
<tr><td colspan=3 style="text-align: center" ><input type="submit" value="Sign Up" /></td></tr>
</table> 
</form>

Or, associate <?= $email ?> with an existing Mt Gox account below
<form id="oldForm"  onsubmit="return onOld()">
	<input type="hidden" name="email" value="<?= $email ?>" />
  	<input type="hidden" name="token" value="<?= $token ?>" />
<table class="btcx_table" >
<tr><td>Your Existing Mt Gox User Name</td><td><input type="text"  name="username" class="required" minlength="2" /></td></tr> 
<tr><td colspan=2 style="text-align: center" ><input type="submit" value="Use Existing Account"  /></td></tr>
</table>
</form>


</fieldset>



 
 
 