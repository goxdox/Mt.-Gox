<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<head profile="http://gmpg.org/xfn/11">
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" inhalt="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
	
	<title><?php bloginfo('name'); ?> <?php if ( is_single() ) { ?> &raquo; Blog Archive <?php } ?> <?php wp_title(); ?></title>
	
	<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
	
	<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
	<!--[if IE]><link rel="stylesheet" type="text/css" href="<?php bloginfo('stylesheet_directory'); ?>/ie.css" media="screen" /><![endif]-->
	
	<?php wp_head(); ?>
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

<body>

<div id="huelle">

	<div id="kopf">
		<h1><span><a href="<?php echo get_option('home'); ?>/">
		  <?php bloginfo('name'); ?>
		</a>
	    <?php bloginfo('description'); ?></span></h1>
		<div id="search">
			<form method="get" id="searchform" action="<?php bloginfo('url'); ?>/">
				<input type="text" value="Search..." name="s" id="s" onfocus="if(this.value=='Search...')this.value=''" onblur="if(this.value=='')this.value='Search...'" />
				<input type="submit" id="searchsubmit" value="" />
			</form>
		</div>
	</div>

	<div id="navigation">
		<ul>
			<li <?php if(!is_page()) echo 'class="current_page_item"'; ?>><a href="<?php echo get_option('home'); ?>/">Home</a></li>
			<?php wp_list_pages('title_li='); ?>
		</ul>
	<a href="<?php bloginfo('rss2_url'); ?>" id="feed">Subscribe</a>	</div>
    <div class="middle"></div>
<div id="wrap">