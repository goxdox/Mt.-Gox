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
		$('#status').text="Registering...";
		$('#error').text="";
	
		var name=$("#username").val();
		var pass=$('#password').val();
		var email=$('#email').val();
		
		$.post("/code/register.php", { "name": name , "pass": pass, "email": email }, onServer , "json" );
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


<form id="form1" onsubmit="return onSave()" >
<fieldset>
 <legend>Register a new account</legend>
<table class="btcx_table">
<tr><td>User Name</td><td colspan=2><input type="text" name="username" id="username"  class="required" minlength="2" /></td></tr>
<tr><td>Password</td><td colspan=2><input type="password" name="password" id="password" class="required" minlength="5"/></td></tr>
<tr><td>Password Again</td><td colspan=2><input type="password" name="passworda" id="passworda" equalTo="#password" /></td></tr>
<tr><td>Email</td><td><input type="text" name="email" id="email" class="email" minlength="5"/></td><td><i>note: this is optional but you can't recover your password without it.</i></td></tr>
<tr><td colspan=3><input type="submit" value="Register" /></td></tr>
</table> 
<div id="status"></div>
<div id="error"></div>
</fieldset>
</form>

