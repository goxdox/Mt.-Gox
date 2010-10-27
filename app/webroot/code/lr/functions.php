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

<TransferRequest id='12881784272832'><Auth>
<ApiName>MtGox.com</ApiName>
<Token>889ECD0DD1C95F3C129A14FA83CACDA44863358D809BDBB964E9A7008A15956A</Token>
</Auth>
<Transfer>
<TransferType>transfer</TransferType></Transfer></TransferRequest><Anonymous>false</Anonymous><TransferRequest id='12881784272832'><Auth><ApiName>MtGox.com</ApiName><Token>889ECD0DD1C95F3C129A14FA83CACDA44863358D809BDBB964E9A7008A15956A</Token></Auth><Transfer><TransferType>transfer</TransferType></Transfer></TransferRequest><Memo>MtGox.com withdrawal</Memo><TransferRequest id='12881784272832'><Auth><ApiName>MtGox.com</ApiName><Token>889ECD0DD1C95F3C129A14FA83CACDA44863358D809BDBB964E9A7008A15956A</Token></Auth><Transfer><TransferType>transfer</TransferType></Transfer></TransferRequest><Anonymous>false</Anonymous><TransferRequest id='12881784272832'><Auth><ApiName>MtGox.com</ApiName><Token>889ECD0DD1C95F3C129A14FA83CACDA44863358D809BDBB964E9A7008A15956A</Token></Auth><Transfer><TransferType>transfer</TransferType></Transfer></TransferRequest><Amount>10.32</Amount><TransferRequest id='12881784272832'><Auth><ApiName>MtGox.com</ApiName><Token>889ECD0DD1C95F3C129A14FA83CACDA44863358D809BDBB964E9A7008A15956A</Token></Auth><Transfer><TransferType>transfer</TransferType></Transfer></TransferRequest><Anonymous>false</Anonymous><TransferRequest id='12881784272832'><Auth><ApiName>MtGox.com</ApiName><Token>889ECD0DD1C95F3C129A14FA83CACDA44863358D809BDBB964E9A7008A15956A</Token></Auth><Transfer><TransferType>transfer</TransferType></Transfer></TransferRequest><Memo>MtGox.com withdrawal</Memo><TransferRequest id='12881784272832'><Auth><ApiName>MtGox.com</ApiName><Token>889ECD0DD1C95F3C129A14FA83CACDA44863358D809BDBB964E9A7008A15956A</Token></Auth><Transfer><TransferType>transfer</TransferType></Transfer></TransferRequest><Anonymous>false</Anonymous><TransferRequest id='12881784272832'><Auth><ApiName>MtGox.com</ApiName><Token>889ECD0DD1C95F3C129A14FA83CACDA44863358D809BDBB964E9A7008A15956A</Token></Auth><Transfer><TransferType>transfer</TransferType></Transfer></TransferRequest><CurrencyId>LRUSD</CurrencyId><TransferRequest id='12881784272832'><Auth><ApiName>MtGox.com</ApiName><Token>889ECD0DD1C95F3C129A14FA83CACDA44863358D809BDBB964E9A7008A15956A</Token></Auth><Transfer><TransferType>transfer</TransferType></Transfer></TransferRequest><Anonymous>false</Anonymous><TransferRequest id='12881784272832'><Auth><ApiName>MtGox.com</ApiName><Token>889ECD0DD1C95F3C129A14FA83CACDA44863358D809BDBB964E9A7008A15956A</Token></Auth><Transfer><TransferType>transfer</TransferType></Transfer></TransferRequest>

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
	$xml .=		"<Payee>$account</Payee>";
	$xml .=		"<CurrencyId>LRUSD</CurrencyId>";
	$xml .=		"<Amount>$amount</Amount>";
	$xml .=		"<Memo>MtGox.com withdrawal</Memo>";
	$xml .=		"<Anonymous>false</Anonymous>";
	$xml .=	"</Transfer></TransferRequest>";
	
	
	
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


	

class ApiError 
{
	var $code;
	var $text;
	var $transfer;
	
	function ApiError($code, $text) {
		$this->code = $code;
		$this->text = $text;
	}
}

class Transfer {
	var $payerAcct;	
	var $payeeAcct;
	var $amount;
	var $memo = "";
	var $isPrivate = false;
	
	function Transfer($payerAcct, $payeeAcct, $amount, $memo = '') {

		$this->payerAcct = $payerAcct;
		$this->payeeAcct = $payeeAcct;
		$this->amount = $amount;		
		$this->memo = $memo;
		$this->isPrivate = false;
	}
	
	function toXml() {
		
		return 
		"<Transfer>".
			"<TransferType>transfer</TransferType>".
		  "<Payer>".$this->payerAcct."</Payer>".
			"<Payee>".$this->payeeAcct."</Payee>".
			"<CurrencyId>LRUSD</CurrencyId>".
			"<Amount>".$this->amount."</Amount>".
			"<Memo>".$this->memo."</Memo>".
			"<Anonymous>".($this->isPrivate ? "true" : "false")."</Anonymous>".
		"</Transfer>";

	}
}

class TransferReceipt {
	var $id = "";
	var $amount = 0;
	var $fee = 0;
	var $closingBalance = 0;
	
	var $transfer;
	
	function TransferReceipt($transfer) {
		$this->transfer = $transfer;
	}
}

class LRAPIRequest {
	var $id = "";

	var $apiName;
	var $authToken;
	
	var $transfers = array();
	
	function LRAPIRequest($apiName, $secWord) {
		$this->id = $this->generateId();
		$this->apiName = $apiName;
		$this->authToken = $this->createAuthToken($secWord);
	}
	
	function generateId() {
		return time().rand(0,9999);
	}
		
	function createAuthToken($secWord) {
		$datePart = gmdate("Ymd");
		$timePart = gmdate("H");	
		
		$authString = $secWord.":".$datePart.":".$timePart;
		
		//echo "<p>AuthString: ".$authString."</p>";  
		
		$sha256 = bin2hex(mhash(MHASH_SHA256, $authString));
		
		return strtoupper($sha256);
	}	
	
	function toXml() 
	{
		$authPart = 
			"<Auth><ApiName>".$this->apiName."</ApiName><Token>".$this->authToken."</Token></Auth>";
			
		$transfersPart = "";
		
		$tranfersCount = count($this->transfers);
		
		for($i = 0; $i < $tranfersCount; $i++) {
			$transfersPart .= $this->transfers[$i]->toXml();
		}
		
		return "<TransferRequest id=\"".$this->id."\">".$authPart.$transfersPart."</TransferRequest>";		
	}
	
	function validateTransaction($txn_id, &$errors)
	{
	}
	
	function addTransfersFromText($payerAcct, $transferList, &$errors) 
	{
		$lines = explode("\n", $transferList);
		
		for ($i = 0; $i < count($lines); $i++) {
		
			$line = $lines[$i];
			if (trim($line) == "") {
				continue;
			}
		
			$parts = explode(",", $line);
			
			if (count($parts) >=3  && 
					isValidAccountNumber(trim($parts[0])) && 
					is_numeric(trim($parts[1])) && 
					(trim($parts[2]) == "private" || trim($parts[2]) == "not-private")) {
			
				$trans = new Transfer($payerAcct, trim($parts[0]), trim($parts[1]));
				$trans->isPrivate = trim($parts[2]) == "private" ? true : false;
				if (count($parts) == 4) {
					$trans->memo = trim($parts[3]);
				}
				else if (count($parts) > 4) {
					$trans->memo = trim($parts[3]);
				
					for ($pi = 4; $pi < count($parts); $pi++) {
						$trans->memo .=  ",".$parts[$pi];
					}
				}
				
				$this->transfers[] = $trans;
			}
			else {
				$errors .= "Error at line ".($i + 1)."\n";
			}
		}
	}	
	
	function parseResponse($responseXml, &$errors) {
		$ver = explode(".", phpversion());

		
		require("functions.parseResponse.php5.php");
		
		
		return TransferRequest_parseResponse($this, $responseXml, $errors);
	}
	
	function getResponse() 
	{
		$url = "https://api.libertyreserve.com/xml/transfer.aspx?req=".urlencode($this->toXml());

		if(!function_exists('curl_init')) {
			die("Curl library not installed.");
			return "";
		}
		
		$handler=curl_init($url);
		
		ob_start();
		curl_exec($handler);
		$content=ob_get_contents();
		ob_end_clean();
		curl_close($handler);
		
		return $content;
	}
	
	function execute() {
		
		$content = $this->getResponse();
		if (trim($content) == "") {
			die("No response was received from the server.");
		}
		return $this->parseResponse($content);
	}
		
}
	

?>