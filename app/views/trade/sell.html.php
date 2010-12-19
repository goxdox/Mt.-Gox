<script type="text/javascript" language="javascript" src="/js/jquery.validate.js"></script>
<script type="text/javascript" language="javascript" src="/js/trade.js"></script>


<div id="status"></div>
<div id="error"></div>


<form id="sellForm" action="" >
<input type="hidden" name="dark" />
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
<i style="font-size: 11px" >Mt Gox charges a small fee (0.65%) for each trade.</i>
</fieldset>
</form>



