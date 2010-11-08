<fieldset>
 <legend>About Merchant Services</legend>
You can use the Mt Gox Checkout Button to easily accept Bitcoin or US Dollar payment on your website. It is quick and easy to setup. If your customer happens to have a Mt Gox account the payment will be instant. Otherwise it will take ~1 hour for the Bitcoin network to complete the transaction.
<p>
<ul>
	<li>Easy Setup</li>
	<li>Accept BTC or USD</li>
	<li>Some payments will be instant</li>
	<li>No need to integrate with or even run bitcoind</li>
	<li>Free to use, no transaction fee, no chargebacks!</li>
</ul> 

You can also easily send either USD or BTC to people through our <a href="merch/paymentAPI">payment API</a>.
</fieldset>

<fieldset>
<legend>Simple Setup</legend>
The quickest way to get started:
<ul>
	<li><a href="/users/settings">Turn on Merchant Services in Options</a></li>
	<li><a href="/merch/widget">Get your Widget Code.</a></li>
	<li>Paste it into your website.</li>
	<li>Fill out the amount option to set how many BTC the customer will be charged.</li>
	<li>Fill out the custom field however you want.</li>
	<li>Done!</li>
	<li><a href="/merch">Merchant Services</a> will list all payments you have received.</li>
</ul> 
</fieldset>

<fieldset>
 <legend>Advanced Callback Setup</legend>
 
This is a fully automated process where you are notified any time a user successfully sends you payment. 
This method requires you to set up a call back url that we hit to notify you there has been a transaction.
<p>
Here is how the flow works:
<ul>
	<li>User on your site is clicks a checkout button.</li>
	<li>User is taken to our checkout page where he can confirm the transaction.</li>
	<li>User is returned to your site.</li>
	<li>Mt Gox posts to your confirmation URL telling you the transaction was completed.</li>
	<li>(optional) For extra security your confirmation page can post back Mt Gox to make sure the confirmation post was from us.</li>
	<li>You have been paid and it is safe to ship the User whatever you are selling.</li>
</ul>

<table class="btcx_table">
<thead><tr><th colspan=3>Step by step guide</th></tr></thead>
<tr><td>1</td><td>Post to <b>https://mtgox.com/merch/checkout</b></td><td>
	Parameters:
	<ul>
	<li>notify_url - where Mt Gox will call back on a successful transaction</li>
	<li>business - your Mt Gox user name</li>
	<li>currency_code - USD or BTC</li>
	<li>amount - Amount of transaction</li>
	<li>item_name - String displayed to user on checkout and in their Account history</li>
	<li>custom - Custom string only sent to your notify_url</li>
	<li>return - URL user is directed to after confirming the transaction</li>
	</ul>
	</td></tr>
<tr><td>2</td><td>Handle calls to your <b>notify_url</b></td><td>
	Parameters sent by us:
	<ul>
		<li>txn_id - Transaction ID. Will be unique so you can make sure this isn't a duplicate post.</li>
		<li>payer_username - The customer's name on Mt Gox.</li>
		<li>currency_code - USD or BTC</li>
		<li>amount - Amount of transaction</li>
		<li>custom - Custom string you passed to mtgox.com/merch/checkout</li>
	</ul>
	</td></tr>
<tr><td>3</td><td>(optional) In your <b>notify_url</b> you can check that the transaction was legitament
	<p>Post to: <b>https://mtgox.com/code/gateway/checkTxn.php</b></td><td>
	Parameters:
	<ul>
		<li>txn_id - Transaction ID. Will be unique so you can make sure this isn't a duplicate post.</li>
		<li>merchID - Your merchant ID. (Shown in <a href="/users/settings">your settings</a>)</li>
		<li>amount - Amount of transaction</li>
	</ul>
	Returns either: <b>ok</b> or <b>invalid</b>
	</td></tr>
<tr><td>4</td><td><b>Ship it!</b></td><td>
	You have been paid and it is safe to ship the customer whatever you are selling.
	<p>
	<a href="/merch">Merchant Services</a> will list all payments you have received.
	</td></tr>
</table>

<a href="/merch/examples/checkout">Example Code</a>
<p>
Please email us if you have any problems or questions: <script>document.write('<A href="mailto:' + 'info' + '@' + 'mt' + 'gox.com' + '">' + 'info' + '@' + 'mt' + 'gox.com' + '</a>')</script>

</fieldset>


