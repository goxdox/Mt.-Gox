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
		<div class="top_row_logo" ><a href="/" ><h1>Mt. Gox</h1></a></div>
		<div class="top_row_left">--- 24/7 <a href="http://www.bitcoin.org/" target="_blank">Bitcoin</a> Exchange</div>
		<div class="top_row_right"><a href="/code/logout.php">Logout</a></div>
		<div class="top_row_right">$<span class="usds"><?= $gUsd ?></span> USD</div>
		<div class="top_row_right"><span class="btcs"><?= $gBtc ?></span> BTC</div>
		<div class="top_row_right">Welcome <a href="/users/settings"><?= $gUserName ?></a></div>
	</div>
	
<?php }else{ ?>
<script> var userID=0; </script>
	<div class="header">
		<div class="top_row_logo" ><a href="/" ><h1>Mt. Gox</h1></a></div>
		<div class="top_row_left">--- 24/7 <a href="http://www.bitcoin.org/" target="_blank">Bitcoin</a> Exchange</div>
		<div class="top_row_right"><a href="/users/login">Login</a></div>
		<div class="top_row_right">not logged in</div>
	</div>
	
    
<?php } ?>
<div id="ticker">Mt. Gox charges no fee and is free to use. </div>
<div id="page">
		
        <?php echo $this->content(); ?>
        
      </div>
      
      <HR style="margin-top: 30px;">
      <div id="footer">
        <p>Copyright (c) 2010 mtgox.com. All rights reserved.</p>
      </div>
    </div>
  </body>
</html>