<?php

if (!class_exists("TrackTheBookModel")) {
	class TrackTheBookModel {
		
		/**
		 * Constructor
		 */
		function TrackTheBookModel() {
			global $wpdb;
			
			$wpdb->trackthebookdb = $wpdb->prefix . 'trackthebook';
		}
		
		/**
		 * Install the table that will store each location
		 */
		function install () {
			global $wpdb;
		
			$table_name = $wpdb->trackthebookdb;
		   	if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
		      
		    	$sql = "CREATE TABLE " . $table_name . " (
			      	`id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
					`book` INT( 11 ) NOT NULL ,
					`name` VARCHAR( 255 ) NULL ,
					`location` VARCHAR( 255 ) NULL ,
					`coords` VARCHAR( 255 ) NULL ,
					`email` VARCHAR( 255 ) NULL ,
					`school` VARCHAR( 255 ) NULL ,
					`grade` VARCHAR( 255 ) NULL ,
					`ip_address` VARCHAR( 40 ) NULL ,
					`date_added` DATETIME NOT NULL ,
					PRIMARY KEY ( `id` )
			);";
		
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		    dbDelta($sql);
		 
			$trackthebook_options = array();
			$trackthebook_options['db_version'] = TTB_DB_VERSION;
			$trackthebook_options['randomize'] = 1; // Turn randomize identical coordinates on by default
			
			add_option('trackthebook_options', $trackthebook_options);
		   }
		}
		
		/**
		 * Inserts a new row into the trackthebook table
		 * @param $book
		 * @param $name
		 * @param $location
		 * @param $coords
		 * @param $email
		 * @param $school
		 */
		function insertBook($book, $name, $location, $coords, $email, $school, $grade) {
			global $wpdb;
			
			$table_name = $wpdb->trackthebookdb;
			
			// Sanitize book
			$clean_book = preg_replace("/[^0-9]/", "", $book); 
			
			// Get users IP address
			$trackthebook = new TrackTheBook;
			$ip_address = $trackthebook->getRealIPAddress();
			
			// Get today's date
			$now = date('Y-m-d H:i:s', time());
			
			$sql = "INSERT INTO " . $table_name . 
					" (book, name, location, coords, email, school, grade, ip_address, date_added)" .
					" VALUES ('" . $clean_book . "','" . $wpdb->escape($name) . "','" . $wpdb->escape($location) . "','" . $coords . "','" . $wpdb->escape($email) . "','" . $wpdb->escape($school) . "','" . $wpdb->escape($grade) . "','" .$ip_address ."','" .$now ."')";
			
			$wpdb->query($sql);
		}
		
		/**
		 * Gets each placemark to display in the KML file
		 * @param $filters
		 * @return array of placemark results
		 */
		function getPlacemarks($filters) {
			global $wpdb;
			
			$table_name = $wpdb->trackthebookdb;

			$sql = "SELECT id, book, location, coords, date_added from " . $table_name . " WHERE coords IS NOT NULL";
			
			if (!empty($filters['book'])) {
				$sql .= " and book = '" . $filters['book'] . "'";
			}
			
			return $wpdb->get_results($sql);			
		}
		
		/**
		 * Detect if coordinates are unique
		 * @param $coords
		 * @return boolean
		 */
		function uniqueCoords($coords) {
			global $wpdb;
			
			$table_name = $wpdb->trackthebookdb;

			$num_rows = $wpdb->get_var("SELECT count(id) from " . $table_name . " WHERE coords = '" . $coords . "'");
			
			if ($num_rows > 0) {
				return false;
			}
			else {
				return true;
			}		
		}
	}
}

?>