<?php 
include('../../../noserve/config.inc');
include('../lib/functions.inc');
include('../lib/common.inc');
require('soap-wsse.php');

//require_once('/code/nusoap/lib/nusoap.php');
// Create the client instance






$fp = fopen(FINBANK_PRIVATEKEY, "r");
$priv_key = fread($fp, 8192);
fclose($fp);
$pkeyid = openssl_get_privatekey($priv_key);

$url = 'http://127.0.0.1/wsdl/finbank.wsdl';
//$url = 'https://wsk.op.fi/wsdl/MaksuliikeCertService.xml';
//$url= 'http://mssoapinterop.org/asmx/simple.asmx?WSDL';


$TIME_STAMP=date("c",time());
$CUSTOMER_ID='1000034728';
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


class mySoap extends SoapClient {

   function __doRequest($request, $location, $saction, $version) {
    $doc = new DOMDocument('1.0');
    $doc->loadXML($request);

    $objWSSE = new WSSESoap($doc);

    $objWSSE->addUserToken("YOUR_USERNAME_HERE", "YOUR_PASSWORD_HERE", TRUE);

    return parent::__doRequest($objWSSE->saveXML(), $location, $saction, $version);
   }
}

$client = new mySoap($url, array('trace' => 1));
echo("<br>\n");
var_dump($client->__getFunctions());
echo("<br>\n");


// Request Header
$requestHeader['Timestamp']=$TIME_STAMP;
$requestHeader['Language']="EN";
$requestHeader['RequestId']=generateRandomString(6);
$requestHeader['SenderId']=$CUSTOMER_ID;
$requestHeader['ReceiverId']='OKOYFIHH';

$appRequest = '<?xml version="1.0" encoding="UTF-8"?>';
$appRequest .= '<ApplicationRequest xmlns="http://bxd.fi/xmldata/">';
$appRequest .= "<CustomerId>$CUSTOMER_ID</CustomerId>";
$appRequest .= "<Timestamp>$TIME_STAMP</Timestamp>";
$appRequest .= "<EnvironmentId>PRODUCTION</EnvironmentId>";
$appRequest .= "<SoftwareId>software 1.01</SoftwareId>";
$appRequest .= "<Compression>false</Compression>";
$appRequest .= "</ApplicationRequest>";

if(! openssl_sign($appRequest, $signature, $pkeyid)) die("Couldn't sign");

$appRequest = '<?xml version="1.0" encoding="UTF-8"?>';
$appRequest .= '<ApplicationRequest xmlns="http://bxd.fi/xmldata/">';
$appRequest .= "<CustomerId>$CUSTOMER_ID</CustomerId>";
$appRequest .= "<Timestamp>$TIME_STAMP</Timestamp>";
$appRequest .= "<EnvironmentId>PRODUCTION</EnvironmentId>";
$appRequest .= "<SoftwareId>software 1.01</SoftwareId>";
$appRequest .= "<Compression>false</Compression>";
$appRequest .= '<Signature xmlns="http://www.w3.org/2000/09/xmldsig#">';
//$appRequest .= '<SignedInfo>';
//$appRequest .= '<CanonicalizationMethod Algorithm="http://www.w3.org/TR/2001/REC-xml-c14n-20010315#WithComments"/>';
//$appRequest .= '<SignatureMethod Algorithm="http://www.w3.org/2000/09/xmldsig#rsa-sha1"/>';
//$appRequest .= '<Reference URI="#xpointer(/)">';
//$appRequest .= '<Transforms>';
//$appRequest .= '<Transform Algorithm="http://www.w3.org/2000/09/xmldsig#enveloped-signature"/>';
//$appRequest .= '</Transforms>';
//$appRequest .= '<DigestMethod Algorithm="http://www.w3.org/2000/09/xmldsig#sha1"/>';
//$appRequest .= '<DigestValue>5PsKL0uX0HMiXN4704l28qdK0NQ=</DigestValue>';
//$appRequest .= '</Reference>';
//$appRequest .= '</SignedInfo>';
$appRequest .= "<SignatureValue>$signature</SignatureValue>";
$appRequest .= '<KeyInfo>';
$appRequest .= '<X509Data>';
$appRequest .= "<X509Certificate>$CSR</X509Certificate>";
$appRequest .= '</X509Data>';
$appRequest .= '</KeyInfo>';
$appRequest .= '</Signature>';
$appRequest .= "</ApplicationRequest>";

//$appRequest['CustomerId']='1000034728';
//$appRequest['Timestamp']=date("c",time());


try{
	
	$result = $client->getUserInfo(array('RequestHeader' => $requestHeader, 'ApplicationRequest' => $appRequest));
}catch (SoapFault $fault) {
    print("Fault string: " . $fault->faultstring . "\n");
    print("Fault code: " . $fault->detail->WebServiceException->code . "\n");
}

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