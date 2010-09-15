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
		$('#status').text="Sending...";
		$('#error').text="";
	
		var name=$("#username").val();
		var email=$('#email').val();
		
		$.post("/code/sendPassReset.php", { "name": name, "email" : email  }, onServer , "json" );	
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


<form id="form1" onsubmit="return onSave()"  >
<fieldset>
 <legend>Password Recovery</legend>
 We will email you a link to reset your password. Please check your spam filter if you don't see the mail.<p>
 Enter either your username or your email.
<table class="btcx_table">
<tr><td>User Name</td><td><input type="text" name="username" id="username"  minlength="2" /></td></tr>
<tr><td>or Email</td><td><input type="text" name="email" id="email" class="email" minlength="5"/></td></tr>
<tr><td colspan=2><input type="submit" value="Recover my password" /></td></tr>
</table> 
<div id="status"></div>
<div id="error"></div>
</fieldset>
</form>
    

