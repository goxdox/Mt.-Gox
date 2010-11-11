<script type="text/javascript" language="javascript" src="/js/date.format.js"></script>
<script type="text/javascript" language="javascript" src="/js/jquery.dataTables.min.js"></script> 
<link rel="stylesheet" type="text/css" href="/css/jquery.dataTables.css" />
<script type="text/javascript" language="javascript">

var gPayTable;

$(document).ready(function(){

if(!userID)
{
	$('#error').text('You have been logged out.');
}else
{
	$('#status').text('Loading...');
	$.post("/code/gateway/getPayments.php", {  }, onServer , "json" );
	gPayTable=$('#payments').dataTable({
		"bLengthChange": false,
		"bFilter": false,
		"bPaginate": false,
		"bAutoWidth": false 
		});
}

});

function getStatus(status)
{
	switch(status)
	{
	case "0": return('pending');
	case "1": return('complete');
	case "3": return('incorrect amouunt');
	}
	return('????');
}

function setPayments(data)
{
	gPayTable.fnClearTable();
	for(var n=0; n<data.length; n++)
	{
		var date=new Date(data[n].date*1000);
		var dateStr= dateFormat(date,"mm/dd/yy HH:MM");
		var statusStr=getStatus(data[n].status);
		
		gPayTable.fnAddData([ statusStr,data[n].amount,data[n].amountr,data[n].custom,dateStr]);
	}

}

function onServer(data)
{
	$('#error').text(data.error);
	
	if(data.status) $('#status').text(data.status);
	else $('#status').text('');

	if(data.payments) setPayments(data.payments);
}



</script>



<div id="status"></div>
<div id="error"></div>

<fieldset>
<a href="merch/about">About Merchant Services</a>

</fieldset>

<fieldset>
<legend>Payments to you</legend>
<table  width="100%" class="sort_table" id="payments" >
<thead><tr><th>Status</th><th>Amount</th><th>Amount Received</th><th>Custom</th><th>When</th></tr></thead>
<tf colspan=4 id="paging"></tf>
</table>
</fieldset>





