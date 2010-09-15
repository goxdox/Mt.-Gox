<?php
include('../../noserve/config.inc');
include('lib/functions.inc');
include('lib/session.php');
include('lib/common.inc');

// make sure:
// user is logged in 

db_connect();

if(!isset($_SESSION['UserID']))
{
	if(isset($_POST['name']) && isset($_POST['pass']))
	{
		$name=mysql_real_escape_string($_POST['name']);
		$pass=mysql_real_escape_string($_POST['pass']);
		
		// check these against the db
		$md5pass=md5($pass);
		$clean_name=strtolower($name);
		$sql = "select userid from Users where CleanName='$clean_name' and password='$md5pass'";
		$uid=getSingleDBValue($sql);
	}
}else
{
	$uid=(int)($_SESSION['UserID']);
}

if($uid)
{
	$result = array();
	
	getFunds($uid);
}else
{ // not found in db
	$result=array( 'error' => "Not logged in." );
}

echo( json_encode($result));

?>
