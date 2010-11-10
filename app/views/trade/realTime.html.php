<script>

$(document).ready(function(){

	if( typeof(WebSocket) != "function" ) 
	{
		window.location ="/support/noWebSocket";
	}else
	{
		var ws = new WebSocket("ws://127.0.0.1:8080/connect");
	
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

var gLastData={ "ticker": {"last": 0, "highBuy": 0, "lowSell": 0}, 
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
			$('#lastCell').text(data.ticker.last);
			gLastData.ticker.last=data.ticker.last;
		}
		$('#bidCell').text(data.ticker.highBuy);
		$('#askCell').text(data.ticker.lowSell);
		if($('#beepTrade').attr('checked')) beep=true;
	}
	if(data.depth)
	{
		$('#bid1000Cell').text(data.depth.bid1000);
		$('#ask1000Cell').text(data.depth.ask1000);
		if($('#beepDepth').attr('checked')) beep=true;
	}
	if(data.volume)
	{
		$('#volCell').text(data.volume+" BTC");
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

<hr>
<table width="100%"  >
	<tr  align="center">
		<td><h2 style="margin-top: 0px;">Bid</h2><div class="bigText" id="bidCell"></div></td>
		<td><h2 style="margin-top: 0px;">Last</h2><div class="bigText" id="lastCell"></div></td>
		<td><h2 style="margin-top: 0px;">Ask</h2><div class="bigText" id="askCell"></div></td>
	</tr>
	<tr  align="center">
		<td><p><div id="bid1000Cell"></div></td>
		<td><p><div id="volCell"></div></td>
		<td><p><div id="ask1000Cell"></div></td>
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