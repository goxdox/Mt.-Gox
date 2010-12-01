<link rel="stylesheet" href="/css/ui-lightness/jquery.ui.css" type="text/css" />
<script type="text/javascript" language="javascript" src="/js/jquery.ui.js"></script>
<script type="text/javascript" src="/js/protovis.js"></script>
<script type="text/javascript" src="/js/date.format.js"></script>

<script>

$(document).ready(function(){


	$.fn.flash = function() {
	    var highlightBg = "#FFFF9C";
	    var ogColor = "#000000";
	    var ogSize  = this.css("font-size");
	    var ogBack  = "#FFFFFF"; //this.css("background-color");
	    this.stop().css("background-color", "#FF0000").css("color", "#FF0000").animate({color: ogColor, backgroundColor: ogBack}, 1500);
	};
	

	if( typeof(WebSocket) != "function" ) 
	{
		window.location ="/trade/megaChart";
	}else
	{
		gMegaChart.setShowCandles(false);
		connect();
		gMegaChart.init();
		vis.render();
	}
	
	
});

function connect()
{
	
	var ws = new WebSocket("ws://127.0.0.1:8080/connect");
	
	ws.onopen = function() {
		//alert("sending");
	    ws.send("subscribe");
	};
	ws.onmessage = function(event) {
		//$("#status").text(event.data);
		
		var data = eval('(' + event.data + ')');
	    onServer(data);
	};
	ws.onclose = function() {
		$('#error').html("Disconnected. Click <a onclick='connect()'>here to reconnect</a>.");
	};
	
}

var gLastData={ "ticker": {"last": 0, "buy": 0, "sell": 0}, 
				"usds" : 0, "btcs" : 0 };

function onServer(data)
{
	//alert(data);
	
	var beep=false;
	
	onTicker(data);
	if(data.ticker)
	{
		if(data.ticker.last != gLastData.ticker.last)
		{
			//$('#lastCell').text(data.ticker.last).animateHighlight("#FF0000", 1000);
			$('#lastCell').text(data.ticker.last);
			//$('#lastCell').animateHighlight("#FF0000", 1000);
			$("#lastCell").flash();
			if($('#beepTrade').attr('checked')) beep=true;
			
		}
		if(data.ticker.buy != gLastData.ticker.buy)
		{
			$('#bidCell').text(data.ticker.buy).flash();
		}
		if(data.ticker.sell != gLastData.ticker.sell)
		{	
			$('#askCell').text(data.ticker.sell).flash();
		}

		gLastData.ticker=data.ticker;
		
	}
	if(data.asks && data.bids)
	{
		updateDepth(data.asks,data.bids);
		if($('#beepDepth').attr('checked')) beep=true;
	}
	if(data.plot)
	{	
		gMegaChart.updateHistory(data);
	}
	
	if(data.usds) 
	{
		$('.usds').text(data.usds);
		if($('#beepMoney').attr('checked')) beep=true;
	}
	if(data.btcs)
	{
		$('.btcs').text(data.btcs);
		if($('#beepMoney').attr('checked')) beep=true;
	}
	
	if(beep)
	{
		//alert("beep");
		document.getElementById("sound").play();
	}	
}

function changeOptions()
{
	gMegaChart.setShowVolume( $("#showVolume").attr('checked') );
	gMegaChart.setShowDepth( $("#showDepth").attr('checked') );
	gMegaChart.setShowOrders( $("#showOrders").attr('checked') );
	gMegaChart.setShowPrice( $("#showPrice").attr('checked') );
	gMegaChart.setShowCandles( $("#showCandles").attr('checked') );

	
	vis.render();
}

</script>

<div id="ticker">Use Mt Gox to send or recieve money for free! <a href="/">learn more</a></div>
<div id="status"></div>
<div id="error"></div>

<div id="page">
<h1>Real Time Mega Chart!</h1>
<table width="100%" ><tr><td>
<span class="spacer"></span>
<input id="showVolume" type="checkbox" onchange="changeOptions()" checked /> Show Volume
<span class="spacer"></span>
<input id="showDepth" type="checkbox" onchange="changeOptions()" checked /> Show Depth
<span class="spacer"></span>
<input id="showOrders" type="checkbox" onchange="changeOptions()" checked /> Show Orders
<span class="spacer"></span>
<input id="showPrice" type="checkbox" onchange="changeOptions()" checked /> Show Price line
<span class="spacer"></span>
<input id="showCandles" type="checkbox" onchange="changeOptions()"  /> Show Candles
</td></tr></table>

<table width="100%" class="megaChart_table" >
<tr><td>.<div id="megaChart" class="megaChart">
<script type="text/javascript" src="/js/chart.js"> </script>
</div></td>
<td style="text-align: center" ><span id="highPriceMC">1</span><p>
	<div id="priceSlider" class="center"></div><br><span id="lowPriceMC">0</span></td></tr>
</table>

<table>
	<tr><td><input type="checkbox" id="beepMoney" /></td><td>Beep on Balance Change</td></tr>
	<tr><td><input type="checkbox" id="beepTrade" /></td><td>Beep on Trade</td></tr>
	<tr><td><input type="checkbox" id="beepDepth" /></td><td>Beep on Depth Change</td></tr>
</table>
</div>
<p>
<p>

<audio id="sound" src="/wav/notify.mp3" preload="auto">

</div>
