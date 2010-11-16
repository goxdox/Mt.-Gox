
function btcx_setup()
{
	//setInterval("reloadTicker()", (60*1000) );
	$.post("/code/ticker.php", {  }, onTicker , "json" );
	
}

/*
var lastMouseMove=new Date();
function reloadTicker()
{
	var now=new Date();
	if(now.getTime()-lastMouseMove.getTime()> (60*1000))
	{
		$.post("/code/ticker.php", {  }, onTicker , "json" );
		lastTicker=new Date();
	}
}
*/

function onTicker(data)
{
	if(data.ticker)
	{
		data.ticker.spacer='<span class="spacer" />';
		//var tickerTemplate='#{div}Volume: #{vol}</div>#{div}Last Price: #{last}</div>#{div}High:#{high}</div>#{div}Low: #{low}</div>';
		var tickerTemplate='Last Price: #{last}#{spacer}High:#{high}#{spacer}Low: #{low}#{spacer}Volume: #{vol}';
		$('#ticker').html($.tmpl(tickerTemplate,data.ticker));
	}
}


function i4_round(number,places)
{
	places=Math.pow(10,places);
	return( Math.round(number*places)/places );
}

function i4_addCommas(nStr)
{
	nStr += '';
	var x = nStr.split('.');
	var x1 = x[0];
	var x2 = x.length > 1 ? '.' + x[1] : '';
	var rgx = /(\d+)(\d{3})/;
	while (rgx.test(x1)) {
		x1 = x1.replace(rgx, '$1' + ',' + '$2');
	}
	return x1 + x2;
}