<script type="text/javascript" language="javascript">
$(document).ready(function(){
	$('.marginBalance').text(<?= $marginBalance ?>);
});
</script>

<?= $this->view()->render(array('element' => 'marginBar') ); ?>

<fieldset>
 <legend>How Margin Trading works</legend>
 
</fieldset>