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

<script type="text/javascript" src="/js/chart.js"> </script>
</div>
