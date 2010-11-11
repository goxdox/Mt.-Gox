
<fieldset>
<legend>Example Checkout Form</legend>
Here is an example of offering payment by Mt Gox on your site.
<div class="code_box" ><xmp>
<form action="https://mtgox.com/merch/checkout" method="post" >
	<input type="hidden" name="notify_url"		value="http://yourdomain.com/ipn.php">
	<input type="hidden" name="business"		value="Your MtGox Username">
	<input type="hidden" name="currency_code"	value="USD">
	<input type="hidden" name="item_name"		value="Your Item Name">		
	<input type="hidden" name="custom"		value="your custom msg to yourself" >
	<input type="hidden" name="amount"		value="10.30">
	<input type="hidden" name="return"		value="http://yourdomain.com/thanks">
	 
	<input type="submit" value="Pay with Mt Gox"  />
</form>
</xmp>
</div> 
This simply posts to our checkout page.
Parameters:
<ul>
	<li>notify_url - (optional) where Mt Gox will call back on a successful transaction</li>
	<li>business - your Mt Gox user name</li>
	<li>currency_code - USD or BTC</li>
	<li>amount - Amount of transaction</li>
	<li>item_name - String displayed to user on checkout and in their Account history</li>
	<li>custom - Custom string only sent to your notify_url</li>
	<li>return - URL user is directed to after confirming the transaction</li>
</ul>
You will see all checkout activity in your Account History and on your <a href="/merch">Merchant Page</a>.<p>
If you want instant notification anytime a checkout happens you can enable <a href="/examples/ipn">Payment Notifications</a>.
</fieldset>