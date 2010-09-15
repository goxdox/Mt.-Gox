<?php

	$txn_id = htmlentities($_POST['txn_id']);
	$email = htmlentities($_POST['payer_email']);
	$gross = htmlentities($_POST['mc_gross']);
	$fee= htmlentities($_POST['mc_fee']);
	$currency = htmlentities($_POST['mc_currency']);
	$payment_status = htmlentities($_POST['payment_status']);
    
	$first_name = htmlentities($_POST['first_name']);
	$last_name = htmlentities($_POST['last_name']); 
	$street = htmlentities($_POST['address_street']);
	$state =htmlentities($_POST['address_state']);
	$city = htmlentities($_POST['address_city']);
	$country = htmlentities($_POST['address_country']);
	$zip = htmlentities($_POST['address_zip']);
	
	$userID= (int)($_POST['custom']);;
	$netAmount=$gross-$fee;
	
	logMsg("userID: $userID ($netAmount)");
	
	
	
	if($currency=="USD")
	{
		// TODO: check $price 
		//$gold= round($gross * 50.125);
	}else 
	{
		logMsg("Not US currency");
		$netAmount=0;
	}
?>	