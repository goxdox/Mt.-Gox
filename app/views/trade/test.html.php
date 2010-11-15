<script type="text/javascript" language="javascript" src="/js/jquery.ui.js"></script>
<script type="text/javascript" src="/js/protovis.js"></script>

<script>

$(document).ready(function(){
	
	$.post("/code/data/getDepth.php", { }, onDepth , "json" );
	
});

function onDepth(result)
{
	if(result.asks && result.bids)
	{
		updateDepth(result.asks,result.bids);
	}
	
}

</script>

<div id="ticker">Use Mt Gox to send or recieve money for free! <a href="/">learn more</a></div>
<div id="status"></div>
<div id="error"></div>
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
<script type="text/javascript" src="/js/chart.js"> </script>
<audio id="sound" src="/wav/notify.mp3" preload="auto">
</div>
