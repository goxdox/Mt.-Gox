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
		$('#status').text="Resetting Password...";
		$('#error').text="";
	
		var resetID='<?= $resetID ?>';
		if(resetID)
		{
			var pass=$('#password').val();
		
			$.post("/code/resetPass.php", { "rid": resetID , "pass": pass }, onServer , "json" );
		}else $('#error').text("Not valid");
	}
	return(false);
}


function onServer(data)
{
	$('#error').text(data.error);
	$('#status').text(data.status);
}

</script>
<p></p>

<div id="status"></div>
<div id="error"><?= $error ?></div>
<form id="form1" onsubmit="return onSave()" >
<fieldset>
 <legend>Reset Your Password</legend>
<table class="btcx_table">
<tr><td>User Name</td><td colspan=2><?= $name ?></td></tr>
<tr><td>Password</td><td colspan=2><input type="password" name="password" id="password" class="required" minlength="5"/></td></tr>
<tr><td>Password Again</td><td colspan=2><input type="password" name="passworda" id="passworda" equalTo="#password" /></td></tr>
<tr><td colspan=3><input type="submit" name="reset" value="Reset" /></td></tr>
</table> 
</fieldset>
</form>

