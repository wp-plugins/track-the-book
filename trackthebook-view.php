<?php
if (!class_exists("TrackTheBookView")) {
	class TrackTheBookView {
		
		function render($view) {
			$page = dirname(__FILE__) . '/views/' . $view . '.php';
			$missing_page = dirname(__FILE__) . '/views/missing.php';
			
			if (is_file($page)) {
				require($page);
			}
			else {
				require($missing_page);
			}
		}
		
		function renderJSON ($params) {
			$newline = "\n";
			
			echo '{
	"response": {' . $newline;
			$total_params = count($params);
			$param_counter = 1;
			foreach ($params as $key => $value) {
				echo '"' . $key . '": "' . addslashes($value) . '"';
				if ($total_params > $param_counter) {
					echo ',';
				}
				echo $newline;
				
				$param_counter++;
			}
			echo '
	}
}';
		}
		
		/**
		 * Coverts [trackthebook] into a register link
		 */
		function shortcodeRegisterLink($atts, $content=null, $code="") {
			global $trackthebook;
			
			if (empty($content)) {
				$content = __('Register my book');
			}
			
			if ($trackthebook->checkGoogleAPIKey()) {
				$register_link = "<a class='thickbox' target='_blank' href='" . get_option('siteurl'). "?view=trackthebook_form&amp;width=560&amp;height=350'>" . $content . "</a>";
			}
			else {
				$register_link = __('You must specify a Google Maps API Key before you can use the Track The Book plugin!');
			}
			
			return $register_link;
		}
		
		/**
		 * Coverts [trackthebook_filters] into a display of filters
		 */
		function shortcodeFilters($atts, $content=null, $code="") {
			global $trackthebook;
			
			ob_start();
			$this->render('filter');
			$filters_code = ob_get_contents();
			ob_end_clean();
			
			return $filters_code;
		}

	}
}
?>