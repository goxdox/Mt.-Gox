<?php

	#
	# included in ipn.php
	# this code inserts a record into 'orders'
	#
	$amountHeld=($gross-$fee)*1000;
	
	$time=time();
	$query = "INSERT INTO Orders (".
			 "txn_id, ".
			 "userID, ".
			 "date, ".
			 "email, ".
			 "first_name, ".
			 "last_name, ".
			 "gross, ".
			 "fee, ".
			 "amountHeld, ".
			 "street, ".
			 "city, ".
			 "state, ".
			 "zip, ".
			 "country) ";

	$query .= " VALUES (".
			  "'$txn_id', ".
			  "$userID,".
			  "$time, ".
			  "'$email', ".
			  "'$first_name', ".
			  "'$last_name', ".
			  "$gross, ".
				"$fee, ".
				"$amountHeld, ".
			  "'$street', ".
			  "'$city', ".
			  "'$state', ".
			  "'$zip', ".
			  "'$country') ";

	//echo $query;
	$result = mysql_query($query);
	if(!$result) { logMsg("ipn.php: $query failed"); exit(); }
	$sql="SELECT LAST_INSERT_ID()";
	$orderID=getSingleDBValue($sql);
	
	mysql_query('BEGIN');
	try{
		$netAmount=$netAmount*1000;
		$sql="Update Users set USD=USD+$netAmount, fundsHeld = fundsHeld+$amountHeld where userid=$userID";
		if(!mysql_query($sql)) throw new Exception($sql);
		
		$sql="SELECT USD,BTC from Users where UserID=$userID";
		if(!($data=mysql_query($sql))) throw new Exception($sql);
		if(!($row=mysql_fetch_array($data)))  throw new Exception("User not found");
		$usd=$row[0];
		$btc=$row[1];
		$sql="INSERT into Activity (UserID,DeltaUSD,Type,TypeID,BTC,USD,Date) values ($uid,$netAmount,6,$orderID,$btc,$usd,$time)";
		if(!mysql_query($sql)) throw new Exception($sql);
		
		mysql_query('COMMIT');
	}catch(Exception $e)
	{
		mysql_query("rollback");
		logMsg("ipn.php: $query failed"); 
		exit(); 
	}
	
?>