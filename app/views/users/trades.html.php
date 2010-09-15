<script type="text/javascript" language="javascript" src="/js/date.format.js"></script>
<script type="text/javascript" language="javascript" src="/js/jquery.dataTables.min.js"></script> 
<link rel="stylesheet" type="text/css" href="/css/jquery.dataTables.css" />
<script type="text/javascript" language="javascript">

var gHistoryTable;

$(document).ready(function(){

if(!userID)
{
	$('#error').text('You have been logged out.');
}else
{
	$('#status').text('Loading...');
	$.post("/code/user/getHistory.php", {  }, onServer , "json" );
	gHistoryTable=$('#history').dataTable({
		"bLengthChange": false,
		"iDisplayLength": 30,
		"aaSorting": [[ 0, "desc" ]],
		"bFilter": false,
		"bAutoWidth": false,
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "/code/user/getActivity.php",
		"sPaginationType": "full_numbers"
		});
	
}

});

//#Reason: 0- ? 1-Trade Sell, 2-Trade Buy, 3-Add BTC by sending, 4-Withdraw BTC, 5- Withdraw Paypal, 6- Add by Paypal, 7- Payment Process,
//#Reason: 0- ? 1-Trade, 2- Payment Process, 3-Add BTC by sending, 4-Withdraw BTC, 5- Withdraw Paypal, 6- Add by Paypal
function getTypeStr(type)
{
	switch(type)
	{
		case 1: return('Sold BTC');
		case 2: return('Bought BTC');
		case 3: return('Add BTC');
		case 4: return('Withdraw BTC');
		case 5: return('Withdraw Paypal');
		case 6: return('Add Paypal');
		case 7: return('Payment Process');
	}
	return('????');
}

function makeDesc(data)
{
	switch(data.type)
	{
		case 1:
			var amount=-data.deltaBTC;
			var price=i4_round(data.deltaUSD/amount,4);
			return(''+amount+' for '+price);
			
		case 2:
			var amount=data.deltaBTC;
			var price=-i4_round(data.deltaUSD/amount,4);
			return(''+amount+' for '+price);
	}
	return(data.typeData);
}

function setHistory(data)
{
	gHistoryTable.fnClearTable();
	for(var n=0; n<data.length; n++)
	{
		var date=new Date(data[n].date*1000);
		var dateStr= dateFormat(date,"mm/dd/yy HH:MM");
		var total=i4_round(data[n].price*data[n].amount,2);
		var typeStr=getTypeStr(data[n].type);
		var descStr=makeDesc(data[n]);
		
		gHistoryTable.fnAddData([dateStr,typeStr,descStr, data[n].deltaBTC, data[n].deltaUSD, data[n].btc, data[n].usd]);
	}
	
}


function onServer(data)
{
	$('#error').text(data.error);
	
	if(data.status) $('#status').text(data.status);
	else $('#status').text('');

	if(data.history) setHistory(data.history);
}



</script>


<div id="status"></div>
<div id="error"></div>

<fieldset>
<legend>Account History</legend>
<table  width="100%" class="sort_table" id="history" >
<thead><tr><th>When</th><th>Type</th><th>Description</th><th>Delta BTC</th><th>Delta USD</th><th>Total BTC</th><th>Total USD</th></tr></thead>
<tbody></tbody>
<tf colspan=4 id="paging"></tf>
</table>
</fieldset>





