<script>

function confirm()
{
	$.post("/code/gateway/customerConfirm.php", { "orderID": $orderID }, onServer , "json" );
}

function onServer(data)
{
	$('#error').text(data.error);
	$('#status').text(data.status);
}

</script>
<h2>Confirm Order</h2>
<div id="status"></div>
<div id="error"><?php echo($error); ?></div>

Do you really want to send <?=$amount ?> BTC to <?=$merchName ?> ?
<input type="button" value="Confirm Payment" onClick="confirm()" ></input>
