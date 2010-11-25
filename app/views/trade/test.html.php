<script type="text/javascript" language="javascript" src="/js/jquery.ui.js"></script>
<script type="text/javascript" src="/js/protovis.js"></script>
<script type="text/javascript" src="/js/date.format.js"></script>

<script>

$(document).ready(function(){
	
	$.post("/code/data/getDepth.php", { }, onDepth , "json" );

	fetchPrice();
	
});

function fetchPrice()
{
	$.post("/code/data/getHistory.php", $("#timeScale").serialize() , onHistory , "json" );
}

function onDepth(result)
{
	if(result.asks && result.bids)
	{
		updateDepth(result.asks,result.bids);
	}
	
}

function onHistory(result)
{
	if(result.plot)
	{
		updateHistory(result);
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
<hr>
<div id="page">
<h1>Mega Chart!</h1>
<table width="100%" ><tr><td>
<form id="timeScale">
<select name="timeScale" onchange="fetchPrice()" >
<option value="0">Real Time</option>
<option value="5">5 min</option>
<option value="15">15 min</option>
<option value="30"  selected>30 min</option>
<option value="60">1 hour</option>
<option value="1440">1 day</option>
</select>
</form>
</td><td>
<span class="spacer"></span>
<input id="showVolume" type="checkbox" onchange="changeOptions()" checked /> Show Volume
<span class="spacer"></span>
<input id="showDepth" type="checkbox" onchange="changeOptions()" checked /> Show Depth
<span class="spacer"></span>
<input id="showOrders" type="checkbox" onchange="changeOptions()" checked /> Show Orders
<span class="spacer"></span>
<input id="showPrice" type="checkbox" onchange="changeOptions()" checked /> Show Price line
<span class="spacer"></span>
<input id="showCandles" type="checkbox" onchange="changeOptions()" checked /> Show Candles
</td></tr></table>
<div id="megaChart" class="megaChart" >.
<script type="text/javascript" src="/js/chart.js"> </script>
</div>
</div>