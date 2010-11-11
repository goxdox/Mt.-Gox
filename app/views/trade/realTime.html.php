<script type="text/javascript" language="javascript" src="/js/jquery.ui.js"></script>
<script>

$(document).ready(function(){

	$("#go").click(function(){
		  $("#lastCell").flash();
		});

	$.fn.flash = function() {
	    var highlightBg = "#FFFF9C";
	    var ogColor = "#000000";
	    var ogSize  = this.css("font-size");
	    var ogBack  = "#FFFFFF"; //this.css("background-color");
	    this.stop().css("background-color", "#FF0000").css("color", "#FF0000").animate({color: ogColor, backgroundColor: ogBack}, 1500);
	};

	if( typeof(WebSocket) != "function" ) 
	{
		window.location ="/support/noWebSocket";
	}else
	{
		var ws = new WebSocket("ws://mtgox.com:8080/connect");
	
		ws.onopen = function() {
			//alert("sending");
		    ws.send("subscribe");
		};
		ws.onmessage = function(event) {
			//alert(event.data);
			
			var data = eval('(' + event.data + ')');
		    onServer(data);
		};
	}
	
});

var gLastData={ "ticker": {"last": 0, "buy": 0, "sell": 0}, 
				"depth": {"bid1000" : 0, "ask1000" : 0}, "volume" : 0,
				"usds" : 0, "btcs" : 0 };

function onServer(data)
{
	//alert(data.ticker);
	
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
	if(data.depth)
	{
		if(data.depth.bid1000 != gLastData.depth.bid1000)
		{
			$('#bid1000Cell').text(data.depth.bid1000).flash();
			if($('#beepDepth').attr('checked')) beep=true;
		}
		if(data.depth.ask1000 != gLastData.depth.ask1000)
		{
			$('#ask1000Cell').text(data.depth.ask1000).flash();
			if($('#beepDepth').attr('checked')) beep=true;
		}
		

		gLastData.depth=data.depth;
	}
	if(data.volume)
	{
		if(gLastData.volume != data.volume)
		{
			$('#volCell').text(data.volume+" BTC").flash();
			if($('#beepTrade').attr('checked')) beep=true;
			gLastData.volume=data.volume;
		}
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

</script>

<div id="ticker">Use Mt Gox to send or recieve money for free! <a href="/">learn more</a></div>
<button id="go">&raquo; Run</button>
<hr>
<table width="100%"  >
	<tr  align="center">
		<td width="33%"><h2 style="margin-top: 0px;">Bid</h2><div class="bigText" id="bidCell"></div></td>
		<td  width="33%"><h2 style="margin-top: 0px;">Last</h2><div class="bigText" id="lastCell"></div></td>
		<td  width="33%"><h2 style="margin-top: 0px;">Ask</h2><div class="bigText" id="askCell"></div></td>
	</tr>
	<tr  align="center">
		<td  width="33%"><p><div id="bid1000Cell"></div></td>
		<td width="33%"><p><div id="volCell"></div></td>
		<td width="33%"><p><div id="ask1000Cell"></div></td>
	</tr>
</table>
<hr>
<p>
<div id="page">
<table>
	<tr><td><input type="checkbox" id="beepMoney" /></td><td>Beep on Balance Change</td></tr>
	<tr><td><input type="checkbox" id="beepTrade" /></td><td>Beep on Trade</td></tr>
	<tr><td><input type="checkbox" id="beepDepth" /></td><td>Beep on Depth Change</td></tr>
</table>
</div>
<p>
<p>
<audio id="sound" src="/wav/notify.mp3" preload="auto">