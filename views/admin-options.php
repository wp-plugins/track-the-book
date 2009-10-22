<?php 
	if(isset($_POST['Submit'])) {
		//Form data sent
		$google_api_key = $_POST['google_api_key'];
		$randomize = $_POST['randomize'];
		
		$trackthebook_options = array();
		$trackthebook_options['google_api_key'] = $google_api_key;
		$trackthebook_options['randomize'] = $randomize;
		
		update_option('trackthebook_options', $trackthebook_options);
		?>
		<div class="updated"><p><strong><?php _e('Options saved.'); ?></strong></p></div>
		<?php
	} 
	
	//Normal page display
	$trackthebook_options = get_option('trackthebook_options');
	$google_api_key = $trackthebook_options['google_api_key'];
	$randomize = $trackthebook_options['randomize'];
?>
<div class="wrap"><?php    echo "<h2>" . __('Track The Book', 'trackthebook') . "</h2>"; ?>
<h3><?php _e('Instructions'); ?></h3>
<?php echo __('To add a "Register my book" link to any post or page, simply add [trackthebook]Register my book[/trackthebook]. A KML file is generated dynamically based on all of the registered locations, and can be accessed at ') . get_option('siteurl') . '/?view=trackthebook.kml'; ?>
<h3><?php _e('Settings'); ?></h3>
<?php 
$trackthebook = new TrackTheBook;
if (!$trackthebook->checkGoogleAPIKey())
{
	echo '<div class="error"><p>Before you can use Track The Book you must acquire a <a href="http://code.google.com/apis/maps/signup.html">Google API Key</a> for your blog - the plugin will not function without it!</p></div>';
}
?>
<form method="post">
<p><?php _e('Google API Key: '); ?><input type="text"
	name="google_api_key" value="<?php echo $google_api_key; ?>" size="50"></p>
<p><?php _e('Randomize Identical Coordinates: '); ?><input type="checkbox" name="randomize" value="1" 
	<?php if (!empty($randomize)) { echo 'checked="checked"'; } ?> /></p>


<p class="submit"><input type="submit" name="Submit"
	value="<?php _e('Update Options') ?>" /></p>
</form>
</div>
