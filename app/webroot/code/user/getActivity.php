<?php
include('../../../noserve/config.inc');
include('../lib/functions.inc');
include('../lib/session.php');
include('../lib/common.inc');

// make sure:
// user is logged in 
if(isset($_SESSION['UserID']))
{
	$uid=(int)($_SESSION['UserID']);

	db_connect();
		
	/* Paging */
	$sLimit = "";
	if ( isset( $_GET['iDisplayStart'] ) )
	{
		$sLimit = "LIMIT ".mysql_real_escape_string( $_GET['iDisplayStart'] ).", ".
			mysql_real_escape_string( $_GET['iDisplayLength'] );
	}
	
	$sOrder = '';
	/* Ordering */
	if ( isset( $_GET['iSortCol_0'] ) )
	{
		$sOrder = "ORDER BY  ";
		for ( $i=0 ; $i<$_GET['iSortingCols']; $i++ )
		{
			$sOrder .= fnColumnToField($_GET['iSortCol_'.$i] )."
			 	".mysql_real_escape_string( $_GET['sSortDir_'.$i] ) .", ";
		}
		$sOrder = substr_replace( $sOrder, "", -2 );
	}
	
	
	$sql="SELECT SQL_CALC_FOUND_ROWS deltaBTC,deltaUSD,type,typeData,btc,usd,date FROM Activity where UserID=$uid $sOrder $sLimit";
	$rResult = mysql_query( $sql ) or die(mysql_error());
	
	$sQuery = "SELECT FOUND_ROWS()";
	$rResultFilterTotal = mysql_query( $sQuery) or die(mysql_error());
	$aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
	$iTotal= $iFilteredTotal = $aResultFilterTotal[0];
	
	
	$sOutput = '{';
	if(isset($_GET['sEcho'])) $sOutput .= '"sEcho": '.intval($_GET['sEcho']).', ';
	$sOutput .= '"iTotalRecords": '.$iTotal.', ';
	$sOutput .= '"iTotalDisplayRecords": '.$iFilteredTotal.', ';
	$sOutput .= '"aaData": [ ';
	while( $row = mysql_fetch_array( $rResult ) )
	{
		$type=$row['type'];
		$date=$row['date'];
		$typeData=$row['typeData'];
		$deltaBTC=$row['deltaBTC'];
		$deltaUSD=$row['deltaUSD'];
		$totalBTC=$row['btc'];
		$totalUSD=$row['usd'];
		
		$dateStr= date("m/d/y H:i",$date);
		$typeStr=getTypeStr($type);
		$descStr=makeDesc($type,$typeData,$deltaBTC,$deltaUSD);
		
		$sOutput .= "[";
		$sOutput .= '"'.addslashes($dateStr).'",';
		$sOutput .= '"'.addslashes($typeStr).'",';
		$sOutput .= '"'.addslashes($descStr).'",';
		$sOutput .= '"'.addslashes($deltaBTC/BASIS).'",';
		$sOutput .= '"'.addslashes($deltaUSD/BASIS).'",';
		$sOutput .= '"'.addslashes($totalBTC/BASIS).'",';
		$sOutput .= '"'.addslashes($totalUSD/BASIS).'"';
		$sOutput .= "],";
	}
	$sOutput = substr_replace( $sOutput, "", -1 );
	$sOutput .= '] }';
	
	echo $sOutput;
}
	
	
	// deltaBTC,deltaUSD,type,typeData,btc,usd,date
	function fnColumnToField( $i )
	{
		switch($i)
		{
			case 0: return("date");
			case 1: return("type");
			case 2: return("typeData");
			case 3: return("deltaBTC");
			case 4: return("deltaUSD");
			case 5: return("btc");
			case 6: return("usd");
		}
		return("date");
	}
	
//#Reason: 0- ? 1-Trade Sell, 2-Trade Buy, 3-Add BTC by sending, 4-Withdraw BTC, 5- Withdraw Paypal, 6- Add by Paypal, 7- Payment Process,
function getTypeStr($type)
{
	switch($type)
	{
		case 1: return('Sold BTC');
		case 2: return('Bought BTC');
		case 3: return('Add BTC');
		case 4: return('Withdraw BTC');
		case 5: return('Withdraw Paypal');
		case 6: return('Add Paypal');
		case 7: return('Payment Process');
		case 8: return('Account Claimed');
		case 9: return('Manual');
		case 10: return('Fund Transfer');
		case 11: return('Add LR');
	}
	return('????');
}

function makeDesc($type,$typeData,$deltaBTC,$deltaUSD)
{
	switch($type)
	{
		case 1:
			$amount=-$deltaBTC;
			$price=round($deltaUSD/$amount,4);
			$amount=round($amount/BASIS,2);
			return("$amount for $price");
			
		case 2:
			$amount=$deltaBTC;
			$price=-round($deltaUSD/$amount,4);
			$amount=round($amount/BASIS,2);
			return("$amount for $price");
	}
	return($typeData);
}
	
?>