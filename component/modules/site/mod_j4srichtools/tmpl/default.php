<?php
defined('_JEXEC') or die();

?>
<script type="text/javascript">
window.addEvent('domready', function(){
	var test_url = 'http://www.google.com/webmasters/tools/richsnippets?url=' + encodeURIComponent(window.location);
	$('testLink').setProperty('href', test_url);
});
</script>

<div class="<?php echo $moduleclass_sfx?>">
	<p>Click on this link to automatically test the current page on Google Rich Snippets Testing Tool</p>
	<p>
		<a id="testLink" href="#" target="_blank">Test this page</a>
	</p>
</div>