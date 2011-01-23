<?	
	
function isValidAccountNumber($acct) 
{
	//if(strlen($acct)>10) return(0);
	//if(strpos($acct, '>')) return(0);
	//return preg_match("^(U|X)[0-9]{1,}$", $acct);
	return preg_match('/^(U|X)[0-9]{1,}$/', $acct);
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
	if(!isValidAccountNumber($accID)) 
	{
		logMsg("Invalid account: $accID");
		return(false);
	}
	
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
	
	//logMsg($xml);
	
	$url = "https://api.libertyreserve.com/xml/history.aspx?req=".urlencode($xml);
	
	$handler=curl_init($url);
	
	ob_start();
	curl_exec($handler);
	$content=ob_get_contents();
	ob_end_clean();
	curl_close($handler);
	
	logMsg("Reply");
			
	
	$doc = new DOMDocument();	
	if(!$doc->loadXML($content)) {
		logMsg("Can't parse: $content");
		return false;
	}
	
	$nodes = $doc->getElementsByTagName("HistoryResponse");
	if($nodes->length)
	{
		$rootElem = $nodes->item(0);
		$responseId = $rootElem->getAttribute("id");
		
		if ($responseId != $id) {
			logMsg("IDs don't match: $content");
			return false;
		}		
		
		$counter = 0;
		
		$nodes = $rootElem->getElementsByTagName("Pager");
		if($nodes->length)
		{
			$nodes = $nodes->item(0)->getElementsByTagName("TotalCount");
			if($nodes->length)	
			{
				$total = $nodes->item(0);
				if($total->nodeValue=="1") 
				{
					logMsg("valid reply");
					return(true);
				}
			}
		}
	}
	
	logMsg("not found: $content $total");
	return false;
}

// throws GoxError
function LRWithdraw($account,$amount)
{
	if(!isValidAccountNumber($account)) return(1);
	
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
	$xml .=	"<Amount>$amount</Amount>";
	$xml .=	'<Memo>MtGox.com withdrawal</Memo>';
	$xml .=	'<Anonymous>false</Anonymous>';
	$xml .=	'</Transfer></TransferRequest>';
	
	
	
	//logMsg($xml);
	
	$url = "https://api.libertyreserve.com/xml/transfer.aspx?req=".urlencode($xml);
	
	$handler=curl_init($url);
	
	ob_start();
	curl_exec($handler);
	$content=ob_get_contents();
	ob_end_clean();
	curl_close($handler);
	
	
	
	$doc = new DOMDocument();	
	if(!$doc->loadXML($content)) {
		logMsg("Can't parse: $content");
		return(0);
	}
	
	$nodes = $doc->getElementsByTagName("TransferResponse");
	if($nodes->length)
	{
		$rootElem = $nodes->item(0);
		$responseId = $rootElem->getAttribute("id");
		
		if ($responseId != $id) {
			logMsg("IDs don't match: $content");
			return(0);
		}		
		
		$counter = 0;
		
		$errors = $rootElem->getElementsByTagName("Error");
		if($errors->length)
		{
			logMsg("Withdraw failed: $account , $amount\r\n $content");
			
			$codes = $errors->item(0)->getElementsByTagName("Code");
			if($codes->length) 
			{
				$errorCode=$codes->item(0)->nodeValue;
				if($errorCode=="301") return(1);
				if($errorCode=="402") return(2);
				
				
				return(3);
			}
		}
	}
			
	return(0);
}


//////	

?>