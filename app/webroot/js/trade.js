var gBuyPrice=0;
var gSellPrice=0;
var isBuyPriceMod=false;
var isSellPriceMod=false;
var gLastPrice=0;
var gOrdersTable;

$(document).ready(function(){

	$("#buyForm").validate({
		  rules: {
			buyAmount: {
				number: true,
		      	required: true,
		      	min: 10
	    	},
	    	buyPrice: {
		      	required: true,
		      	min: 0
	    	}
		  }
		});


	$("#sellForm").validate({
		  rules: {
		sellAmount: {
			number: true,
	      	required: true,
	      	min: 10
    	},
    	sellPrice: {
	      	required: true,
	      	min: 0
    	}
	  }
	});
	
	gOrdersTable=$('#orders').dataTable({
		"bLengthChange": false,
		"bFilter": false,
		"bPaginate": false,
		"bAutoWidth": false,
		"aaSorting": [[ 2, "desc" ]]
		});
	
	
	
	if(userID)
	{
		$.post("/code/getOrders.php", {  }, onServer , "json" );
	}else
	{
		$('#status').text('Note: You must be logged in to trade.');
	}

	
	
});


function orderTypeStr(type,dark)
{
	if(type==1) 
	{
		if(dark==1) return("Selling Dark");
		if(dark==2) return("Selling Dark Only");
		return("Selling");
	}
	if(dark==1) return("Buying Dark");
	if(dark==2) return("Buying Dark Only");
	return("Buying");
}

function orderStatusStr(type)
{
	if(type==1) return("Active");
	return("Not enough funds");
}

function setOrders(data)
{
	gOrdersTable.fnClearTable();
	for(var n=0; n<data.length; n++)
	{
		var type=orderTypeStr(data[n].type,data[n].dark);
		var status=orderStatusStr(data[n].status);
		var date=new Date(data[n].date*1000);
		var dateStr= dateFormat(date,"mm/dd HH:MM");
		var total=data[n].price*data[n].amount;
		total=i4_round(total,2);
		// <tr><td>Type</td><td>Amount</td><td>Price</td><td>Status</td><td>When Placed</td><td>Cancel</td></tr>
		var cancelStr='<a onclick="cancelOrder('+data[n].oid+','+data[n].type+')">cancel</a>';
		gOrdersTable.fnAddData([type,data[n].amount,data[n].price,status,total,dateStr,cancelStr]);
	}
}




function changeBuyPrice()
{
	isBuyPriceMod=true;
	changeBuyAmount();
}

function changeBuyAmount()
{
	var total=$('#buyPrice').val()*$('#buyAmount').val();
	total=i4_round(total,2);
	$('#buyCost').text(total);
}

function changeSellPrice()
{
	isSellPriceMod=true;
	changeSellAmount();
}

function changeSellAmount()
{
	var total=$('#sellPrice').val()*$('#sellAmount').val();
	total=i4_round(total,2);
	$('#sellCost').text(total);
}

function onTicker(data)
{
	if(data.ticker)
	{
		gBuyPrice=data.ticker.buy;
		gSellPrice=data.ticker.sell;
		if(data.ticker.last) gLastPrice=data.ticker.last;
		else data.ticker.last=gLastPrice;
		//data.div='<div class="ticker_item">';
		data.ticker.spacer='<span class="spacer" />';
		//var tickerTemplate='#{div}Volume: #{vol}</div>#{div}Last Price: #{last}</div>#{div}High:#{high}</div>#{div}Low: #{low}</div>';
		var tickerTemplate='Last Price: #{last}#{spacer}High:#{high}#{spacer}Low: #{low}#{spacer}Volume: #{vol}';
		$('#ticker').html($.tmpl(tickerTemplate,data.ticker));
		if(! isBuyPriceMod)
		{
			var p=gSellPrice- (- .00001);
			p=i4_round(p,5);
			$('#buyPrice').val(p);
		}
		$('#buyP').text(gSellPrice);
		if(!isSellPriceMod)
		{
			var p=gBuyPrice-.00001;
			p=i4_round(p,5);
			$('#sellPrice').val(p);
		}
		$('#sellP').text(gBuyPrice);
	}
}

function onServer(data)
{
	onTicker(data);
	if(data.orders) setOrders(data.orders);
	if(data.error) $('#error').html(data.error);
	else $('#error').text("");
	if(data.status) $('#status').html(data.status);
	else $('#status').text("");
	
	if(data.usds) 
	{
		$('.usds').text(data.usds);
	}
	if(data.btcs)
	{
		$('.btcs').text(data.btcs);
	}
}

function onSellResp(data)
{
	onServer(data);
	printStatusAndTrades('#sellStatus',data);
}

function onBuyResp(data)
{
	onServer(data);
	printStatusAndTrades('#buyStatus',data);
}

function printStatusAndTrades(status,data)
{
	var str="";
	if(data.trades)
	{
		for(var n=0; n<data.trades.length; n++)
		{
			str = str + data.trades[n]+"<br>";
		}
	}
	if(data.status)	str = str+data.status;
	if(data.error)	str = str+'<font color=red>'+data.error+'</font>';
	
	$(status).html(str);
}


function cancelOrder(orderID,type)
{
	$('#status').text('Canceling Order...');
	$('#error').text('');
	$.post("/code/cancelOrder.php", { "oid": orderID, "type": type }, onServer , "json" );
}

function onSell()
{
	if(userID)
	{
		if($("#sellForm").valid())
		{
			var amount=$('#sellAmount').val();
			var price=$('#sellPrice').val();
			var dark=$('#sellDark').val();
			$('#sellStatus').text('Selling...');
			$('#error').text('');
			$.post("/code/sellBTC.php", { "dark": dark, "amount": amount , "price": price }, onSellResp , "json" );
		}
	}else
	{
		$('#error').text('You must be logged in to trade.');
	}
}


function onBuy()
{
	if(userID)
	{
		if($("#buyForm").valid())
		{
			var amount=$('#buyAmount').val();
			var price=$('#buyPrice').val();
			var dark=$('#buyDark').val();
	
			$('#buyStatus').text('Buying...');
			$('#error').text('');
			$.post("/code/buyBTC.php", { "dark": dark, "amount": amount , "price": price }, onBuyResp , "json" );
		}
	}else
	{
		$('#error').text('You must be logged in to trade.');
	}
}