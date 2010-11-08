<fieldset>
 <legend>Trade API</legend>
The Mt Gox trade API is simple and easy to use. It involes posting to the following URLs with the appropriate fields.
<p>
All these URLs return JSON results.
<table class="btcx_table" >
<thead><tr><th colspan=2>Fetching Public Data</th></tr></thead>
<tr><td>Ticker Data</td><td>http://mtgox.com/code/data/ticker.php</td></tr>
<tr><td>Market Depth</td><td>http://mtgox.com/code/data/getDepth.php</td></tr>
<tr><td>Recent Trades</td><td>http://mtgox.com/code/data/getTrades.php</td></tr>
</table>
<p>
The following take your Mt Gox username and password as parameters. They must be sent as a POST.
<table class="btcx_table" >
<thead><tr><th colspan=2>Placing Orders</th></tr></thead>
<tr><td>Get your current balance</td><td>https://mtgox.com/code/getFunds.php?name=blah&pass=blah</td></tr>
<tr><td>Place an order to Buy BTC</td><td>https://mtgox.com/code/buyBTC.php?name=blah&pass=blah&amount=#price=#</td></tr>
<tr><td>Place an order to Sell BTC</td><td>https://mtgox.com/code/sellBTC.php?name=blah&pass=blah&amount=#price=#</td></tr>
<tr><td>Fetch a list of your open Orders</td><td>https://mtgox.com/code/getOrders.php?name=blah&pass=blah</td></tr>
<tr><td>Cancel an order</td><td>https://mtgox.com/code/sellBTC.php?name=blah&pass=blah&oid=#type=#<br>oid: Order ID<br>type: 1 for sell order or 2 for buy order</td></tr>
</table>
<p>
That's it. Now write a bot!


</fieldset>
