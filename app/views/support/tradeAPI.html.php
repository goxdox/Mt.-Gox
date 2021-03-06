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
<tr><td>Get your current balance</td><td><b>https://mtgox.com/code/getFunds.php?name=blah&pass=blah</b></td></tr>
<tr><td>Place an order to Buy BTC</td><td><b>https://mtgox.com/code/buyBTC.php?name=blah&pass=blah&amount=#&price=#</b><br>returns a list of your open orders</td></tr>
<tr><td>Place an order to Sell BTC</td><td><b>https://mtgox.com/code/sellBTC.php?name=blah&pass=blah&amount=#&price=#</b><br>returns a list of your open orders</td></tr>
<tr><td>Fetch a list of your open Orders</td><td><b>https://mtgox.com/code/getOrders.php?name=blah&pass=blah</b><br>oid: Order ID<br>type: 1 for sell order or 2 for buy order<br>status: 1 for active, 2 for not enough funds</td></tr>
<tr><td>Cancel an order</td><td><b>https://mtgox.com/code/cancelOrder.php?name=blah&pass=blah&oid=#&type=#</b><br>oid: Order ID<br>type: 1 for sell order or 2 for buy order</td></tr>
<tr><td>Send BTC</td><td><b>https://mtgox.com/code/withdraw.php?name=blah&pass=blah&group1=BTC&btca=bitcoin_address_to_send_to&amount=#</b></td></tr>
</table>
<p>
That's it. Now write a bot!


</fieldset>
