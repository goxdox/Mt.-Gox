<script type="text/javascript" language="javascript" src="/js/jquery.validate.js"></script>
<script type="text/javascript" language="javascript" src="/js/date.format.js"></script>
<script type="text/javascript" language="javascript" src="/js/trade.js"></script>
<script type="text/javascript" language="javascript" src="/js/jquery.dataTables.min.js"></script> 
<link rel="stylesheet" type="text/css" href="/css/jquery.dataTables.css" />




<div id="status"></div>
<div id="error"></div>




<form id="buyForm" action="" >
<input type="hidden" name="dark" value="0" />

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
<i style="font-size: 11px" >Mt Gox charges a small fee (0.65%) for each trade.</i>
</fieldset>
</form>
