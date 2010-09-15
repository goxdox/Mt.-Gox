<?php 
include('../../noserve/config.inc');
include("lib/functions.inc");
include('lib/session.php');
	
$name=0;
$email=0;
if(isset($_POST['name']) )
{
	$name=mysql_real_escape_string($_POST['name']);
	$clean_name=strtolower($name);
}else if(isset($_POST['name']) )
{
	$email=mysql_real_escape_string($_POST['email']);
	$email=strtolower($email);
}
	
if($name || $email)
{
	db_connect();


	if($name) $sql = "select UserID,UserName,Email from Users where CleanName='$clean_name'";
	else $sql = "select UserID,UserName,Email from Users where email='$email'";
	$data=mysql_query($sql);
	if($data)
	{
		if($row=mysql_fetch_array($data))
		{
			$uid=$row[0];
			$name=$row[1];
			$email=$row['Email'];
			
			if($email)
			{
				
				$time=time();
				$resetID=generateRandomString(10);
				$sql="INSERT INTO PasswordResets (ResetID,UserID,Username,Date) values ('$resetID',$uid,'$name',$time)";
				mysql_query($sql);
				
				$msg= "Someone has requested your password to be reset.\r\n";
				$msg.= "If this wasn't you, don't worry and you don't have to do anything else.\r\n";
				$msg.= "If you want to reset your password click the link below:\r\n";
				$msg.= "https://mtgox.com/users/resetPass/$resetID";
				$msg.= "\r\n\r\nThanks for using Mt. Gox!\r\n";
				
				$headers = "From: support@mtgox.com\r\n";
				$headers .= "Reply-To: <support@mtgox.com>\r\n";
				$headers .= "Return-Path: <support@mtgox.com>\r\n";
							
				mail($email,"mtgox.com Password Recovery",$msg,$headers);
				
				$result['status'] = "The link to reset your password has been mailed.";
				
			}else $result['error'] = "Sorry No email on file for this user.";

		}else $result['error'] = "Sorry this user wasn't found.";
	}else $result['error'] = "SQL Error";
			
}else $result=array( 'error' => "Enter in your User Name or Email." );
	
echo( json_encode($result));
?>