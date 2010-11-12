<div id="sidebar">
<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar() ) : ?>
<div class="block">
Bit coin Market Anaylsis brought to you by
		<h3><a href="/">Mt Gox</a></h3>
		Bitcoin Exchange.
	</div>
	
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
		<h3>Meta</h3>
		<ul>
			<?php wp_register(); ?>
			<li><?php wp_loginout(); ?></li>
		</ul>
 

	</div>      <p></p> <p align="center">

</p>
		
<?php endif; ?>
</div>