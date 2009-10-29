<?php

if (!class_exists("TrackTheBookAdmin")) {
	class TrackTheBookAdmin {
		
		/**
		 * Render the settings page
		 */
		function adminOptions() {
			TrackTheBookView::render('admin-options');
		}
		
		/**
		 * Add a Track The Book link under the settings menu
		 */
		function adminMenu() {
			add_options_page('Track The Book Settings', 'Track The Book', 8, 'trackthebook.php', array($this,'adminOptions'));
		}
		
		/**
		 * Add the settings link on the plugins page
		 */
		function pluginShortcuts($links, $file) {
			
			if ( $file == TTB_BASENAME )
			{
				$links[] = '<a href="options-general.php?page=trackthebook.php">' . __('Settings') . '</a>';
			}

			return $links;
		}
		
		/**
		 * Render the dashboard stats widget
		 */
		function dashboard() {
			TrackTheBookView::render('dashboard');
		}
		
		/**
		 * Add widgets to the dashboard
		 */
		function addDashboardWidgets() {
			wp_add_dashboard_widget('ttb_dashboard', 'Track The Book', array($this,'dashboard'));
		}
	}
}

?>