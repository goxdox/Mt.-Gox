<?php 
include('../lib/functions.inc');
include('../lib/common.inc');
//require_once('/code/nusoap/lib/nusoap.php');
// Create the client instance

$url = 'http://127.0.0.1/wsdl/finbank.wsdl';
$url = 'https://wsk.op.fi/wsdl/MaksuliikeCertService.xml';
//$url= 'http://mssoapinterop.org/asmx/simple.asmx?WSDL';
$client = new SoapClient($url, array('trace' => 1));
echo("<br>\n");
var_dump($client->__getFunctions());
echo("<br>\n");

$TIME_STAMP=date("c",time());
$CUSTOMER_ID='1000034728';
$TRANSFER_KEY='5063633536709026';
$CSR  ='MIICojCCAYoCAQAwXTELMAkGA1UEBhMCRkkxEjAQBgNVBAgTCUJlcmtzaGlyZTEQ';
$CSR .='MA4GA1UEBxMHTmV3YnVyeTETMBEGA1UEChMKMTAwMDAzNDcyODETMBEGA1UEAxMK';
$CSR .='MTAwMDAzNDcyODCCASIwDQYJKoZIhvcNAQEBBQADggEPADCCAQoCggEBAMdl5ogZ';
$CSR .='tGeuugBJSV0bHF5j8vHI3fnb57R8C7uIYvjXtNw+05SKCuCpbUBdTvPS1c88zbZ6';
$CSR .='T7P3sDUeBcoJQS3MD9zAzZO28YxW5TgtZ5Jtk8UiphEBs6LMkMc0TV0sEyJDQEFy';
$CSR .='wmCTi3G/wOfQrrNj7HW7pDv3kSqaNSpBrMd7IzmIQyRWd31WQpshAYv75OiqNi4c';
$CSR .='NaOb2uyvblpAS5VW81Mnua1JDsT+A/dZLldBcNuHK885yeibdLQymlMDNLS0oOms';
$CSR .='utsCUH3FOHLyTxm5LFly/Gy1HjUCk/6EnS0BhKsRFpFdp7lNC3kdTQZ5xDmwKAhV';
$CSR .='09HXZmlWJgUAK90CAwEAAaAAMA0GCSqGSIb3DQEBBQUAA4IBAQCTVcVECDdjR54U';
$CSR .='cJPouU9mUuWCEW/ThhrWWGqGzYBK+bqF62L9VdhRjYyHPxFdtERHAtTRiSneuqla';
$CSR .='52biiaMFM+71lbLxkhEf5g+Eqcom8itYedcAp/b5uLfFtls0QKmAfcXprvf8yLxF';
$CSR .='znk8YNHQsb9RkX5gq8RCwW8xwsstVirNAEmbA8AtTVlgcK1ikOAqeb+mpToHPQkh';
$CSR .='8GwohvOqEj8Et87JFMTl0D97wI16DfeLiVYaKKsABBXm7k7pykuoVPjw4BPqyByW';
$CSR .='9+uQhIEvWskK7QVcmfgmp363Nt485FBQwN18FbRwoFhuSHZJ0Zc2NLcLkVO0wdXe';
$CSR .='GQbxP6Xq';

// Request Header
$requestHeader['Timestamp']=$TIME_STAMP;
$requestHeader['Language']="EN";
$requestHeader['RequestId']=generateRandomString(6);
$requestHeader['SenderId']=$CUSTOMER_ID;
$requestHeader['ReceiverId']='OKOYFIHH';

$appRequest = '<?xml version="1.0" encoding="UTF-8"?>';
$appRequest .= '<CertApplicationRequest xmlns="http://op.fi/mlp/xmldata/">';
$appRequest .= "<CustomerId>$CUSTOMER_ID</CustomerId>";
$appRequest .= "<Timestamp>$TIME_STAMP</Timestamp>";
$appRequest .= "<Environment>PRODUCTION</Environment>";
$appRequest .= "<SoftwareId>software 1.01</SoftwareId>";
$appRequest .= "<Compression>false</Compression>";
$appRequest .= "<Service>MATU</Service>";
$appRequest .= "<Content>$CSR</Content>";
$appRequest .= "<TransferKey>$TRANSFER_KEY</TransferKey>";
$appRequest .= "</CertApplicationRequest>";

//$appRequest['CustomerId']='1000034728';
//$appRequest['Timestamp']=date("c",time());


try{
	
	$result = $client->getCertificate(array('RequestHeader' => $requestHeader, 'ApplicationRequest' => $appRequest));
}catch(Exception $ex){ echo("exception".$ex->getMessage()); }
// Call the SOAP method
//$result = $client->call('hello', array('name' => 'Scott'));
// Display the result
//echo(print_r($result));
//logMsg($result);
//echo($result);


echo("\nDumping request headers:\n" .$client->__getLastRequestHeaders());

  echo("\nDumping request:\n".$client->__getLastRequest());

   echo("\nDumping response headers:\n"
      .$client->__getLastResponseHeaders());

   echo("\nDumping response:\n".$client->__getLastResponse());



//echo("done");


?>