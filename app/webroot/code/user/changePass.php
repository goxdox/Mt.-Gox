<?php 
include('../../../noserve/config.inc');
include("../lib/functions.inc");
include('../lib/session.php');
include('../lib/common.inc');

if ($_SESSION['UserID'])
{
	$uid		 = (int)$_SESSION['UserID'];
	$password 	 = isset($_POST['oldpass'])? mysql_real_escape_string($_POST['oldpass']) : '';
	$newPassword = isset($_POST['newpass'])? mysql_real_escape_string($_POST['newpass']) : '';

	if($password && $newPassword)
	{
		db_connect();
		
		$md5OldPass = md5($password);
		$md5NewPass = md5($newPassword);
		
		$pass = getSingleDBValue("SELECT COUNT(*) FROM Users WHERE UserID=$uid AND Password='$md5OldPass'");
		
		if ($pass)
		{
			$sql = "UPDATE Users SET Password='$md5NewPass' WHERE UserID=$uid";
			if (mysql_query($sql)) $result['status'] = 'Password was changed';
			else $result['error'] = 'Sorry password wasn\'t changed';
		}else $result['error'] = 'Old password is incorrect.';		
	} else $result['error'] = 'You should fill in both old password and new password fields';
} else $result['error'] = 'You must be logged in';

echo( json_encode($result));
?>