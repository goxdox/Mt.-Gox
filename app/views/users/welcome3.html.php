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
			axes:{ yaxis:{tickOptions:{formatString:'%.3f'}},
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
<div class="info_menu" >
	<div class="info_box"><div class="info_title"><a href="http://www.bitcoin.org/" target="_blank">What are Bitcoins?</a></div>
 		Bitcoins are a decentralized internet commodity.<br><br> <a href="http://www.bitcoin.org/" target="_blank">Learn more about Bitcoins here.</a>
 	</div>
</div>

<fieldset class="info_frame" >
 <legend style="margin-left: 22px" >What is Mt Gox?</legend>
 <div class="info_box"><div class="info_title"><a href="support">Bitcoin Exchange</a></div>
 	Mt Gox is an exchange. It allows you to trade US Dollars (USD) for Bitcoins (BTC) or Bitcoins for US Dollars with other Mt Gox users. You set the price you want to buy or sell your BTC for.<br><br>Fully automated, always available, 24 hours a day.</div>
 <div class="info_box"><div class="info_title"><a href="merch/about">Merchant Services</a></div>
 	<ul><li>Take Bitcoin or USD payments quickly and easily.</li>
 		<li>Simple secure API.</li>
 	 <li>No need to run bitcoind.</li> 
 	 <li>Instant transactions.</li></ul>
 	 <a href="merch/about"><img class="center" src="/img/btclogo.png" /></a>
 </div>
 <div class="info_box"><div class="info_title"><a href="/users/sendMoney">Bitcoin Manager</a></div>
 	<ul>
 		<li>Send USD or BTC for free to anyone with an email address.</li>
 		<li>Store your Bitcoins</li>
 		<li>No need to download or run any software</li>
 	</ul>
 	<a href="/users/sendMoney"><img class="center" src="/img/loveBTC.png" /></a>
 </div>
 	
 <div class="info_box"><div class="info_title"><a href="support/advancedTrading">Advanced Trading</a></div>
 	<ul><li>Automate your trading with our <a href="support/tradeAPI">Trading API</a></li>
 	<li>Dark pools allow you to trade large quantities without moving the market. (coming soon)</li>
 	<li>Margin trading. (coming soon)</li></ul></div>
 	
 <div id="plot" class="info_graphic" ></div>
</fieldset>

<fieldset style="width: 790px;" >
 <legend >4 Easy Steps</legend>
<ul>
<li>1. <a href="/users/register">Make an Account</a></li>
<li>2. <a href="/users/addFunds">Add some funds</a></li>
<li>3. <a href="/trade">Buy or Sell Bitcoins</a></li>
<li>4. <a href="/users/withdraw">Withdraw your converted funds</a></li>
</ul>
<h2><a href="/users/register">Sign up!</a></h2>
</fieldset>

