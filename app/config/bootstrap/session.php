<?php 
include('../noserve/config.inc');
include('code/lib/functions.inc');
include('code/lib/session.php');

if(isset($_SESSION['UserID']))
{
	$gUserID=$_SESSION['UserID'];
	$gUsd=$_SESSION['usd'];
	$gBtc=$_SESSION['btc'];
	$gUserName=$_SESSION['UserName'];
	$gMerchOn=$_SESSION['Merch'];
		
}else $gUserID=0;

?>