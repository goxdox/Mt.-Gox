<?	
	
function isValidAccountNumber($acct) {
	return ereg("^(U|X)[0-9]{1,}$", $acct);
}

function generateId() 
{
	return time().rand(0,9999);
}

function createAuthToken($secWord) 
{
	$datePart = gmdate("Ymd");
	$timePart = gmdate("H");	
	
	$authString = $secWord.":".$datePart.":".$timePart;
	
	//echo "<p>AuthString: ".$authString."</p>";  
	
	$sha256 = hash("sha256", $authString);
	
	return strtoupper($sha256);
}	

function validateTransaction($txn_id, $accID, $storeName, $secWord)
{
	$id=generateId();
	$token=createAuthToken($secWord);
	
	$xml="<HistoryRequest id='$id'>";
	$xml .="<Auth>";
	$xml .= "<ApiName>$storeName</ApiName>";
	$xml .= "<Token>$token</Token>";
	$xml .= '</Auth>';
	$xml .= '<History><CurrencyId>LRUSD</CurrencyId>';
	$xml .= "<AccountId>$accID</AccountId>";
	$xml .= "<ReceiptId>$txn_id</ReceiptId>";
	$xml .='</History></HistoryRequest>';
	
	logMsg($xml);
	
	$url = "https://api.libertyreserve.com/xml/history.aspx?req=".urlencode($xml);
	
	$handler=curl_init($url);
	
	ob_start();
	curl_exec($handler);
	$content=ob_get_contents();
	ob_end_clean();
	curl_close($handler);
			
	
	$doc = new DOMDocument();	
	if(!$doc->loadXML($content)) {
		logMsg("Can't parse: $content");
		return false;
	}
	
	$rootElem = $doc->getElementsByTagName("HistoryResponse")->item(0);
	$responseId = $rootElem->getAttribute("id");
	
	if ($responseId != $id) {
		logMsg("IDs don't match: $content");
		return false;
	}		
	
	$counter = 0;
	
	$total = $rootElem->getElementsByTagName("Pager")->item(0)->getElementsByTagName("TotalCount")->item(0);
	
	if($total->tagName=="1") return(true);
	
	logMsg("not found: $content $total");
	return false;
}

// throws GoxError
function LRWithdraw($account,$amount)
{
	global $LR_SECURITY_WORD;
	global $LR_ACCOUNT_NUMBER;
	global $LR_STORE_NAME;

	$id=generateId();
	$token=createAuthToken($LR_SECURITY_WORD);
	
	$xml = "<TransferRequest id='$id'>";
	$xml .= "<Auth>";
	$xml .= "<ApiName>$LR_STORE_NAME</ApiName>";
	$xml .= "<Token>$token</Token>";
	$xml .= '</Auth>';
	$xml .= "<Transfer><TransferType>transfer</TransferType>";
	$xml .= "<Payer>$LR_ACCOUNT_NUMBER</Payer>";
	$xml .=	"<Payee>$account</Payee>";
	$xml .=	'<CurrencyId>LRUSD</CurrencyId>';
	$xml .=		"<Amount>$amount</Amount>";
	$xml .=	'<Memo>MtGox.com withdrawal</Memo>';
	$xml .=	'<Anonymous>false</Anonymous>';
	$xml .=	'</Transfer></TransferRequest>';
	
	
	
	logMsg($xml);
	
	$url = "https://api.libertyreserve.com/xml/transfer.aspx?req=".urlencode($xml);
	
	$handler=curl_init($url);
	
	ob_start();
	curl_exec($handler);
	$content=ob_get_contents();
	ob_end_clean();
	curl_close($handler);
	
	logMsg("Reply: \r\n $content");
			
	return(true);
}


//////	

?>