<fieldset>
 <legend>Payment API</legend>
You can use the Mt Gox Payment API to easily send Bitcoins or USD to any other Mt Gox user. The payment will be instant.
<p>
<ul>
	<li>Enable Payment API in your <a href="/users/settings">settings</a></li>
	<li>Make a note of your merchant ID and token shown on your settings page.</li>
	<li>Post to https://mtgox.com/code/gateway/send.php with the following variables:
		<table class="btcx_table">
			<tr><td>merchID</td><td>Your Merchant ID.</td></tr>
			<tr><td>token</td><td>Your Token.</td></tr>
			<tr><td>item</td><td>Item description. Shows up in account history of receiver.</td></tr>
			<tr><td>receiver</td><td>Mt Gox username of the receiver.</td></tr>
			<tr><td>currency</td><td>Either the string USD or BTC.</td></tr>
			<tr><td>amount</td><td>Amount that you want to send.</td></tr>
		</table>
	</li>
</ul> 
<a href="/merch/examples/payAPI">PHP example code</a>
</fieldset>