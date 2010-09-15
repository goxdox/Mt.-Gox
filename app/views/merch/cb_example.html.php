<script>
$(function () {
	  $("#main").text(code);
});

var code= '<'+'?php\r\n';
code += '	$amount=$_POST["amount"];\r\n';
code += '	$txn_id=$_POST["txn_id"];\r\n';
code += '	$custom=$_POST["custom"];\r\n';
code += '\r\n	$ch = curl_init("http://mtgox.com/code/gateway/checkTxn.php");\r\n';
code += '	curl_setopt($ch, CURLOPT_POST      ,1);\r\n';
code += '	curl_setopt($ch, CURLOPT_POSTFIELDS    ,"?amount=$amount&txn_id=$txn_id&merchID=$merchID");\r\n';
code += '	curl_setopt($ch, CURLOPT_HEADER      ,0);\r\n';  
code += '	curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);\r\n';  
code += '	$reply = curl_exec($ch);\r\n';
code += '\r\n	if($reply=="ok")\r\n';
code += '	{\r\n';
code += '		// payment is valid. Do your business\r\n';
code += '	}else die("invalid");\r\n';
code += '?>\r\n';

</script>
<fieldset>
<legend>Payment Callback Example</legend>
If you are using the Payment Notification URL to get notified when a payment comes in you need to make a simple script for us to call. 
Well will post 3 things to that script:
<ul>
<li><b>amount</b> The number of BTC of the payment.</li>
<li><b>custom</b> The custom string you sent us from the widget options.</li>
<li><b>txn_id</b> A unique string so you can verify that we called your callback page.</li>
</ul>

To be safe you can verify that we are the ones that posted to your call back by doing either a GET or POST to:<br> 
<div class="code_box">http://mtgox.com/code/gateway/checkTxn.php?amount=&txn_id=&merchID=</div>  
<p>
Here is an example in php:<br></br>

<div class="code_box" id="main"></div>

</fieldset>