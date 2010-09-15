<?php 
include('../../noserve/config.inc');
include("lib/functions.inc");

	
if(isset($_REQUEST['rid']) &&  isset($_REQUEST['pass']) )
{

  //  $result['status']="1";
  
  db_connect();

  $pass=mysql_real_escape_string($_REQUEST['pass']);
  $rid=mysql_real_escape_string($_REQUEST['rid']);
  
  
  $md5pass=md5($pass);
	

	
	$sql="SELECT UserID from PasswordResets where ResetID='$rid'";
	$uid=getSingleDBValue($sql);
	
	if($uid)
	{
	
		$sql="UPDATE Users set Password='$md5pass' where UserID=$uid";
		mysql_query($sql);
		
		$sql="DELETE FROM PasswordResets where ResetID='$rid'";
		mysql_query($sql);
		
		$result['status'] = "Your password has been reset.";
	}else $result['error'] = "Request not Found.";
  
}else $result['error'] = "Invalid.";
	
echo( json_encode($result));
?>