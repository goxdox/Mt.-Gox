<?php 
include('/code/lib/functions.inc');
include('/code/lr/functions.php');

//preg_match('/\U/', "U8227430");

echo( isValidAccountNumber("U8227430") );
echo( isValidAccountNumber("U0764959</Payee>") );
die();
//require_once('/code/nusoap/lib/nusoap.php');
// Create the client instance

$url = 'http://127.0.0.1/wsdl/finbank.wsdl';
//$url= 'http://mssoapinterop.org/asmx/simple.asmx?WSDL';
$client = new SoapClient($url, array('trace' => 1));
echo("<br>\n");
var_dump($client->__getFunctions());
echo("<br>\n");

// Request Header
$requestHeader['Timestamp']=date("c",time());
$requestHeader['Language']="EN";
$requestHeader['RequestId']=generateRandomString(6);
$requestHeader['SenderId']='1000034728';
$requestHeader['ReceiverId']='OKOYFIHH';

$appRequest['CustomerId']='1000034728';
$appRequest['Timestamp']=date("c",time());


try{
	$p1=new SoapParam($requestHeader, "RequestHeader");
	$p2=new SoapParam($appRequest, "ApplicationRequest");
$result = $client->getUserInfo(array('RequestHeader' => $requestHeader, 'ApplicationRequest' => $appRequest));
}catch(Exception $ex){ echo("exception".$ex->getMessage()); }
// Call the SOAP method
//$result = $client->call('hello', array('name' => 'Scott'));
// Display the result
//echo(print_r($result));

echo($result);


echo("\nDumping request headers:\n" .$client->__getLastRequestHeaders());

  echo("\nDumping request:\n".$client->__getLastRequest());

   echo("\nDumping response headers:\n"
      .$client->__getLastResponseHeaders());

   echo("\nDumping response:\n".$client->__getLastResponse());



echo("done");


/*
<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://bxd.fi/CorporateFileService">
<SOAP-ENV:Body><ns1:getUserInfoin/>
</SOAP-ENV:Body>
</SOAP-ENV:Envelope>

<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://model.bxd.fi" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:ns2="http://bxd.fi/CorporateFileService">
<SOAP-ENV:Body>
<ns2:getUserInfoin><ns1:RequestHeader><ns1:SenderId>1000034728</ns1:SenderId><ns1:RequestId>OkQHnF</ns1:RequestId><ns1:Timestamp>2010-12-07T10:28:56-03:00</ns1:Timestamp><ns1:Language>EN</ns1:Language><ns1:UserAgent xsi:nil="true"/><ns1:ReceiverId>OKOYFIHH</ns1:ReceiverId></ns1:RequestHeader><ns1:ApplicationRequest>QXJyYXk=</ns1:ApplicationRequest></ns2:getUserInfoin></SOAP-ENV:Body></SOAP-ENV:Envelope>



<env:Envelope xmlns:env="http://schemas.xmlsoap.org/soap/envelope/">
<env:Header>
<wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-
wss-wssecurity-secext-1.0.xsd" env:mustUnderstand="1">
<wsse:BinarySecurityToken wsu:Id="bst_Zkt4E6PpC4aTK272"
xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-
1.0.xsd" ValueType="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-x509-tokenprofile-
1.0#X509v3" EncodingType="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wsssoap-
message-security-1.0#Base64Binary">MIIDF...(most Base64 removed for
clarity)...8Xx60=</wsse:BinarySecurityToken>
<dsig:Signature xmlns:dsig="http://www.w3.org/2000/09/xmldsig#">
<dsig:SignedInfo>
<dsig:CanonicalizationMethod
Algorithm="http://www.w3.org/2001/10/xml-exc-c14n#"/>
<dsig:SignatureMethod
Algorithm="http://www.w3.org/2000/09/xmldsig#rsa-sha1"/>
<dsig:Reference URI="#Body_SJqHqDhuSvW7UkFo">
<dsig:Transforms>
<dsig:Transform
Algorithm="http://www.w3.org/2001/10/xml-exc-c14n#">
<exc14n:InclusiveNamespaces
xmlns:exc14n="http://www.w3.org/2001/10/xml-exc-c14n#" PrefixList=""/>
</dsig:Transform>
</dsig:Transforms>
<dsig:DigestMethod
Algorithm="http://www.w3.org/2000/09/xmldsig#sha1"/>
<dsig:DigestValue>4yzYxO6f0W9wu4YkQf4zayxTiLs=</dsig:DigestValue>
</dsig:Reference>
</dsig:SignedInfo>
<dsig:SignatureValue>PBVGxh7x2kzFYnkrL15zMqtLa5RHuqvRVEFcIbQzaivGnjJJTE3fOozbAb3st1ZHT
jwCykX/ZWP+NPNe9KvtaB959Jve3zUZbnrA1Deyg7GNAQQaDfbnGxW6uooyQOp+xwOsoIqDBVp83nigdKfsEhOKt6EWm
Mug+Ovw6V8Cxvk=</dsig:SignatureValue>
<dsig:KeyInfo>
<wsse:SecurityTokenReference xmlns:wsse="http://docs.oasisopen.
org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd"
xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-
1.0.xsd" wsu:Id="str_Pqf55eQFnl5t1jOT">
<wsse:Reference URI="#bst_Zkt4E6PpC4aTK272"
ValueType="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-x509-token-profile-
1.0#X509v3"/>
</wsse:SecurityTokenReference>
</dsig:KeyInfo>
</dsig:Signature>
<wsu:Timestamp xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-
200401-wss-wssecurity-utility-1.0.xsd">
<wsu:Created>2008-04-14T10:07:31Z</wsu:Created>
<wsu:Expires>2008-04-14T10:08:31Z</wsu:Expires>
</wsu:Timestamp>
</wsse:Security>
</env:Header>
<env:Body wsu:Id="Body_SJqHqDhuSvW7UkFo" xmlns:wsu="http://docs.oasisopen.
org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
<cor:uploadFilein xmlns:cor="http://bxd.fi/CorporateFileService">
<java:RequestHeader xmlns:java="java:fi.bxd.model">
<java:SenderId>2457785447</java:SenderId>
<java:RequestId>1234567</java:RequestId>
<java:Timestamp>2008-04-14T13:07:26.371+00:00</java:Timestamp>
<java:Language>FI</java:Language>
<java:UserAgent>TestClient 1.00</java:UserAgent>
<java:ReceiverId>BANKCODE</java:ReceiverId>
</java:RequestHeader>
<java:ApplicationRequest xmlns:java="java:fi.bxd.model">PD94b... (most
Base64 removed for clarity)...lc3Q+</java:ApplicationRequest>
</cor:uploadFilein>
</env:Body>
</env:Envelope>
*/
?>