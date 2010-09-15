
<script>

var widgetStr;

if(userID)
{
	widgetStr = "<script src='http://mtgox.com/js/BTCButton.js' ><"+"/script>\r\n";
	widgetStr += '<script>\r\n';
	widgetStr += 'var mtgoxOptions = {\r\n';
	widgetStr += '		merchID: "'+<?=$gUserID ?>+'",\r\n';
	widgetStr += '		amount: "10.23",\r\n';
	widgetStr += '		custom: "enter your custom data here"\r\n';
	widgetStr += '};\r\n';
	widgetStr += '<'+'/script>\r\n';
	widgetStr += '<a style="cursor:pointer;" onClick="mtgox_pay(this)" >Pay using Bitcoins!</a>\r\n';
}else
{
	widgetStr="You must be logged in to get your Payment Widget";
}

$(document).ready(function(){
	$("#code").text(widgetStr);
  });
  
</script>
<fieldset>
<legend>Your BTC Payment Widget</legend>
Simply embed the following code in your webpage to start accepting BTC payments. Change the value of <b>amount</b> and <b>custom</b> to fit your needs.
<p>
<div class="code_box" id="code">



</div>

Please someone let me know if you make a nice "Pay with Bitcoins" button image.
</fieldset>