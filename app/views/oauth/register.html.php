<script type="text/javascript" language="javascript">

function onYes()
{
}

function onNo()
{
}
</script>

<div id="ticker">Use Mt Gox to send or recieve money for free! <a href="/">learn more</a></div>
<div id="page">

<fieldset>
 <legend>Allow <b><?= $appName ?></b> Access</legend>
Would you like to allow <?= $appName ?> the ability to send funds on your behalf. Only do this if you trust <?= $appName ?>

<table class="btcx_table">
<tr>
<td><input type="button" value="Sure I trust them."  onclick="onYes()" /></td>
<td><input type="button" value="No, No, No!" onclick="onNo()" /></td>
</tr>
</table>
 
</fieldset>

</div>