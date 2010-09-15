<script language="javascript" type="text/javascript" src="/js/jquery.jqplot.js"></script>
<script type="text/javascript" src="/js/plugins/jqplot.dateAxisRenderer.min.js"></script>
<script type="text/javascript" src="/js/plugins/jqplot.highlighter.js"></script>

<script>
$(function () {
	$.post("/code/getHistory.php", { }, onRecent , "json" );
});

function onRecent(result)
{
	if(result.plot)
	{
		var data=result.plot.data;
		var minTime=data[0][0];
		var maxTime=data[data.length-1][0];
		
		var plot = $.jqplot('plot',[ data ], { 
			 title:'Price over last 48 hours',
			highlighter: {
				show: true,
			     tooltipAxes: 'y',
		      showMarker:true
		  },
			axes:{ yaxis:{tickOptions:{formatString:'$%.3f'}},
				xaxis:{
				min: minTime,
				max: maxTime,
            renderer:$.jqplot.DateAxisRenderer, 
            tickOptions:{formatString:'%H:%M:%S'},
            tickInterval:'8 hour'
        	}}

 			} );
	}
	$('#error').text(result.error);
	$('#status').text(result.status);
}

</script>

<fieldset>
 <legend>How it Works</legend>
 <div id="plot" style="width:500px;height:300px;float:right;margin-left:20px;" ></div>
Mt Gox is an exchange. It allows you to trade US Dollars (USD) for Bitcoins (BTC) or Bitcoins for US Dollars with other Mt. Gox users. You set the price you want to buy or sell your BTC for.<p>
Bitcoins are a new decentralized internet commodity. <a href="http://www.bitcoin.org/" target="_blank">Learn more about Bitcoins here.</a><p>
<h2>Why Use Mt. Gox?</h2>
<ul>
<li>Always available, 24 hours a day.</li>
<li>Fully automated.</li>
<li>Easy to Use.</li>
<li>Fast and Efficient.</li>
<li>Pretty Charts :)</li>
</ul>
</fieldset>

<fieldset>
 <legend>4 Easy Steps</legend>
<ul>
<li>1. Make an Account</li>
<li>2. Add some funds</li>
<li>3. Buy or Sell Bitcoins</li>
<li>4. Withdraw your converted funds</li>
</ul>
</fieldset>


 
<h2>Contact us!</h2>

We are very interested in any suggestions, bug reports, or general feedback!<br>
<script>document.write('<A href="mailto:' + 'info' + '@' + 'mt' + 'gox.com' + '">' + 'info' + '@' + 'mt' + 'gox.com' + '</a>')</script> send us mail!

