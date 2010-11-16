<script type="text/javascript" language="javascript" src="/js/jquery.ui.js"></script>
<script type="text/javascript" src="/js/protovis.js"></script>

<script>

$(document).ready(function(){
	
	$.post("/code/data/getDepth.php", { }, onDepth , "json" );
	
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
		updateHistory(result.plot);
	}
}

</script>

<div id="ticker">Use Mt Gox to send or recieve money for free! <a href="/">learn more</a></div>
<div id="status"></div>
<div id="error"></div>
<hr>
<form id="timeScale">
<select  name="timeScale" onchange="fetchPrice()" >
<option value="0"  selected>Real Time</option>
<option value="5">5 min</option>
<option value="15">15 min</option>
<option value="30">30 min</option>
<option value="60">1 hour</option>
<option value="1440">1 day</option>
</select>
</form>

<script type="text/javascript" src="/js/chart.js"> </script>

