<script type="text/javascript" language="javascript" src="/js/jquery.validate.js"></script>
<script type="text/javascript" language="javascript" src="/js/date.format.js"></script>
<script type="text/javascript" language="javascript" src="/js/trade.js"></script>
<script type="text/javascript" language="javascript" src="/js/jquery.dataTables.min.js"></script> 
<link rel="stylesheet" type="text/css" href="/css/jquery.dataTables.css" />


<h3>Welcome to the Bitcoin Exchange.</h3> 
Buy and Sell the internet commodity known as Bitcoins!<br>
<i style="font-size: 11px" >Mt Gox charges a small fee (0.65%) for each trade.</i>

<div id="status"></div>
<div id="error"></div>




<form id="buyForm" action="" >
<fieldset>
 <legend>Buy Bitcoins</legend>
<table class="btcx_table">
<tr><td>USD in your account</td><td class="usds">0</td><td><a href="/users/addFunds" >add more</a></td></tr>
<tr><td>Lowest Ask Price</td><td id="buyP" colspan=2>Loading...</td></tr>
<tr><td>Amount to Buy</td><td colspan=2><input type="text" name="buyAmount" id="buyAmount" onKeyUp="changeBuyAmount()" /></td></tr>
<tr><td>Price per coin in Dollars</td><td colspan=2><input type="text" name="buyPrice" id="buyPrice"  onKeyUp="changeBuyPrice()" /></td></tr>
<tr><td>Total</td><td colspan=2 id="buyCost">0</td></tr>
<tr><td colspan=3><input type="button" value="Buy Bitcoins" onClick="onBuy()"/></td></tr>
<tr><td colspan=3 ><i id="buyStatus">You can make a lower offer but it wont be filled until someone accepts it.</i></td></tr>
</table>
</fieldset>
</form>

<HR>
<form id="sellForm" action="" >
<fieldset>
 <legend>Sell Bitcoins</legend>
<table class="btcx_table">
<tr><td>Bitcoins in your account</td><td class="btcs">0</td><td><a href="/users/addFunds" >add more</a></td></tr>
<tr><td>Highest Bid Price</td><td id="sellP" colspan=2>Loading...</td></tr>
<tr><td>Amount to Sell</td><td colspan=2><input type="text" name="sellAmount" id="sellAmount" onKeyUp="changeSellAmount()" /></td></tr>
<tr><td>Price per coin in Dollars</td><td colspan=2><input type="text" name="sellPrice" id="sellPrice"  onKeyUp="changeSellPrice()" /></td></tr>
<tr><td>Total</td><td colspan=2> <span id="sellCost">0</span></td></tr>
<tr><td colspan=3><input type="button" value="Sell Bitcoins" onClick="onSell()"/></td></tr>
<tr><td colspan=3 ><i id="sellStatus">You can make a higher offer but it wont be filled until someone accepts it.</i></td></tr>
</table>
</fieldset>
</form>
<hr>

<fieldset>
 <legend>Your Open Orders</legend>
<table width="100%" class="sort_table" id="orders">
<thead><tr><th>Type</th><th>Amount</th><th>Price</th><th>Status</th><th>Total $</th><th>When Placed</th><th>Cancel</th></tr></thead>
</table>
</fieldset>
</form>
