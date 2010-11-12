<div id="sidebar">
<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar() ) : ?>
<div class="block">
		<h3>Categories</h3>
		<ul>
			<?php wp_list_categories('orderby=name&show_count=0&title_li='); ?>
		</ul>
	</div>
	
	<div class="block">
		<h3>Archives</h3>
		<ul>
			<?php wp_get_archives('type=monthly'); ?>
		</ul>
	</div>
	
	<div class="block">
		<h3>Blogroll</h3>
		<ul>
			<?php wp_list_bookmarks('title_li=&categorize=0'); ?>
		</ul>
	</div>		
	
	<div class="block">
		<h3>Meta</h3>
		<ul>
			<?php wp_register(); ?>
			<li><?php wp_loginout(); ?></li>
			<li><a href="http://validator.w3.org/check/referer" title="This page validates as XHTML 1.0 Transitional">Valid <abbr title="eXtensible HyperText Markup Language">XHTML</abbr></a></li>
			<li><a href="http://gmpg.org/xfn/"><abbr title="XHTML Friends Network">XFN</abbr></a></li>
			<li><a href="http://wordpress.org/" title="Powered by WordPress, state-of-the-art semantic personal publishing platform.">WordPress</a></li>
			<?php wp_meta(); ?>
		</ul>
 

	</div>      <p></p> <p align="center">
<a href="http://jigsaw.w3.org/css-validator/">
    <img style="border:0;width:88px;height:31px"
        src="http://jigsaw.w3.org/css-validator/images/vcss-blue"
        alt="CSS ist valide!" />
</a>
</p>
		
<?php endif; ?>
</div>