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
		
		$.post("/code/login.php", { "name": name , "pass": pass  }, onServer , "json" );	
	}
	return(false);
}


function onServer(data)
{
	if(data.loc) window.location=data.loc;
	else
	{
		$('#error').text(data.error);
		$('#status').text(data.status);
	}
}

</script>
<p></p>


<form id="form1" onsubmit="return onSave()"  >
<fieldset>
 <legend>Login</legend>
<table class="btcx_table">
<tr><td>User Name</td><td><input type="text" name="username" id="username"  class="required" minlength="2" /></td></tr>
<tr><td>Password</td><td><input type="password" name="password" id="password" class="required" minlength="5"/></td></tr>
<tr><td colspan=2><input type="submit" value="Login" /></td></tr>
</table> 
<a href="/users/forgot" >Forgot your password?</a>
<div id="status"></div>
<div id="error"></div>
</fieldset>
</form>
    

