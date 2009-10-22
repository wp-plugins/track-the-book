<style type="text/css" media="screen">
.trackthebook h2 {
	margin-top: 5px;
}
.trackthebook span {
	margin: 0;
	font-style: italic;
	font-size: smaller;
	white-space:nowrap;
}
.trackthebook p.required {
	margin: 0;
	font-size: smaller;
}
.trackthebook span.highlight {
	color: #FF0000;
}
.trackthebook label.error, .trackthebook #location_error {
	background-color: #ffebe8;
	border-color: #c00;
	-moz-border-radius-bottomleft:3px;
	-moz-border-radius-bottomright:3px;
	-moz-border-radius-topleft:3px;
	-moz-border-radius-topright:3px;
	border-style:solid;
	border-width:1px;
	margin:0 10px;
	padding:4px;
	font-weight: bold;
}
.trackthebook #location_error {
	display: none;
	font-weight: normal;
}
.trackthebook #location_note {
	background-color: #ffffe0;
	border-color: #e6db55;
	-moz-border-radius-bottomleft:3px;
	-moz-border-radius-bottomright:3px;
	-moz-border-radius-topleft:3px;
	-moz-border-radius-topright:3px;
	border-style:solid;
	border-width:1px;
	padding:4px;
	display: none;
	font-weight: normal;
}
.trackthebook input#close_window {
	margin-left: 10px;
}
.trackthebook #notification {
	display: none;
}
</style>
<div class="wrap trackthebook"><?php    echo "<h2>" . __('Register My Book') . "</h2>"; ?>
<br />
<div id="location_note"></div>
<div id="notification"></div>
<form id="trackthebook_register" method="post">
<p class="required"><span class="highlight">*</span> denotes a required field</p>
<input type="hidden" name="action" id="action" value="register_my_book" />
<input type="hidden" name="coords" id="coords" value="" />
<input type="hidden" name="simplified_location" id="simplified_location" value="" />
<table>
	<tr>
		<td><?php _e('Book Number'); ?> <span class="highlight">*</span></td>
		<td><input type="text" name="book" size="5" class="required"> <span><?php _e('(The red number on the front of your book)'); ?></span>
		</td>
	</tr>
	<tr>
		<td><?php _e('Your Location'); ?> <span class="highlight">*</span></td>
		<td><input type="text" name="location" id="location" size="25"
			class="required"> <span><?php _e('(ex. Columbia, MD or 21044)'); ?></span></td>
	</tr>
</table>
<div id="location_error"></div>
<p><?php _e('The following information will not be displayed to the public.'); ?></p>
<table>
	<tr>
		<td><?php _e('Name'); ?></td>
		<td><input type="text" name="name" size="25"></td>
	</tr>
	<tr>
		<td><?php _e('Email'); ?></td>
		<td><input type="text" name="email" size="25"></td>
	</tr>
	<tr>
		<td><?php _e('School'); ?></td>
		<td><input type="text" name="school" size="25"></td>
	</tr>
	<tr>
		<td><?php _e('Grade'); ?></td>
		<td><input type="text" name="grade" size="25"></td>
	</tr>
</table>
<p><input type="submit" name="Submit"
	value="<?php _e('Register') ?>" />
	 
	<input type="button" name="Cancel" id="close_window"
	value="<?php _e('Cancel') ?>" />
</p>
</form>
<script type="text/javascript">
jQuery(document).ready(function(){
	var validator = jQuery("#trackthebook_register").validate({
			submitHandler: function(form) {
				checkLocation();
								
				return false;
			}
		});

	function checkLocation () {
		var location = jQuery("input#location").val();
		jQuery.getJSON("<?php echo get_option('siteurl'); ?>", { action: "get_location", location: location}, 
			function(data){
				var response = data.response;
				
				if (response.status == "200") {
					jQuery("div#location_error").hide();
					jQuery("input#coords").val(response.coords);
					jQuery("input#simplified_location").val(response.simplified_location);
					
					// The input looks good, lets submit it!
					registerBook();

					return true;
				}
				else {
					jQuery("div#location_error").show().html(response.message);
					jQuery("input#coords").val('');
					jQuery("input#simplified_location").val('');

					return false;
				}
			});
	}

	function registerBook () {
		// Get form fields
		var form_data = jQuery("#trackthebook_register").formSerialize();
		
		jQuery.getJSON("<?php echo get_option('siteurl'); ?>", form_data, 
			function(data){
				var response = data.response;
				
				if (response.status == "200") {
					jQuery("div#notification").show().html(response.message);
					jQuery("#trackthebook_register").hide();

					// Notify user if location has been changed for them
					if (jQuery("input#simplified_location").val().toLowerCase() != jQuery("input#location").val().toLowerCase()) {
						jQuery("div#location_note").show().html('<?php _e('In order to protect your privacy and for consistency, your location has been changed to '); ?>' + jQuery("input#simplified_location").val());
					}
					
					// Hijack the close buttons, so that they will refresh the page
					jQuery("#TB_closeWindowButton,#TB_overlay,#TB_ImageOff").unbind("click");
					jQuery("#TB_closeWindowButton,#TB_overlay,#TB_ImageOff,#TTB_close").bind("click", function() {
						closeWindow();
						window.location.reload(1);
						});
	
					return true;
				}
				else {
					jQuery("div#notification").show().html(response.message);

					return false;
				}
			});
	}

	function closeWindow() {
		if (typeof tb_remove == 'function') {
			tb_remove();
		}
		else if (typeof TB_remove == 'function') {
			TB_remove(); 
		}
	}

	jQuery("input#close_window").bind("click",closeWindow);
	jQuery("input#location").bind("keyup", function() { if (jQuery("div#location_error").is(":visible")) {jQuery("div#location_error").hide();} } );
});
</script>

</div>
