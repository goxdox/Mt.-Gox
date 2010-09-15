<!doctype html>
<html>
<head>
	<?php echo $this->html->charset();?>
	<title>Mt Gox - Bitcoin Exchange <?=$title ?></title>
	<?php echo $this->html->style(array('lithium','btcx')); ?>
	<?php echo $this->html->link('Icon', null, array('type' => 'icon')); ?>
	<script type="text/javascript" src="/js/jquery.js"></script>
	<script type="text/javascript" language="javascript" src="/js/jquery.tmpl.js"></script>
	<script type="text/javascript" src="/js/btcx.js"></script>
	<script type="text/javascript">
	  var _gaq = _gaq || [];
	  _gaq.push(['_setAccount', 'UA-17500249-1']);
	  _gaq.push(['_trackPageview']);
	
	  (function() {
	    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	  })();
	</script>
</head>
<body class="app">

<?php
global $gUserID;
global $gUsd;
global $gBtc;
global $gUserName;
global $gMerchOn;

if($gUserID)
{
?>
<script> var userID=<?=$gUserID ?>; 

</script>

	<div class="header">
		<div class="top_row_logo" ><h1>Mt. Gox</h1></div>
		<div class="top_row_left">--- 24/7 <a href="http://www.bitcoin.org/" target="_blank">Bitcoin</a> Exchange</div>
		<div class="top_row_right"><a href="/code/logout.php">Logout</a></div>
		<div class="top_row_right">$<span class="usds"><?= $gUsd ?></span> USD</div>
		<div class="top_row_right"><span class="btcs"><?= $gBtc ?></span> BTC</div>
		<div class="top_row_right">Welcome <a href="/users/settings"><?= $gUserName ?></a></div>
	</div>
	<div class="menu">
	<table class="btcx_table">
        <tr><td><a href="/" >Trade</a></td></tr>
        <tr><td><a href="/trade/history">Trade Data</a></td></tr>
        <tr><td><a href="/users/trades">Account History</a></td></tr>
          <tr><td><a href="/users/addFunds" >Add Funds</a></td></tr>
        <tr><td><a href="/users/withdraw" >Withdraw Funds</a></td></tr>
        <tr><td><a href="/support">How it Works</a></td></tr>
        <?php if($gMerchOn){ echo("<tr><td><a href='/merch'>Merchant Services</a></td></tr>"); } ?>
	</table>
	

<?php }else{ ?>
<script> var userID=0; </script>
	<div class="header">
		<div class="top_row_logo" ><h1>Mt. Gox</h1></div>
		<div class="top_row_left">--- 24/7 <a href="http://www.bitcoin.org/" target="_blank">Bitcoin</a> Exchange</div>
		<div class="top_row_right"><a href="/users/login">Login</a></div>
		<div class="top_row_right">not logged in</div>
		
	</div>
	<div class="menu">
	<table class="btcx_table">
         <tr><td><a href="/users/register">Register</a></td></tr>
         <tr><td><a href="/users/login">Login</a></td></tr>
         <tr><td><a href="/support">How it Works</a></td></tr>
        <tr><td><a href="/trade/history">Trade Data</a></td></tr>
    </table>
    
<?php } ?>
	<div class="undermenu">
	Mt. Gox charges no fee and is free to use. Donate a few BTC to help keep it that way:
	<span style="font-size: 10px;">1EuMa4dhfCK8ikgQ4emB7geSBgWK2cEdBG</span>
	<p>We are very interested in any suggestions, bug reports, or general feedback!<br>
	<script>document.write('<A href="mailto:' + 'info' + '@' + 'mt' + 'gox.com' + '">' + 'info' + '@' + 'mt' + 'gox.com' + '</a>')</script> send us mail!
	</p>
	</div>
</div>
<div id="ticker">Sorry, you must have Javascript enabled to use Mt. Gox</div>
<div id="page">
		
        <?php echo $this->content(); ?>
        
      </div>
      
      <HR style="margin-top: 30px;">
      <div id="footer">
        <p>Copyright (c) 2010 mtgox.com. All rights reserved.</p>
      </div>
    </div>
    <script>btcx_setup();</script>
  </body>
</html>