<?php 
include('../../noserve/config.inc');
include("lib/functions.inc");


db_connect();

$username=mysql_real_escape_string($_POST["name"]);
$email=mysql_real_escape_string($_POST["email"]);
$password=mysql_real_escape_string($_POST["pass"]);
$clean=strtolower($username);


$sql="SELECT count(*) From Users where CleanName='$clean' limit 1";

if(!getSingleDBValue($sql))
{
	$md5pass=md5($password);
	$time=time();
	$newToken=generateRandomString(20);
	$ip = mysql_real_escape_string($_SERVER['REMOTE_ADDR']);
			
	$sql="INSERT INTO Users (Username,CleanName,Password,Email,merchToken,signUpIP,Date) values ('$username','$clean','$md5pass','$email','$newToken','$ip',$time)";
	if( mysql_query($sql) )
	{
		$result=array( 'status' => "Registered!  <a href='/login'>Login now</a>" );
	}else $result=array( 'error' => "SQL Error." );
}else 
{
	$result=array( 'error' => "Sorry that User Name is already taken." );
}

echo( json_encode($result));

?>