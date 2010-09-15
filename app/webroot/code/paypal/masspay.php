<?php

function paypalWithdraw($email,$amount)
{
	$emailSubject =urlencode('Mt.Gox Withdrawal');
	$receiverEmail = urlencode($email);
	$amount = urlencode($amount);
	
	$nvpStr="&EMAILSUBJECT=$emailSubject&RECEIVERTYPE=EmailAddress&CURRENCYCODE=USD&L_EMAIL0=$receiverEmail&L_Amt0=$amount";
		
	return( PPHttpPost($nvpStr) );
}


function PPHttpPost($nvpStr_) 
{
	// Set up your API credentials, PayPal end point, and API version.
	$API_UserName = urlencode('paypal_api1.theFarWilds.com');
	$API_Password = urlencode('W8QQC5ABS5364LV8');
	$API_Signature = urlencode('AdiIFmtwpOIPiy8vjxIIus1qZtSWAMO185EDoQozfMRG0QqeStvI0OZx');
	$API_Endpoint = "https://api-3t.paypal.com/nvp";
	$version = urlencode('51.0');

	// Set the curl parameters.
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $API_Endpoint);
	curl_setopt($ch, CURLOPT_VERBOSE, 1);

	// Turn off the server and peer verification (TrustManager Concept).
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);

	// Set the API operation, version, and API signature in the request.
	$nvpreq = "METHOD=MassPay&VERSION=$version&PWD=$API_Password&USER=$API_UserName&SIGNATURE=$API_Signature$nvpStr_";

	//echo($nvpreq);
	
	// Set the request as a POST FIELD for curl.
	curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);

	// Get response from the server.
	$httpResponse = curl_exec($ch);

	return($httpResponse);
	//echo($httpResponse);
	/*
	if(!$httpResponse) {
		exit("$methodName_ failed: ".curl_error($ch).'('.curl_errno($ch).')');
	}

	// Extract the response details.
	$httpResponseAr = explode("&", $httpResponse);

	$httpParsedResponseAr = array();
	foreach ($httpResponseAr as $i => $value) {
		$tmpAr = explode("=", $value);
		if(sizeof($tmpAr) > 1) {
			$httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
		}
	}

	if((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
		exit("Invalid HTTP Response for POST request($nvpreq) to $API_Endpoint.");
	}

	return $httpParsedResponseAr;
	*/
}

?>