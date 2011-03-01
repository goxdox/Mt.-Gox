<?php
// send them LR



	$request = new TransferRequest($apiName, $securityWord);
	$request->addTransfersFromText($payerAcct, $transferList, $transferListError);	
	
	$transferListError = str_replace("\n","<br />", $transferListError);
	if ($transferListError != "") {
		$wasError = true;
	}

?>