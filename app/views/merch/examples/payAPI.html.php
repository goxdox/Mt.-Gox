<?php
use \lithium\analysis\Logger;


function httpsPost($url, $strRequest)
{
	// Initialisation
	$ch=curl_init();
	// Set parameters
	curl_setopt($ch, CURLOPT_URL, $url);
	// Return a variable instead of posting it directly
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	// Active the POST method
	curl_setopt($ch, CURLOPT_POST, 1) ;
	// Request
	curl_setopt($ch, CURLOPT_POSTFIELDS, $strRequest);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	// execute the connexion
	$result = curl_exec($ch);
	// Close it
	curl_close($ch);
	return $result;
} 

function sendFunds($goxName,$currency,$amount)
{
	global $gGoxToken;
	
	$itemName =urlencode('Five Grinder Withdrawal');
	$receiver = urlencode($goxName);
	if($currency==1) $currency='USD';
	else if($currency==2) $currency='BTC';
	else return(0);
	
	
	$amount = urlencode($amount);
	$goxToken = urlencode($gGoxToken);
	
	$postVars ="merchID=$gGoxMerchID&token=$goxToken&item=$itemName&receiver=$receiver&currency=$currency&amount=$amount";
	
	$httpResponse=httpsPost("https://mtgox.com/gateway/send.php",$postVars);
	if($httpResponse=='ok') return(1);
	else if($httpResponse=='none') return(2);
	else Logger::alert("goxwithdraw fail: $httpResponse");
	
	return(0);
}


?>